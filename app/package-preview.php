<?php
/** Called only after require_admin(); drafts are never exposed through public query parameters. */
function package_preview(?array $record,array $fields): void {
 header('X-Robots-Tag: noindex, nofollow, noarchive');header('Cache-Control: private, no-store');header('Referrer-Policy: no-referrer');
 $previewPackage=$record;
 if($_SERVER['REQUEST_METHOD']==='POST'){
  check_csrf();$status=$_POST['status']??'draft';$_POST['status']='draft';
  try{$data=validate_entity('packages',$fields);$previewPackage=array_merge($record??['id'=>0],$data);$previewPackage['status']=in_array($status,['draft','published'],true)?$status:'draft';}
  catch(InvalidArgumentException $ex){admin_head('Package preview');admin_error($ex->getMessage());echo '<p>Close this tab and adjust the package form. Preview does not save changes.</p>';admin_end();return;}
  finally{$_POST['status']=$status;}
 }
 if(!$previewPackage)throw new HttpError('Save or fill in a package before previewing it.',404);
 // The editor fields must not leak into the disabled customer Booking form.
 $_POST=[];$isAdminPreview=true;$route='package/'.$previewPackage['slug'];
 require __DIR__.'/public.php';
}
