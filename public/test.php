<?php 
// set our end point
//$domain = "https://limelitelighting.mobilegiz.com";

$store_url = 'https://limelitelighting.mobilegiz.com';
$endpoint = '/wc-auth/v1/authorize';
$params = [
    'app_name' => 'crimson',
    'scope' => 'read_write',
    'user_id' => 1,
    'return_url' => 'https://platform.crimson.dev',
    'callback_url' => 'https://platform.crimson.dev'
];
$query_string = http_build_query( $params );

echo $store_url . $endpoint . '?' . $query_string;
?>