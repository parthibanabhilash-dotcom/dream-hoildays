<?php
// LOCAL TEST DATABASE ONLY. Never deploy/run this script on a hosting account.
if(PHP_SAPI!=='cli')exit;
$root=dirname(__DIR__);if(is_file($root.'/config/config.php')){echo 'Private local configuration already exists.'.PHP_EOL;exit;}
$pdo=new PDO('mysql:host=127.0.0.1;port=3306;charset=utf8mb4','root','',[PDO::ATTR_ERRMODE=>PDO::ERRMODE_EXCEPTION]);
$name='dream_holidays_local_test';$user='tdh_local_test';$password=bin2hex(random_bytes(24));
$pdo->exec('CREATE DATABASE IF NOT EXISTS '.$name.' CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci');
// Fail rather than change an existing user/account.
$pdo->exec("CREATE USER '".$user."'@'127.0.0.1' IDENTIFIED BY ".$pdo->quote($password));
$pdo->exec("GRANT ALL PRIVILEGES ON ".$name.".* TO '".$user."'@'127.0.0.1'");
$pdo->exec('USE '.$name);foreach(['schema.sql','drafts.sql'] as $file){foreach(explode(';',file_get_contents($root.'/database/'.$file)) as $sql)if(trim($sql)!=='')$pdo->exec($sql);}
$config=['db_dsn'=>'mysql:host=127.0.0.1;port=3306;dbname='.$name.';charset=utf8mb4','db_user'=>$user,'db_password'=>$password,'base_url'=>'http://127.0.0.1:8098','installation_token'=>bin2hex(random_bytes(32)),'session_secure'=>false,'timezone'=>'Asia/Kolkata'];
file_put_contents($root.'/config/config.php',"<?php\nreturn ".var_export($config,true).";\n");echo 'Isolated local database and private configuration created. No administrator seeded.'.PHP_EOL;