<?php
/** Explain the same publication rules used by the public templates. */
function content_visibility(string $entity,array $record): array {
 if(isset($record['status'])&&$record['status']!=='published')return ['Draft — not on the website','Select Published, confirm approval and save to show this content.',''];
 if($entity==='packages')return ['Published — on the Packages page',empty($record['featured'])?'Enable Featured to also show this package on the homepage.':'This package is also featured on the homepage.',url('package/'.$record['slug'])];
 if($entity==='media'){
  $owner=$record['owner_type'];
  if($owner==='gallery')return ['Published — in the Gallery','This image appears in the website Gallery, not in a tour package.',url('gallery')];
  if($owner==='team')return ['Published — on About Us','This image appears in the team section on the About Us page.',url('about')];
  if(in_array($owner,['packages','services','vehicles'],true)){
   $parent=one('SELECT * FROM '.$owner.' WHERE id=?',[(int)$record['owner_id']]);
   if(!$parent)return ['Not on the website — linked record missing','Choose an existing package, service or vehicle for this image.',''];
   if($parent['status']!=='published')return ['Image published — linked content is still a draft','Publish '.$parent['name'].' to show this image on the website.',''];
   return ['Published — on '.ucfirst($owner),$owner==='packages'?'This image belongs to '.$parent['name'].'. It does not appear in the separate Gallery.':'This image appears with '.$parent['name'].'.',url($owner==='packages'?'package/'.$parent['slug']:$owner)];
  }
 }
 if(in_array($entity,['services','vehicles'],true))return ['Published — on the website','',url($entity)];
 if($entity==='pages')return ['Published — on the website','',url($record['slug'])];
 return ['Saved','', ''];
}
function visibility_panel(string $entity,array $record): void {
 if(!isset($record['status']))return;
 [$title,$detail,$link]=content_visibility($entity,$record);
 echo '<aside class="notice publication-status"><strong>'.e($title).'</strong>'.($detail!==''?'<p>'.e($detail).'</p>':'');
 if($link!=='')echo '<a class="text-link" href="'.e($link).'" target="_blank" rel="noopener noreferrer">View on website ↗</a>';
 echo '</aside>';
}
function content_saved_message(string $entity,array $record): string {
 [$title,$detail]=content_visibility($entity,$record);
 return 'Changes saved. '.$title.'.'.($detail!==''?' '.$detail:'');
}
function media_admin_url(string $owner,string $action=''): string {
 $base=url('admin/media'.$action);
 return $base.(str_contains($base,'?')?'&':'?').'owner_type='.rawurlencode($owner);
}
