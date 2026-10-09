<?php
$root=dirname(__DIR__);
if(!is_file($root.'/config/config.php')){http_response_code(503);header('Content-Type: text/html; charset=utf-8');echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Setup required — The Dream Holidays</title><main><h1>The Dream Holidays</h1><p>Website configuration is pending. Follow the private installation guide to connect the database and create an administrator.</p></main></html>';exit;}
$config=require $root.'/config/config.php';$GLOBALS['config']=$config;
date_default_timezone_set($config['timezone']??'Asia/Kolkata');
ini_set('display_errors','0');ini_set('log_errors','1');ini_set('error_log',$root.'/storage/php-errors.log');
ini_set('session.use_strict_mode','1');ini_set('session.use_only_cookies','1');
session_name('dream_holidays');session_set_cookie_params(['lifetime'=>0,'path'=>'/','secure'=>$config['session_secure']??true,'httponly'=>true,'samesite'=>'Lax']);session_start();
header('X-Content-Type-Options: nosniff');header('X-Frame-Options: DENY');header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self'; style-src 'self'; img-src 'self' data: blob:; connect-src 'self'; frame-src https://www.google.com https://maps.google.com; object-src 'none'; base-uri 'self'; form-action 'self'; frame-ancestors 'none'");
require $root.'/app/functions.php';
$route=trim((string)($_GET['route']??''),'/');
try {
 if($route==='robots.txt'){header('Content-Type: text/plain');echo "User-agent: *\nDisallow: /index.php?route=admin\nDisallow: /admin\nDisallow: /setup\nDisallow: /index.php?route=setup\nSitemap: ".url('sitemap.xml');exit;}
 if($route==='sitemap.xml'){header('Content-Type: application/xml; charset=utf-8');$links=['','packages','services','vehicles','gallery','contact'];foreach(rows("SELECT slug FROM pages WHERE status='published'") as $p)$links[]=$p['slug'];foreach(rows("SELECT slug FROM packages WHERE status='published'") as $p)$links[]='package/'.$p['slug'];echo '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';foreach($links as $link)echo '<url><loc>'.e(url($link)).'</loc></url>';echo '</urlset>';exit;}
 if($route==='setup'||str_starts_with($route,'admin')){header('Cache-Control: no-store');require $root.'/app/admin.php';exit;}
 require $root.'/app/public.php';
} catch(Throwable $error){error_log((string)$error);http_response_code($error instanceof HttpError?$error->getCode():503);echo '<!doctype html><html lang="en"><meta charset="utf-8"><title>Request unavailable</title><main><h1>The Dream Holidays</h1><p>'.($error instanceof HttpError?e($error->getMessage()):'This page is temporarily unavailable. Please try again later.').'</p></main></html>';}
