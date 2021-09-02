<?php 
$insert_sub_cate = array('name' => 'testcate3july','parent' => 1814,'description' => 'test description','display' => 'default','menu_order' => 0);


$request_detail_ln = json_encode($insert_sub_cate);


$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => "https://lighthouserentals.mobilegiz.com/wp-json/wc/v3/products/categories?consumer_key=ck_2143614c0353070abb347f5114e908adefeb9425&consumer_secret=cs_b407133e32e3ed957ef67aa79b1b202b9d299b41",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "POST",
CURLOPT_POSTFIELDS => $request_detail_ln,
CURLOPT_SSL_VERIFYPEER => 0,
CURLOPT_SSL_VERIFYHOST => FALSE,
CURLOPT_HTTPHEADER => array(
  "Authorization: Basic ",
  "Content-Type: application/json",
  "cache-control: no-cache"
),
));

$response = curl_exec($curl);
$response = json_decode($response);
	echo "<pre>";print_r($response);exit();
?>