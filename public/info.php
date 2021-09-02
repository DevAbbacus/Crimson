<?php 


$req_data = '{
  "name": "Test grouped product",
  "type": "grouped",
  "regular_price": "53.0",
  "sale_price": "37.0",
  "categories": [
	    {
	        "id": 646
	    }
	],
	"images": [
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2016/09/Ninja_bundle.jpg",
               "position": 0
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/hoodie_4_front.jpg",
                "position": 1
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/hoodie_4_back.jpg",
                "position": 2
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/T_7_front.jpg",
                "position": 3
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/T_7_back.jpg",
                "position": 4
            }
        ],
  "grouped_products": [100674,100670]
}';

/*$req_data = '{
        "name": "Happy Ninja Bundle",
        "type": "grouped",
        "regular_price": "53.0",
        "sale_price": "37.0",
        "description": "In pretium enim justo, at ornare libero aliquam quis. Nullam imperdiet rutrum volutpat. Suspendisse aliquet ex in ex volutpat vestibulum. Curabitur ultrices convallis condimentum.",
        "short_description": "In pretium enim justo, at ornare libero aliquam quis.",
        "categories": [
            {
                "id": 1007
            }
        ],
        "images": [
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2016/09/Ninja_bundle.jpg",
               "position": 0
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/hoodie_4_front.jpg",
                "position": 1
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/hoodie_4_back.jpg",
                "position": 2
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/T_7_front.jpg",
                "position": 3
            },
            {
                "src": "http://somewherewarm.net/pb/wp-content/uploads/2013/06/T_7_back.jpg",
                "position": 4
            }
        ],
        "grouped_products": [123213,123842],            
        "bundle_layout": "default"
    }';*/




$curl = curl_init();
curl_setopt_array($curl, array(
  CURLOPT_URL => "https://baseboys.mobilegiz.com/wp-json/wc/v2/products?consumer_key=ck_2143614c0353070abb347f5114e908adefeb9425&consumer_secret=cs_b407133e32e3ed957ef67aa79b1b202b9d299b41",
  CURLOPT_RETURNTRANSFER => true,
  CURLOPT_ENCODING => "",
  CURLOPT_MAXREDIRS => 10,
  CURLOPT_TIMEOUT => 30,
  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
  CURLOPT_CUSTOMREQUEST => "POST",
  CURLOPT_SSL_VERIFYPEER => 0,
  CURLOPT_SSL_VERIFYHOST => FALSE,
  CURLOPT_POSTFIELDS => $req_data,
  CURLOPT_HTTPHEADER => array(
    "Authorization: Basic ",
    "Content-Type: application/json",
    "cache-control: no-cache"
  ),
));
$insert_response = curl_exec($curl);

$insert_response = json_decode($insert_response);


//$response = curl_exec($curl);
	echo "<pre>";print_r($insert_response);exit();



	/*$curl = curl_init();
curl_setopt_array($curl, array(
CURLOPT_URL => "https://www.tvlights.co.uk/wp-json/wl/v1/post",
CURLOPT_RETURNTRANSFER => true,
CURLOPT_ENCODING => "",
CURLOPT_MAXREDIRS => 10,
CURLOPT_TIMEOUT => 30,
CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
CURLOPT_CUSTOMREQUEST => "GET",
CURLOPT_SSL_VERIFYPEER => 0,
CURLOPT_SSL_VERIFYHOST => FALSE
//CURLOPT_POSTFIELDS => $data,

));

if(curl_exec($curl) === false)
{
  echo 'Curl error: ' . curl_error($curl);
}
else
{
  echo 'Operation completed without any errors';
}


$response = curl_exec($curl);
	echo "<pre>";print_r($response);exit();*/