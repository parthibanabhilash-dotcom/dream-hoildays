<?php
// Vercel executes only this entrypoint. Public static output contains no PHP source.
$path=rawurldecode((string)(parse_url($_SERVER['REQUEST_URI']??'/',PHP_URL_PATH)??'/'));
if(preg_match('~^/(?:app|config|database|storage|tests|scripts|api)(?:/|$)|(?:^|/)\.|\x00~',$path)){
 http_response_code(404);header('Content-Type: text/plain');exit('Page not found.');
}
$GLOBALS['serverless_entry']=true;
if($path!=='/index.php')$_GET['route']=trim($path,'/');
require dirname(__DIR__).'/public/index.php';
