<?php
// Secrets come exclusively from the hosting environment, never source files.
$env=static fn(string $key,string $fallback=''): string => (($value=getenv($key))!==false?$value:$fallback);
$host=$env('TDH_DB_HOST');$port=$env('TDH_DB_PORT','3306');$name=$env('TDH_DB_NAME');
if(!preg_match('/^[a-zA-Z0-9.-]+$/D',$host)||!ctype_digit($port)||!preg_match('/^[a-zA-Z0-9_]+$/D',$name))throw new RuntimeException('Database environment is incomplete.');
$url=rtrim($env('TDH_BASE_URL'),'/');if(!filter_var($url,FILTER_VALIDATE_URL)||!str_starts_with($url,'https://'))throw new RuntimeException('Configure an HTTPS base URL.');
return [
 'db_dsn'=>"mysql:host=$host;port=$port;dbname=$name;charset=utf8mb4",
 'db_user'=>$env('TDH_DB_USER'),'db_password'=>$env('TDH_DB_PASSWORD'),
 'db_ssl_ca'=>$env('TDH_DB_SSL_CA'),'db_ssl_required'=>true,
 'base_url'=>$url,'installation_token'=>$env('TDH_INSTALLATION_TOKEN'),
 'session_secure'=>true,'session_driver'=>'database','clean_urls'=>true,
 'timezone'=>$env('TDH_TIMEZONE','Asia/Kolkata'),'serverless'=>true,
 'media_driver'=>'cloudinary','cloudinary_cloud_name'=>$env('CLOUDINARY_CLOUD_NAME'),
 'cloudinary_api_key'=>$env('CLOUDINARY_API_KEY'),'cloudinary_api_secret'=>$env('CLOUDINARY_API_SECRET'),
];
