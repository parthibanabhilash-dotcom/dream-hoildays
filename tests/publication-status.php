<?php
if(PHP_SAPI!=='cli')exit;
$parents=[];
function one(string $sql,array $args): ?array {global $parents;return $parents[$args[0]]??null;}
function url(string $route=''): string {return 'https://travel.example/'.$route;}
require dirname(__DIR__).'/app/admin-visibility.php';
function expect_visibility(bool $ok,string $label): void {if(!$ok)throw new RuntimeException($label);echo 'PASS: '.$label.PHP_EOL;}
$draft=['status'=>'draft','slug'=>'trip','featured'=>0];
expect_visibility(content_visibility('packages',$draft)[2]==='','draft package has no public link');
$published=array_replace($draft,['status'=>'published']);
expect_visibility(content_visibility('packages',$published)[2]===url('package/trip'),'published package links to its detail page');
expect_visibility(str_contains(content_visibility('packages',$published)[1],'Enable Featured'),'homepage visibility explained');
$image=['owner_type'=>'gallery','owner_id'=>0,'status'=>'published'];
expect_visibility(content_visibility('media',$image)[2]===url('gallery'),'gallery image links only to Gallery');
$parents[1]=['status'=>'draft','name'=>'Test trip','slug'=>'test-trip'];
$image=['owner_type'=>'packages','owner_id'=>1,'status'=>'published'];
expect_visibility(content_visibility('media',$image)[2]===''&&str_contains(content_visibility('media',$image)[0],'still a draft'),'published image of a draft package is not described as live');
$parents[1]['status']='published';
expect_visibility(content_visibility('media',$image)[2]===url('package/test-trip'),'package image links to its parent package');
$image['status']='draft';
expect_visibility(content_visibility('media',$image)[2]==='','draft image stays private even with published parent');
expect_visibility(str_contains(content_saved_message('media',$image),'not on the website'),'save message distinguishes draft from publication');
expect_visibility(media_admin_url('gallery')==='https://travel.example/admin/media?owner_type=gallery','gallery shortcut preserves its filter');
