<?php
/** Serialize media changes with package/vehicle publication. Call inside a transaction. */
function lock_image_owners(array $owners): array {
 $keys=[];foreach($owners as $owner)if($owner&&in_array($owner['owner_type'],['packages','vehicles'],true))$keys[$owner['owner_type'].':'.(int)$owner['owner_id']]=[$owner['owner_type'],(int)$owner['owner_id']];ksort($keys);
 foreach($keys as [$type,$id])one('SELECT id FROM '.$type.' WHERE id=? FOR UPDATE',[$id]);return array_values($keys);
}
function assert_listing_images(array $owners): void {
 foreach($owners as [$type,$id]){ $listing=one('SELECT status FROM '.$type.' WHERE id=?',[$id]);if($listing&&$listing['status']==='published'&&!(int)query("SELECT COUNT(*) FROM media WHERE owner_type=? AND owner_id=? AND status='published'",[$type,$id])->fetchColumn())throw new InvalidArgumentException('Add another approved published image, or save the listing as a draft, before removing its last published image.'); }
}
/** Package-scoped image management; never accepts an arbitrary media owner. */
function package_image_data(): array {
 $alt=trim((string)($_POST['alt_text']??''));$credit=trim((string)($_POST['credit']??''));$license=trim((string)($_POST['license_url']??''));$status=(string)($_POST['image_status']??'draft');
 $order=filter_var($_POST['sort_order']??0,FILTER_VALIDATE_INT,['options'=>['min_range'=>0,'max_range'=>100000]]);
 if($alt===''||strlen($alt)>255||strlen($credit)>255||strlen($license)>1000||$order===false||!in_array($status,['draft','published'],true))throw new InvalidArgumentException('Provide image description, valid order and status. Text must be within the displayed limits.');
 if($license!==''&&(!filter_var($license,FILTER_VALIDATE_URL)||!str_starts_with($license,'https://')))throw new InvalidArgumentException('Photo license link must use HTTPS.');
 if($status==='published'&&($credit===''||empty($_POST['photo_approved'])))throw new InvalidArgumentException('Record photo ownership/source and confirm approval before publishing the image.');
 return [$alt,$credit,$license,$order,$status];
}
function handle_package_images(array $package): void {
 $new=null;$old=null;
 try{
  check_csrf();$operation=(string)($_POST['operation']??'');
  if(!in_array($operation,['upload','update','delete','reorder'],true))throw new InvalidArgumentException('Invalid image action.');
  db()->beginTransaction();
  // Serialize changes for this package, including removal of its last cover image.
  $package=one('SELECT * FROM packages WHERE id=? FOR UPDATE',[$package['id']]);
  if($operation==='reorder'){
   $order=(string)($_POST['image_order']??'');
   if(strlen($order)>20000||!preg_match('/^[1-9][0-9]*(?:,[1-9][0-9]*)*$/D',$order))throw new InvalidArgumentException('Choose a valid image order.');
   $ids=array_map('intval',explode(',',$order));$actual=array_map('intval',array_column(rows("SELECT id FROM media WHERE owner_type='packages' AND owner_id=? FOR UPDATE",[$package['id']]),'id'));
   $sorted=$ids;sort($sorted);sort($actual);
   if(count(array_unique($ids))!==count($ids)||$sorted!==$actual)throw new InvalidArgumentException('The images have changed. Reload the package and arrange its current images.');
   foreach($ids as $position=>$imageId)query("UPDATE media SET sort_order=? WHERE id=? AND owner_type='packages' AND owner_id=?",[$position,$imageId,$package['id']]);
   db()->commit();flash('Image order saved. The first published image is the package cover.');redirect('admin/packages/edit/'.$package['id']);
  }
  $image=$operation==='upload'?null:one("SELECT * FROM media WHERE id=? AND owner_type='packages' AND owner_id=? FOR UPDATE",[(int)($_POST['image_id']??0),$package['id']]);
  if($operation!=='upload'&&!$image)throw new InvalidArgumentException('This image does not belong to the selected package.');
  $data=$operation==='delete'?null:package_image_data();
  if($operation==='delete'){query('DELETE FROM media WHERE id=?',[$image['id']]);$old=$image['filename'];}
  else{
   $new=upload_image();if($operation==='upload'&&!$new)throw new InvalidArgumentException('Choose a JPEG, PNG or WebP image.');
   if($image){query('UPDATE media SET alt_text=?,credit=?,license_url=?,sort_order=?,status=?,filename=? WHERE id=?',[...$data,$new??$image['filename'],$image['id']]);if($new)$old=$image['filename'];}
   else query("INSERT INTO media(owner_type,owner_id,alt_text,credit,license_url,sort_order,status,filename) VALUES('packages',?,?,?,?,?,?,?)",[$package['id'],...$data,$new]);
  }
  assert_listing_images([['packages',(int)$package['id']]]);db()->commit();if($old)delete_image_file($old);flash('Package image '.($operation==='delete'?'deleted':'saved').'. The first published image is the package cover.');redirect('admin/packages/edit/'.$package['id']);
 }catch(Throwable $ex){if(db()->inTransaction())db()->rollBack();if($new)delete_image_file($new);if($ex instanceof HttpError)throw $ex;$error=$ex instanceof InvalidArgumentException?$ex->getMessage():'Could not save the image. Please try again.';if(!$ex instanceof InvalidArgumentException)error_log((string)$ex);admin_head('Package images');admin_error($error);echo '<a class="button" href="'.e(url('admin/packages/edit/'.$package['id'])).'#package-images">Back to package images</a>';admin_end();}
}
function package_image_fields(?array $image=null,int $order=0): void {
 echo '<div class="form-grid"><label>Image description (alt text)<input name="alt_text" required maxlength="255" value="'.e($image['alt_text']??'').'"></label><label>Photo source / owner<input name="credit" maxlength="255" value="'.e($image['credit']??'').'"><small>Required for publication. Use the actual owner or licensed source.</small></label><label>License link (optional)<input type="url" name="license_url" maxlength="1000" value="'.e($image['license_url']??'').'"></label><label>Display order<input type="number" name="sort_order" min="0" max="100000" required value="'.e($image['sort_order']??$order).'"><small>Lower numbers appear first. First published image is the cover.</small></label><label>Image status<select name="image_status"><option value="draft">Draft</option><option value="published" '.(($image['status']??'')==='published'?'selected':'').'>Published</option></select></label><label>'.($image?'Replace image (optional)':'Choose package image').'<input data-photo-upload type="file" data-max-bytes="'.(upload_limit_mb()*1024*1024).'" name="image" accept="image/jpeg,image/png,image/webp" '.(!$image?'required':'').'><small>JPEG, PNG or WebP. Maximum '.upload_limit_mb().' MB / 16 megapixels.</small></label><label class="full approval"><input type="checkbox" name="photo_approved" value="1"> This photograph and its usage rights are business-approved for publication.</label></div>';
}
function package_images_panel(array $package): void {
 $images=rows("SELECT * FROM media WHERE owner_type='packages' AND owner_id=? ORDER BY sort_order,id",[$package['id']]);$endpoint=e(url('admin/packages/photos/'.$package['id']));
 echo '<section id="package-images" class="package-images"><div class="section-head"><div><h2>Package images</h2><p>Upload photos here. Published photos appear when the package is published; drafts stay private.</p></div><span>'.count($images).' images</span></div>';
 if($images){
  echo '<form class="photo-order-form" method="post" action="'.$endpoint.'">'.csrf_field().'<input type="hidden" name="operation" value="reorder"><input type="hidden" name="image_order" value="'.e(implode(',',array_column($images,'id'))).'"><h3>Choose your cover &amp; photo order</h3><p>Drag photos to arrange them, or use the arrow buttons on keyboard and touch. Save the order to apply it. Draft photos never appear publicly.</p><ol class="photo-sort-list" aria-label="Package photo order">';
  foreach($images as $image)echo '<li class="photo-sort-item" data-image-id="'.e($image['id']).'"><span class="photo-drag-handle" draggable="true" tabindex="0" role="button" aria-label="Drag '.e($image['alt_text']).'; use the adjacent arrow buttons to reorder">Grip · <span data-photo-position></span></span><img src="'.e(media_url($image['filename'])).'" alt="'.e($image['alt_text']).'" width="240" height="160" loading="lazy" draggable="false"><strong>'.e($image['alt_text']).'</strong><small>'.e(ucfirst($image['status'])).'<span data-cover-label></span></small><div class="photo-sort-actions"><button type="button" data-photo-move="-1" aria-label="Move '.e($image['alt_text']).' earlier">↑</button><button type="button" data-photo-move="1" aria-label="Move '.e($image['alt_text']).' later">↓</button>'.($image['status']==='published'?'<button type="button" data-photo-cover>Set cover</button>':'').'</div><input type="hidden" data-photo-published value="'.($image['status']==='published'?'1':'0').'"></li>';
  echo '</ol><p class="help" data-sort-announcement role="status" aria-live="polite">Use display order fields below if JavaScript is unavailable.</p><button class="button" data-save-order>Save image order</button></form>';
 }
 if($images)echo '<script src="'.e(asset('assets/package-order.js?v=20261009')).'" defer></script>';
 echo '<div class="package-photo-grid">';
 foreach($images as $image){echo '<article class="package-photo-card"><img class="package-photo-preview" src="'.e(media_url($image['filename'])).'" alt="'.e($image['alt_text']).'" loading="lazy"><form method="post" enctype="multipart/form-data" action="'.$endpoint.'">'.csrf_field().'<input type="hidden" name="operation" value="update"><input type="hidden" name="image_id" value="'.e($image['id']).'">';package_image_fields($image);echo '<button class="button">Save image</button></form><form class="delete-form" method="post" action="'.$endpoint.'" data-confirm="Delete this package image permanently?">'.csrf_field().'<input type="hidden" name="operation" value="delete"><input type="hidden" name="image_id" value="'.e($image['id']).'"><button class="button danger">Delete image</button></form></article>';}
 echo '</div><form class="package-photo-upload" method="post" enctype="multipart/form-data" action="'.$endpoint.'">'.csrf_field().'<input type="hidden" name="operation" value="upload"><h3>Add package image</h3><img class="package-photo-preview" data-new-photo-preview hidden alt="Selected image preview">';package_image_fields(null,$images?min(100000,max(array_column($images,'sort_order'))+1):0);echo '<button class="button">Upload image</button></form></section>';
}
