<?php
	$url = "https://api.current-rms.com/api/v1/product_groups?page=1&per_page=500";
    $ch = curl_init( $url );
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "GET");
    curl_setopt($ch, CURLOPT_HTTP_VERSION, "CURL_HTTP_VERSION_1_1");
    curl_setopt( $ch, CURLOPT_HTTPHEADER, array('X-SUBDOMAIN:savage','X-AUTH-TOKEN:9Pi5sBfxa5th1kRC_TGy','Content-Type:application/json'));
    # Return response instead of printing.
    curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
    # Send request.
    $result = curl_exec($ch);
    curl_close($ch);
    # Print response.
    $result = json_decode($result);

    $product_groups = $result->product_groups;

    foreach ($product_groups as $key => $groups) {
    		echo "<pre>";
    		print_r($groups->id); echo "--> ";
    		print_r($groups->name);    		
    }		

   exit();
?>