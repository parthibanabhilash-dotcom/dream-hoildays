<?php
// Development router for readable URLs. Bind the built-in server to localhost only.
if(PHP_SAPI!=='cli-server')exit;
$path=parse_url($_SERVER['REQUEST_URI'],PHP_URL_PATH);
if(preg_match('~^/(?:assets|media)/[a-zA-Z0-9_./-]+$~',$path)&&!str_contains($path,'..')&&is_file(__DIR__.'/public'.$path))return false;
if($path!=='/index.php')$_GET['route']=trim($path,'/');require __DIR__.'/public/index.php';