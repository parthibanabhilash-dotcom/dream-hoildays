<?php
// Copy to config.php. This folder must stay outside the web document root.
return [
 'db_dsn' => 'mysql:host=localhost;dbname=dream_holidays;charset=utf8mb4',
 'db_user' => 'CHANGE_ME', 'db_password' => 'CHANGE_ME',
 'base_url' => 'https://example.com',
 'installation_token' => 'REPLACE_WITH_AT_LEAST_32_RANDOM_CHARACTERS',
 'session_secure' => true,
 'clean_urls' => true, // Set false if Apache rewrite is unavailable.
 // 'public_dir' => '/home/ACCOUNT/public_html', // Set when public/ is moved.

 'timezone' => 'Asia/Kolkata',
];
