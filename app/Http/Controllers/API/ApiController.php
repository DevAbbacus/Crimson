<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Auth;
use App\Models\Product;
use App\Models\AlternativeProducts;
use App\Models\File;
use App\Models\User;
use App\Models\Rates;
use App\Models\ProductGroup;
use App\Models\SubCategory;
use Illuminate\Support\Facades\DB;
class ApiController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

   
    public function syncWordpressProducts($id)
    {
        $user_id = $id;

        if (empty($user_id)) {
            return "User id not found!";
        }



        $product_query = Product::with('productAccessories')->where('user_id',$user_id)->get()->toArray();
        
        //  echo "<pre>";print_r($product_query);exit();

        $category_query = ProductGroup::where('user_id',$user_id)->get()->toArray();
        $sub_category_query = SubCategory::where('user_id',$user_id)->get()->toArray();
        

        $user_query = User::select('api_token','sub_domain','site_url')->where('id',$user_id)->get()->first()->toArray();
          
        $user_api_token  = $user_query['api_token'];
        $user_sub_domain = $user_query['sub_domain'];
        $user_site_url = $user_query['site_url'];

          
        foreach ($product_query as $key => $product) { 
          $defult_start_date = array('wp_product_start_date' => '2020-01-01');
          $defult_end_date = array('wp_product_end_date' => '2030-12-31');
          if (!array_key_exists("wp_product_end_date", $product['custom_fields']))
          {
            $product_query[$key]['custom_fields'] = array_merge($defult_end_date,$product_query[$key]['custom_fields']);
          }

          if (!array_key_exists("wp_product_start_date", $product['custom_fields']))
          {
              $product_query[$key]['custom_fields'] = array_merge($defult_start_date,$product_query[$key]['custom_fields']); 
          }       
        }

        


           
        /*get key*/
        if ($user_site_url) {
          $curl = curl_init();
          curl_setopt_array($curl, array(
            CURLOPT_URL => $user_site_url."/wp-json/wl/v1/post",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 50,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_SSL_VERIFYPEER => 0,
            CURLOPT_SSL_VERIFYHOST => FALSE
            //CURLOPT_POSTFIELDS => $data,
            
          ));


          $response = curl_exec($curl);

          $site_keys = json_decode($response);
        }

        $site_consumer_key = $site_keys->consumer_key;
        $site_consumer_secret = $site_keys->consumer_secret;

        $related_products = AlternativeProducts::select('related_id')->get()->toArray();

        $r_product_query = array();
        foreach ($related_products as $key => $r_product) {
          $rdata = Product::select('name')->where('user_id',$user_id)->where('id',$r_product['related_id'])->get()->toArray();
            if (!empty($rdata)) {

                $name = str_replace(" ","-", $rdata[0]['name']);
                $lower_name = strtolower($name);
                $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
                 


                $curl = curl_init();
                curl_setopt_array($curl, array(
                CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$final_sku."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

              $response = curl_exec($curl);

              $r_product_query[] = json_decode($response);

            }
          
        }

        $wp_id = array();
        foreach ($r_product_query as $key => $get_wp_id) {
          if (!empty($get_wp_id)) {
            $wp_id[] = $get_wp_id[0]->id;
          }
        }

          $i_data = implode(',', $wp_id);

          $relative_product_query = $i_data;

          function getProductImagesWp($pid)
          {
            $get_images = File::where('target_id',$pid)->get()->toArray();
            $return_data = array();
            $images = [];
            if (!empty($get_images)) {
              foreach ($get_images as $key => $image) {
                  $explode_seg = explode("/", $image['path']);
                  $imagePath = "https://platform.crimson.dev/".'/storage'.$image['path'];
                  $imagePath = str_replace('/public', '', $imagePath);
                  
                  $content = array('src' => $imagePath);
                  
                  $return_data[] = $content;
              }
                  return $return_data;
                    
            }
          }

          function insertProductMainCate($main_category_detail,$user_site_url,$site_consumer_key,$site_consumer_secret)
          {

              $m_category_name = $main_category_detail['name'];
              $name = str_replace(" ","-", $m_category_name);
              $lower_name = strtolower($name);
              $main_cate_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
              $final_main_cat_slug = str_replace("--","-", $main_cate_slug);
              $final_main_cat_slug = trim($final_main_cat_slug, '-');


              $categories_detail = array('name' => $main_category_detail['name'],'slug' => $final_main_cat_slug );

              $categories_detail_data = json_encode($categories_detail);
             
              $get_single_cate_data  = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);


              if ($user_site_url == "https://my-brand.be")
                
                {
                  $multi_lag_data = array('0' => 'nl','1' => 'fr','2' => 'en' );
                  $final_main_cat_slug_nl = $final_main_cat_slug."-nl";
                  $final_main_cat_slug_fr = $final_main_cat_slug."-fr";
                  $final_main_cat_slug_en = $final_main_cat_slug."-en";

                  foreach ($multi_lag_data as $key => $value) {  

                    if ($value == 'nl') {
                      
                      $categories_detail = array('name' => $main_category_detail['name'],'slug' => $final_main_cat_slug_nl);
                      $request_detail = json_encode($categories_detail);
                    }
                    if ($value == 'fr') {
                      
                      $categories_detail = array('name' => $main_category_detail['name'],'slug' => $final_main_cat_slug_fr);
                      $request_detail = json_encode($categories_detail);
                    }
                    if ($value == 'en') {
                      
                      $categories_detail = array('name' => $main_category_detail['name'],'slug' => $final_main_cat_slug_en);
                      $request_detail = json_encode($categories_detail);
                    }

                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?slug=".$final_main_cat_slug."&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    //CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                      "Authorization: Basic ",
                      "Content-Type: application/json",
                      "cache-control: no-cache"
                    ),
                    ));
                    $response = curl_exec($curl);
                    $response_data= json_decode($response);
                      
                    if ($response_data == null) 
                    {
                     
                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                      CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $request_detail,
                      CURLOPT_SSL_VERIFYPEER => 0,
                      CURLOPT_SSL_VERIFYHOST => FALSE,
                      CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic ",
                        "Content-Type: application/json",
                        "cache-control: no-cache"
                      ),
                      ));

                      $response = curl_exec($curl);
                      

                    }else{
                      continue;
                    }

                  }
                   
                        
                }else{

                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                      CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $categories_detail,
                      CURLOPT_SSL_VERIFYPEER => 0,
                      CURLOPT_SSL_VERIFYHOST => FALSE,
                      CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic ",
                        "Content-Type: application/json",
                        "cache-control: no-cache"
                      ),
                      ));

                      $response = curl_exec($curl);

                }
              
          }


          function get_categoryfromwp_mybrand($final_slug,$user_site_url,$site_consumer_key,$site_consumer_secret,$lang_val){

              

              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?slug=".$final_slug."&lang=".$lang_val."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

              $response = curl_exec($curl);
               return $response_decode = json_decode($response);


          }


          function get_categoryfromwp($final_slug,$user_site_url,$site_consumer_key,$site_consumer_secret){

              

              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?slug=".$final_slug."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

              $response = curl_exec($curl);
               return $response_decode = json_decode($response);


          }

          function get_uncategoryfromwp($user_site_url,$site_consumer_key,$site_consumer_secret){

              $curl = curl_init();
              curl_setopt_array($curl, array(
              CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?slug=uncategorized&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

              $response = curl_exec($curl);
               return $response_decode = json_decode($response);


          }


          $skip_site = array("https://www.tvlights.co.uk","https://limelitelighting.mobilegiz.com", "https://pccproductions.mobilegiz.com","https://hire.pccproductions.com.au");

          if (!in_array($user_site_url, $skip_site))
          {
              
            /*foreach ($category_query as $key => $main_category) {
                $insert_main_cate = array('name' => $main_category['name']);
                $checkProduct= insertProductMainCate($insert_main_cate,$user_site_url,$site_consumer_key,$site_consumer_secret);
            }*/
            //echo "categories done";
                
            /*if (!empty($sub_category_query)) {
              foreach ($sub_category_query as $key => $sub_category) {
                $m_category = ProductGroup::where('user_id',$user_id)->where('id',$sub_category['p_group'])->get()->first();
                if (!empty($m_category)) {
                      $name = str_replace(" ","-", $m_category->name);
                $lower_name = strtolower($name);
                $main_cat_final_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);

                $sub_category_name = str_replace(" ","-", $sub_category['name']);
                $sub_category_lower_name = strtolower($sub_category_name);
                $sub_cat_final_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $sub_category_lower_name);
                //$sub_cat_final_slug = str_replace("--","-", $sub_cat_final_slug_data);

                $get_single_cate_data  = get_categoryfromwp($main_cat_final_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);

                if (!isset($get_single_cate_data[0])) {
                    echo "<pre>";print_r($main_cat_final_slug);exit();
                }
                  
                $insert_sub_cate = array('name' => $sub_category['name'],'slug' => $sub_cat_final_slug,'parent' => $get_single_cate_data[0]->id,'description' => $sub_category['description'],'display' => 'default');

                $sub_category_name_fr = str_replace(" ","-", $sub_category['name_fr']);
                $sub_category_lower_name_fr = strtolower($sub_category_name_fr);
                $sub_cat_final_slug_fr =preg_replace('/[^A-Za-z0-9\-]/', '', $sub_category_lower_name_fr);

                $insert_sub_cate_fr = array('name' => $sub_category['name_fr'],'slug' => $sub_cat_final_slug_fr,'parent' => $get_single_cate_data[0]->id,'description' => $sub_category['description']);

                $sub_category_name_en = str_replace(" ","-", $sub_category['name_en']);
                $sub_category_lower_name_en = strtolower($sub_category_name_en);
                $sub_cat_final_slug_en =preg_replace('/[^A-Za-z0-9\-]/', '', $sub_category_lower_name_en);

                $insert_sub_cate_en = array('name' => $sub_category['name_en'],'slug' => $sub_cat_final_slug_en,'parent' => $get_single_cate_data[0]->id,'description' => $sub_category['description']);

                $request_detail_ln = json_encode($insert_sub_cate);
                $request_detail_fr = json_encode($insert_sub_cate_fr);
                $request_detail_en = json_encode($insert_sub_cate_en);
                
                if ($user_site_url == "https://my-brand.be")
                
                {
                  $multi_lag_data = array('0' => 'nl','1' => 'fr','2' => 'en' );

                  foreach ($multi_lag_data as $key => $value) {  

                    if ($value == 'nl') {
                      $request_detail = $request_detail_ln;
                    }
                    if ($value == 'fr') {
                      $request_detail = $request_detail_fr;
                    }
                    if ($value == 'en') {
                      $request_detail = $request_detail_en;
                    }
                    
                    $json_req_data = json_decode($request_detail, true);                  


                    $curl = curl_init();
                    curl_setopt_array($curl, array(
                    CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?slug=".$json_req_data['slug']."&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                    CURLOPT_RETURNTRANSFER => true,
                    CURLOPT_ENCODING => "",
                    CURLOPT_MAXREDIRS => 10,
                    CURLOPT_TIMEOUT => 30,
                    CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                    CURLOPT_CUSTOMREQUEST => "GET",
                    CURLOPT_SSL_VERIFYPEER => 0,
                    CURLOPT_SSL_VERIFYHOST => FALSE,
                    //CURLOPT_POSTFIELDS => $data,
                    CURLOPT_HTTPHEADER => array(
                      "Authorization: Basic ",
                      "Content-Type: application/json",
                      "cache-control: no-cache"
                    ),
                    ));
                    $response = curl_exec($curl);
                    $response_data= json_decode($response);
                      
                    if ($response_data == null) 
                    {
                     
                      $curl = curl_init();
                      curl_setopt_array($curl, array(
                      CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                      CURLOPT_RETURNTRANSFER => true,
                      CURLOPT_ENCODING => "",
                      CURLOPT_MAXREDIRS => 10,
                      CURLOPT_TIMEOUT => 30,
                      CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                      CURLOPT_CUSTOMREQUEST => "POST",
                      CURLOPT_POSTFIELDS => $request_detail,
                      CURLOPT_SSL_VERIFYPEER => 0,
                      CURLOPT_SSL_VERIFYHOST => FALSE,
                      CURLOPT_HTTPHEADER => array(
                        "Authorization: Basic ",
                        "Content-Type: application/json",
                        "cache-control: no-cache"
                      ),
                      ));

                      $response = curl_exec($curl);

                        }    
                  } 
                }else{  


                        $get_single_cate_data  = get_categoryfromwp($sub_cat_final_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);

                        if ($get_single_cate_data == null){
                              $curl = curl_init();
                              curl_setopt_array($curl, array(
                              CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

                        }else{
                            continue;
                          }
                      }
                }  
                
              }
            }*/
            //echo "<pre>";print_r("done");exit();
          }


          function getCateIdName($user_site_url,$site_consumer_key,$site_consumer_secret)
          {
            $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/categories?per_page=100&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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
              $response_all_cate = curl_exec($curl);
                
              $array_of_cate = json_decode($response_all_cate);

              $live_cate_detail = array();
              foreach ($array_of_cate as $key => $cateData) {
                  
                $live_cate_detail[] = array('id' => $cateData->id,'name' =>$cateData->name,'slug'=> $cateData->slug );
              }
                 return $live_cate_detail;
          }


          function getCatenamecrimson($cate_id,$user_id)
          {


             $category_query = ProductGroup::select('name')->where('user_id',$user_id)->where('id',$cate_id)->get()->toArray();
            
             return $category_query[0]['name'];
              
          }



          /*Insert product to wordpress site*/
          function insertProduct($insert_product,$product_id,$product_group_id,$user_id,$crms_id,$user_sub_domain,$user_api_token,$weight,$replacement_charge,$purchase_price,$user_site_url,$relative_product_query,$acc,$custom_fields,$site_consumer_key,$site_consumer_secret,$wp_product_start_date,$wp_product_end_date,$data_in_en,$data_in_fr,$sale_price,$discount_start,$discount_end)
          {              

                         
                  
             

              $tag_list = $insert_product['tag_list'];
              $status = $insert_product['status'];

                //echo "<pre>";print_r($status);exit();

              $product_sku = $insert_product['sku'];
              if (!empty($insert_product['notes'])) {
                $admin_notes = $insert_product['notes'];
              } else {
                $admin_notes = "";
              }
              
               // echo "<pre>";print_r($);exit();
              $curl = curl_init();
              curl_setopt_array($curl, array(
                CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$product_sku."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "GET",
                CURLOPT_SSL_VERIFYPEER => 0,
                CURLOPT_SSL_VERIFYHOST => FALSE,
                //CURLOPT_POSTFIELDS => $data,
                CURLOPT_HTTPHEADER => array(
                  "Authorization: Basic ",
                  "Content-Type: application/json",
                  "cache-control: no-cache"
                ),
              ));

              $response = curl_exec($curl);

              $data= json_decode($response);

              $images = getProductImagesWp($product_id);

              $wp_site_images = array();

              $live_images = array();
              foreach ($data as $key => $value) {

                      $wp_images = $value->images;
                      if ($wp_images) {
                          foreach ($wp_images as $key => $value) {
                            
                            $live_images[] = $value->name;
                          }
                      }

              }
              

              $im = array('images' => $images);

              $crms_img = array();
              $images = array();

              $images = $im['images'];

              if ($images) {
                foreach ($images as $key => $image) {
                  $images[$key] = $image['src'];

                  $explode = explode('files/', $images[$key]);
                  $crms_img[] = $explode[1]; 
                }               
              }

              $count_live_images = count($live_images);
              $count_crms_img = count($crms_img);
              
              if ($count_live_images != $count_crms_img) {                    
                    $insert_product = array_merge($insert_product,$im);
              }
              



              $skip_site = array("https://www.tvlights.co.uk","https://limelitelighting.mobilegiz.com", "https://pccproductions.mobilegiz.com","https://hire.pccproductions.com.au");

              if (!in_array($user_site_url, $skip_site))
              {
                  //echo "<pre>"; echo "site in"; print_r("in");exit();
                $getCateIdName = getCateIdName($user_site_url,$site_consumer_key,$site_consumer_secret);
                $crimson_cate =  getCatenamecrimson($product_group_id,$user_id);
                  
                $product_cate_id = array();

                /*24-june-2021*/
                $final_main_cat_slug_array = array();
                foreach ($getCateIdName as $key => $CateIdName) {
                    $main_cat_name = str_replace(" ","-", $crimson_cate);
                    $name_in_lower = strtolower($main_cat_name);
                    $main_cate_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $name_in_lower);
                    $final_main_cat_slug = str_replace("--","-", $main_cate_slug);

                    $update_meta_wpml = array("https://my-brand.be"); 
                    if (in_array($user_site_url, $update_meta_wpml))
                    {
                        $multi_lag_data = array('0' => 'ln','1' => 'en','2' => 'fr' );
                        foreach ($multi_lag_data as $key => $value) {

                          if ($value == 'en') {
                            $final_main_cat_slug_array[$value] = $final_main_cat_slug."-en";

                            if ($CateIdName['slug'] == $final_main_cat_slug_array[$value]) {
                              $get_main_cate_data = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);
                                if (!empty($get_main_cate_data[0])) {                        
                                $product_cate_id[] = array( "id" => $get_main_cate_data[0]->id);
                                $category = array('categories' => $product_cate_id);
                                $insert_product = array_merge($insert_product,$category);
                            }
                          }

                          }else if($value == 'fr'){
                            $final_main_cat_slug_array[$value] = $final_main_cat_slug."-fr";

                            if ($CateIdName['slug'] == $final_main_cat_slug_array[$value]) {
                              $get_main_cate_data = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);
                                if (!empty($get_main_cate_data[0])) {                        
                                $product_cate_id[] = array( "id" => $get_main_cate_data[0]->id);
                                $category = array('categories' => $product_cate_id);
                                $insert_product = array_merge($insert_product,$category);
                            }
                          }
                          }else{
                            $final_main_cat_slug_array[$value] = $final_main_cat_slug;

                            if ($CateIdName['slug'] == $final_main_cat_slug_array[$value]) {
                              $get_main_cate_data = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);
                                if (!empty($get_main_cate_data[0])) {                        
                                $product_cate_id[] = array( "id" => $get_main_cate_data[0]->id);
                                $category = array('categories' => $product_cate_id);
                                $insert_product = array_merge($insert_product,$category);
                            }
                          }
                          }
                        }
                    }else{
                        if ($CateIdName['slug'] == $final_main_cat_slug) {
                          $get_main_cate_data = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);
                            if (!empty($get_main_cate_data[0])) {                        
                            $product_cate_id[] = array( "id" => $get_main_cate_data[0]->id);
                            $category = array('categories' => $product_cate_id);
                            $insert_product = array_merge($insert_product,$category);
                        }
                      }
                    }

                                           
                    
                        
                }
                  
                      

              $subcategory_product_data = DB::table('subcategory_product')->where('sub_id',$product_id)->get();


              

               
              if (!empty($subcategory_product_data[0])) {

                  foreach ($subcategory_product_data as $key => $value) {
                    $sub_cat_id = $subcategory_product_data[$key]->relatable_id;
                    $product_subgroup_data = DB::table('product_subgroup')->where('id',$sub_cat_id)->get();
                    $sub_cat_name = $product_subgroup_data[0]->name;

                    $sub_cat_name = str_replace(" ","-", $sub_cat_name);
                    $name_in_lower = strtolower($sub_cat_name);
                    $sub_cate_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $name_in_lower);

                    $get_sub_cate_data = get_categoryfromwp($sub_cate_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);

                    if (!empty($get_sub_cate_data[0])) {                        
                        $product_cate_id[] = array( "id" => $get_sub_cate_data[0]->id);
                        $category = array('categories' => $product_cate_id);
                        $insert_product = array_merge($insert_product,$category);
                    }
                  }     
              }
            }

            

            $explode_tag_list = explode(",", $tag_list);
            //hidden


            
            if ($user_site_url == "https://lighthouserentals.com.au") {
                if (in_array("Package", $explode_tag_list)) {
                    $decode_acc = json_decode($acc);
                    
                    $bundle_products = array();
                    foreach ($decode_acc as $key => $explo_acc) {
                  
                        $name = str_replace(" ","-", $explo_acc->related_name);
                        $lower_name = strtolower($name);
                        $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);

                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$final_sku."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "GET",
                          CURLOPT_SSL_VERIFYPEER => 0,
                          CURLOPT_SSL_VERIFYHOST => FALSE,
                          //CURLOPT_POSTFIELDS => $data,
                          CURLOPT_HTTPHEADER => array(
                            "Authorization: Basic ",
                            "Content-Type: application/json",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response = curl_exec($curl);

                        $data_decode= json_decode($response);

                        $bundle_products[] = $data_decode[0]->id;
                    }
                    $acc_data = array('key'=>'accessories','value' => "");
                    $insert_product['grouped_products']=$bundle_products;
                    unset($insert_product['type']);
                    $insert_product['type'] = "grouped";

                }else{
                    $acc_data = array('key'=>'accessories','value' => $acc);

                }
            }

            if ($user_site_url == "https://baseboys.mobilegiz.com") {
                if (in_array("KIT", $explode_tag_list)) {
                    $decode_acc = json_decode($acc);
                  // echo "<pre>"; print_r($decode_acc); echo "</pre>"; exit;
                    $bundle_products = array();
                    foreach ($decode_acc as $key => $explo_acc) {
                      
                  
                        $name = str_replace(" ","-", $explo_acc->related_name);
                        $lower_name = strtolower($name);
                        $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);

                        
                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                          CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$final_sku."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                          CURLOPT_RETURNTRANSFER => true,
                          CURLOPT_ENCODING => "",
                          CURLOPT_MAXREDIRS => 10,
                          CURLOPT_TIMEOUT => 30,
                          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                          CURLOPT_CUSTOMREQUEST => "GET",
                          CURLOPT_SSL_VERIFYPEER => 0,
                          CURLOPT_SSL_VERIFYHOST => FALSE,
                          //CURLOPT_POSTFIELDS => $data,
                          CURLOPT_HTTPHEADER => array(
                            "Authorization: Basic ",
                            "Content-Type: application/json",
                            "cache-control: no-cache"
                          ),
                        ));

                        $response = curl_exec($curl);

                        $data_decode= json_decode($response);



                        $bundle_products[] = $data_decode[0]->id;
                    }
                    $acc_data = array('key'=>'accessories','value' => "");
                    //$acc_data = array('key'=>'grouped_products','value' => $bundle_products);
                    $insert_product['grouped_products']=$bundle_products;
                    unset($insert_product['type']);
                    $insert_product['type'] = "grouped";
               
                    

                }else{
                    $acc_data = array('key'=>'accessories','value' => $acc);

                }
            }


              $no_package = array("https://lighthouserentals.com.au","https://baseboys.mobilegiz.com");

              if (!in_array($user_site_url, $no_package)){
                $acc_data = array('key'=>'accessories','value' => $acc);
              }

              


             
          
              


              if (!empty($crms_id)) {
                $meta_data[] = array('key'=>'user_id','value' => $user_id);
                $meta_data[] = array('key'=>'crms_id','value' => $crms_id);
                $meta_data[] = array('key'=>'sub_domain','value' => $user_sub_domain);
                $meta_data[] = array('key'=>'api_token','value' => $user_api_token);
                $meta_data[] = array('key'=>'weight','value' => $weight);
                $meta_data[] = array('key'=>'replacement_charge','value' => $replacement_charge);
                $meta_data[] = array('key'=>'regular_price','value' => $purchase_price);
                
                $meta_data[] = array('key'=>'related_products','value' => $relative_product_query);
                $meta_data[] = $acc_data;
                $meta_data[] = array('key'=>'tag_list','value' => $tag_list);
                $meta_data[] = array('key'=>'notes','value' => $admin_notes);
                $meta_data[] = array('key'=>'product_start_date','value' => $wp_product_start_date);
                $meta_data[] = array('key'=>'product_end_date','value' => $wp_product_end_date);
                $meta_data[] = array('key'=>'allowed_stock_type','value' => $insert_product['allowed_stock_type']);


                $meta_data[] = array('key'=>'custom_fields','value' => json_encode($custom_fields));                
                $meta_detail = array('meta_data' => $meta_data );
                $insert_product = array_merge($insert_product,$meta_detail);
              }



              
              

              /*if ($data != null) {                 
                $ids = array();
                $req_data = json_encode($insert_product);
                foreach ($data[0]->categories as $key => $value) { 
                    
                  if ($value->id != '15') {
                        unset($insert_product['categories']);
                        $cat = array('categories' =>  array( $ids[] = array('id' => $value->id) ));
                        $insert_product = array_merge($insert_product,$cat);                        
                        $req_data = json_encode($insert_product);
                   }
                }
              }else{
              }*/
                

                

                $sale_prices = "";
                $discount_starts = "";
                $discount_ends = "";

                if (!empty($sale_price)) {
                  $sale_prices = array('sale_price' => (string)$sale_price);
                  $insert_product = array_merge($insert_product,$sale_prices);
                }else{
                  $sale_prices = array('sale_price' => "");
                  $insert_product = array_merge($insert_product,$sale_prices);
                }
                if (!empty($discount_start)) {
                  $discount_starts = array('date_on_sale_from' => $discount_start);
                  $insert_product = array_merge($insert_product,$discount_starts);
                }else{
                  $discount_starts = array('date_on_sale_from' => "");
                  $insert_product = array_merge($insert_product,$discount_starts);
                }
                if (!empty($discount_end)) {
                  $discount_ends = array('date_on_sale_to' => $discount_end);
                  $insert_product = array_merge($insert_product,$discount_ends);
                }else{
                  $discount_ends = array('date_on_sale_to' => "");
                  $insert_product = array_merge($insert_product,$discount_ends);
                }
                $req_data = json_encode($insert_product);

                /*if ($insert_product['name'] == "10a > 15a Truck Adapter") {
                 echo "<pre>";print_r($insert_product);exit();
                }*/

                /*if ($insert_product['name']=="1,5 x 1,5 Černý KIT") {
                    echo "<pre>";print_r($req_data);
                  echo "<pre>";print_r($insert_product);exit();
                }*/
              /*echo "<pre>";
              print_r($insert_product);
              echo "test";
              print_r($data);
              exit();*/
              if ($data != null) 
              {              
                

                $pro_id= $data[0]->id;

                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/".$pro_id."?consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "PUT",
                  CURLOPT_SSL_VERIFYPEER => 0,
                  CURLOPT_SSL_VERIFYHOST => FALSE,
                  CURLOPT_POSTFIELDS => $req_data,
                  CURLOPT_HTTPHEADER => array(
                    "Authorization: Basic ",
                    "Content-Type: application/json",
                    "cache-control: no-cache"
                  ),
                ));
                $update_response = curl_exec($curl);
                $return_update_response = json_decode($update_response);


                  $update_meta_wpml = array("https://my-brand.be"); 
                  if (in_array($user_site_url, $update_meta_wpml) || $return_update_response != null)
                  {
                      $multi_lag_data = array('0' => 'en','1' => 'fr' );

                      foreach ($multi_lag_data as $key => $value) {                         

                         
                          foreach ($return_update_response->categories as $key => $response_value) {
                            $cat_slug[$key] = $response_value->slug.'-'.$value;
                            $slug_wp =$cat_slug[$key];
                            $get_sub_cate_data = get_categoryfromwp_mybrand($slug_wp,$user_site_url,$site_consumer_key,$site_consumer_secret,$value);
                            $cat = array();
                            if (!empty($get_sub_cate_data)) {
                            $cat = $get_sub_cate_data[0]->id;
                            }                            

                          }
                              //echo "<pre>";print_r($get_sub_cate_data);exit();

                        $only_meta_pro_id = $return_update_response->id;

                        $only_meta[] = array('key'=>'product_start_date','value' => $wp_product_start_date);
                        $only_meta[] = array('key'=>'product_end_date','value' => $wp_product_end_date);
                        
                        $meta_detail = array('meta_data' => $only_meta );

                        $lang_wise_detail = "";

                        if ($user_site_url == 'https://my-brand.be') {
                          if ($value == 'en') {
                              $data_in_en['categories'][] = array('id' => $cat);
                              unset($data_in_en['status']);
                              $data_in_en['status'] = $return_update_response->status;
                              $lang_wise_detail = array_merge($data_in_en,$meta_detail);
                              //echo "<pre>en";print_r($lang_wise_detail);exit();
                          }
                          if ($value == 'fr') {
                            unset($data_in_fr['categories']);
                            $data_in_fr['categories'][] = array('id' => $cat);
                            unset($data_in_fr['status']);
                            $data_in_fr['status'] = $return_update_response->status;
                            $lang_wise_detail = array_merge($data_in_fr,$meta_detail);  
                            //echo "<pre>fr";print_r($lang_wise_detail);exit();                   
                          }
                        }

                        if ($value == 'en') {

                          $name = str_replace(" ","-", $data_in_en['name']);
                          $lower_name = strtolower($name);
                          $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
                          $final_sku = str_replace("--","-", $final_sku);
                            
                        }
                        if ($value == 'fr') {
                            
                          $name = str_replace(" ","-", $data_in_fr['name']);
                          $lower_name = strtolower($name);
                          $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
                          $final_sku = str_replace("--","-", $final_sku);
                        }


                        $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$final_sku."&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

                      $response = curl_exec($curl);
                      $lang_wise_response = json_decode($response);
                      $meta_detail_req = json_encode($lang_wise_detail);

                        if (empty($lang_wise_response)) {
                              
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                          
                            CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",

                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_SSL_VERIFYHOST => FALSE,
                            CURLOPT_POSTFIELDS => $meta_detail_req,
                            CURLOPT_HTTPHEADER => array(
                              "Authorization: Basic ",
                              "Content-Type: application/json",
                              "cache-control: no-cache"
                            ),
                          ));
                          $insert_response = curl_exec($curl);
                           //echo "<pre>sdf";print_r($insert_response);exit();
                            
                        }else{
                          $lang_wise_response_id = $lang_wise_response->id;
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                          CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/".$lang_wise_response_id."?lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "PUT",
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_SSL_VERIFYHOST => FALSE,
                            CURLOPT_POSTFIELDS => $meta_detail_req,
                            CURLOPT_HTTPHEADER => array(
                              "Authorization: Basic ",
                              "Content-Type: application/json",
                              "cache-control: no-cache"
                            ),
                          ));
                          $update_response = curl_exec($curl);
                           // echo "<pre>tgest";print_r($update_response);exit();
                        }
                      }                  

                  }
                


              } else { 
              	
              		//echo "<pre>";print_r($req_data);exit();

                $curl = curl_init();
                curl_setopt_array($curl, array(
                  CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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
                $return_insert_response = json_decode($insert_response);
                //echo "<pre>2";print_r($return_insert_response);exit();

                $update_meta_wpml = array("https://my-brand.be"); 
                  if (in_array($user_site_url, $update_meta_wpml) || $return_insert_response != null)
                  {
                      $multi_lag_data = array('0' => 'en','1' => 'fr' );

                      foreach ($multi_lag_data as $key => $value) {                        

                        
                        foreach ($return_update_response->categories as $key => $response_value) {
                          $cat_slug[$key] = $response_value->slug.'-'.$value;
                          $slug_wp =$cat_slug[$key];
                          $get_sub_cate_data = get_categoryfromwp_mybrand($slug_wp,$user_site_url,$site_consumer_key,$site_consumer_secret,$value);
                          $cat = array();
                            if (!empty($get_sub_cate_data)) {
                            $cat = $get_sub_cate_data[0]->id;
                            }
                          
                        }

                        $only_meta_pro_id = $return_insert_response->id;

                        $only_meta[] = array('key'=>'product_start_date','value' => $wp_product_start_date);
                        $only_meta[] = array('key'=>'product_end_date','value' => $wp_product_end_date);
                        
                        $meta_detail = array('meta_data' => $only_meta );

                        $lang_wise_detail = "";

                        if ($user_site_url == 'https://my-brand.be') {
                          if ($value == 'en') {
                              $data_in_en['categories'][] = array('id' => $cat);
                              unset($data_in_en['status']);
                              $data_in_en['status'] = $return_insert_response->status;
                              $lang_wise_detail = array_merge($data_in_en,$meta_detail);
                          }
                          if ($value == 'fr') {
                            $data_in_fr['categories'][] = array('id' => $cat);
                            unset($data_in_fr['status']);
                            $data_in_fr['status'] = $return_insert_response->status;
                            $lang_wise_detail = array_merge($data_in_fr,$meta_detail);                     
                          }
                        }
                          


                        if ($value == 'en') {

                          $name = str_replace(" ","-", $data_in_en['name']);
                          $lower_name = strtolower($name);
                          $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
                          $final_sku = str_replace("--","-", $final_sku);
                            
                        }
                        if ($value == 'fr') {
                            
                          $name = str_replace(" ","-", $data_in_fr['name']);
                          $lower_name = strtolower($name);
                          $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);
                          $final_sku = str_replace("--","-", $final_sku);
                        }

                         
                         $curl = curl_init();
                        curl_setopt_array($curl, array(
                        CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?sku=".$final_sku."&lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
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

                      $response = curl_exec($curl);
                      $lang_wise_response = json_decode($response); 
                          
                        $meta_detail_req = json_encode($lang_wise_detail);

                        

                        if (empty($lang_wise_response)) {
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                          CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products?lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "POST",
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_SSL_VERIFYHOST => FALSE,
                            CURLOPT_POSTFIELDS => $meta_detail_req,
                            CURLOPT_HTTPHEADER => array(
                              "Authorization: Basic ",
                              "Content-Type: application/json",
                              "cache-control: no-cache"
                            ),
                          ));
                          $insert_response = curl_exec($curl);
                            //echo "<pre>";print_r($insert_response);exit();
                            
                        }else{
                          $lang_wise_response_id = $lang_wise_response->id;
                          $curl = curl_init();
                          curl_setopt_array($curl, array(
                          CURLOPT_URL => $user_site_url."/wp-json/wc/v3/products/".$lang_wise_response_id."?lang=".$value."&consumer_key=".$site_consumer_key."&consumer_secret=".$site_consumer_secret."",
                            CURLOPT_RETURNTRANSFER => true,
                            CURLOPT_ENCODING => "",
                            CURLOPT_MAXREDIRS => 10,
                            CURLOPT_TIMEOUT => 30,
                            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                            CURLOPT_CUSTOMREQUEST => "PUT",
                            CURLOPT_SSL_VERIFYPEER => 0,
                            CURLOPT_SSL_VERIFYHOST => FALSE,
                            CURLOPT_POSTFIELDS => $meta_detail_req,
                            CURLOPT_HTTPHEADER => array(
                              "Authorization: Basic ",
                              "Content-Type: application/json",
                              "cache-control: no-cache"
                            ),
                          ));
                          $update_response = curl_exec($curl);
                        }
                      }                  

                  }


                
                }   
          }

            //echo "<pre>";print_r($product_query);exit();
          foreach ($product_query as $key => $product) { 


              $status = "publish";  
              $acc = '';
              
              $product_accessories = $product['product_accessories'];
              $accessorie = array();
              if (!empty($product['product_accessories'])) {

                  if ($user_site_url == "https://lighthouserentals.com.au") {

                    if (in_array("Package", $product['tag_list'])) {
                        $acc_implo = '';
                        foreach ($product_accessories as $key => $accessories) {
                          $accessorie[$key]['related_name'] = $accessories['related_name'];
                          $accessorie[$key]['quantity'] = $accessories['quantity'];
                          $convert_to[] = $accessorie[$key];
                          
                        }
                        $acc = json_encode($convert_to);
                    }else{
                      foreach ($product_accessories as $key => $accessories) {
                          $accessorie[] = $accessories['crms_id'];
                        }

                        $acc = implode(',', $accessorie);
                    }

                    
                }else if($user_site_url == "https://baseboys.mobilegiz.com"){

                  if (in_array("KIT", $product['tag_list'])) {
                        $acc_implo = '';
                        foreach ($product_accessories as $key => $accessories) {
                          $accessorie[$key]['related_name'] = $accessories['related_name'];
                          $accessorie[$key]['quantity'] = $accessories['quantity'];
                          $convert_to[] = $accessorie[$key];
                          
                        }
                        $acc = json_encode($convert_to);
                    }else{
                      foreach ($product_accessories as $key => $accessories) {
                          $accessorie[] = $accessories['crms_id'];
                        }

                        $acc = implode(',', $accessorie);
                    }

                }else{
                  foreach ($product_accessories as $key => $accessories) {
                    $accessorie[] = $accessories['crms_id'];
                  }

                  $acc = implode(',', $accessorie);
                }
              }
              
          

              $product_rates = Rates::select('price')->where('product_id',$product['id'])->get();
              $price = '0.0';
              if (isset($product_rates[0]->price)) {
                $price = $product_rates[0]->price;
              }

              if ($user_site_url == 'https://my-brand.be') {
                if ($product['custom_fields']['published_on_my_brand'] == "No") {
                  $status = "draft";   
                } else {
                  $status = "publish"; 
                }
                
              }

              if ($user_site_url == 'https://pccproductions.mobilegiz.com') {
                if ($product['active'] == "0") {
                  $status = "draft";
                }else{
                  $status = "publish"; 
                }
                                
              } 

              if ($user_site_url == 'https://hire.pccproductions.com.au') {
                if ($product['active'] == "0") {
                  $status = "draft";
                }else{
                  $status = "publish"; 
                }
                                
              }

              if ($user_site_url == 'https://lighthouserentals.com.au') {
                if ($product['accessory_only'] == "1") {
                  $status = "draft";
                }else{
                  $status = "publish"; 
                }
                                
              }

                //echo "<pre>";print_r($product);exit();
              
              $name = str_replace(" ","-", $product['name']);
              $lower_name = strtolower($name);
              $final_sku =preg_replace('/[^A-Za-z0-9\-]/', '', $lower_name);

              $wp_product_start_date = "";
              $wp_product_end_date = "";
              if (isset($product['custom_fields']['wp_product_start_date'])) {
                $date = $product['custom_fields']['wp_product_start_date'];
                $wp_product_start_date = str_replace('-','',$date);
              }
              if (isset($product['custom_fields']['wp_product_end_date'])) {
                $date = $product['custom_fields']['wp_product_end_date'];
                $wp_product_end_date = str_replace('-','',$date);
              }


              $product_en = (isset($product['custom_fields']['product_en'])) ? $product['custom_fields']['product_en'] : "" ;
              $product_fr = (isset($product['custom_fields']['product_fr'])) ? $product['custom_fields']['product_fr'] : "" ;
              $long_description_en = (isset($product['custom_fields']['long_description_en'])) ? $product['custom_fields']['long_description_en'] : "" ;
              $long_description_fr = (isset($product['custom_fields']['long_description_fr'])) ? $product['custom_fields']['long_description_fr'] : "" ;
              $short_description_en = (isset($product['custom_fields']['short_description_en'])) ? $product['custom_fields']['short_description_en'] : "" ;
              $short_description_fr = (isset($product['custom_fields']['short_description_fr'])) ? $product['custom_fields']['short_description_fr'] : "" ;



              	$getCateIdName = getCateIdName($user_site_url,$site_consumer_key,$site_consumer_secret);
                $crimson_cate =  getCatenamecrimson($product['product_group_id'],$product['user_id']);
              	
                  
                $product_cate_id = array();

                
                foreach ($getCateIdName as $key => $CateIdName) {
                    $main_cat_name = str_replace(" ","-", $crimson_cate);
                    $name_in_lower = strtolower($main_cat_name);
                    $main_cate_slug =preg_replace('/[^A-Za-z0-9\-]/', '', $name_in_lower);
                    $final_main_cat_slug = str_replace("--","-", $main_cate_slug);
                      if ($user_site_url == "https://my-brand.be") {
                        $final_main_cat_slug = $final_main_cat_slug."-nl";
                      }                      
                      
                  if ($CateIdName['slug'] == $final_main_cat_slug) {
                      $get_main_cate_data = get_categoryfromwp($final_main_cat_slug,$user_site_url,$site_consumer_key,$site_consumer_secret);
                        if (!empty($get_main_cate_data[0])) {                        
                        $product_cate_id[] = array( "id" => $get_main_cate_data[0]->id);
                        
                        
                    }
                  }      
                }

              $data_in_en = "";
              $data_in_fr = "";
              if ($user_site_url == 'https://my-brand.be') {
                $allowed_stock_type = "";
                if ($product['allowed_stock_type'] == 1) {
                    $allowed_stock_type = 'Rental';
                }else{
                    $allowed_stock_type = 'Sale';
                }
                $data_in_en = array('name' => $product_en,'description'=>$long_description_en,'short_description'=>$short_description_en,'status'=> $status,'categories' => $product_cate_id,'allowed_stock_type' => $allowed_stock_type);
                $data_in_fr = array('name'=>$product_fr,'description'=>$long_description_fr,'short_description'=>$short_description_fr,'status'=> $status,'categories' => $product_cate_id,'allowed_stock_type' => $allowed_stock_type);
              }

              

                
              $allowed_stock_type = "";
              if ($product['allowed_stock_type'] == 1) {
                  $allowed_stock_type = 'Rental';
              }else{
                  $allowed_stock_type = 'Sale';
              }

              $tags = "";
              if (isset($product['tag_list'])) {
                    $tags = implode(",", $product['tag_list']);
              }
                    //echo "<pre>";print_r();exit();
              $discount_amount = (isset($product['custom_fields']['discount_amount'])) ? $product['custom_fields']['discount_amount'] : "" ;
              $discount_start = (isset($product['custom_fields']['discount_start'])) ? $product['custom_fields']['discount_start'] : "" ;
              $discount_end = (isset($product['custom_fields']['discount_end'])) ? $product['custom_fields']['discount_end'] : "" ;

              $sale_price = "";



              if (!empty($product['custom_fields']['discount_amount']) && empty($product['custom_fields']['discount_percentage'])) {
                  $per = round((100*$product['custom_fields']['discount_amount'])/$price,2);
                  $product['custom_fields']['discount_percentage'] = $per;
                  $sale_price = $price - $product['custom_fields']['discount_amount'];
              }

              if(!empty($product['custom_fields']['discount_percentage']) && empty($product['custom_fields']['discount_amount'])){

              $product['custom_fields']['discount_amount'] = ($product['custom_fields']['discount_percentage'] * $price)/100;
              $sale_price = $price - $product['custom_fields']['discount_amount'];
              }



                /*L = List Price
                S = Sale Price
                D = Discount percentage

                    D=(L−S)L×100

                */


              /*if(!empty($product['custom_fields']['discount_amount']) && empty($product['custom_fields']['discount_percentage'])){

                $discount_amount = $product['custom_fields']['discount_amount'];

                $sale_price = $price - $discount_amount;

                $step_one = $price - $discount_amount;
                $step_two = $step_one/$price;
                $step_three = $step_two * 100;
                  $per = round($step_three,0);
                $product['custom_fields']['discount_percentage'] = $per;
                  

              }*/

              /*S=L−D100×L*/           
              
  
                
              /*if (empty($product['custom_fields']['discount_amount']) && !empty($product['custom_fields']['discount_percentage'])) {

                $discount_percentage = $product['custom_fields']['discount_percentage'];

                $step_one = $discount_percentage/100;
                $step_two = $step_one * $price;
                $sale_price = $price - $step_two;


                
              }*/

              //Subtract the final price from the original price.
              //Divide this number by the original price.
              //Finally, multiply the result by 100.

              $sale_price = round($sale_price, 0);
              $discount_start = $discount_start;
              $discount_end = $discount_end;


              $insert_product = array('name' =>$product['name'],'weight'=> $product['weight'],'status'=> $status,'user_id' => $product['user_id'],'sku' => $final_sku,'type'=>'simple','regular_price' => (string)$price,'description' => $product['description'],'notes' =>$product['notes'],'allowed_stock_type' => $allowed_stock_type,'tag_list' => $tags);
              $crms_id = $product['crms_id'];
              $custom_fields = $product['custom_fields'];


              /*$pro_array = array('Daylight Essentials','Doco Kit','HMI Interview Package','Tungsten Package Two','Advanced Grip Package','Basic Grip Package','Standard Grip Package','Advanced Power Package','Advanced Rigging Package','Advanced Stands Package','Basic Power Package','Basic Rigging Package','Basic Stands Package','Standard Power Package','Standard Rigging Package','Standard Stands Package');

                if (in_array($product['name'], $pro_array)) {*/
                    $checkProduct = insertProduct($insert_product,$product['id'],$product['product_group_id'],$user_id,$crms_id,$user_sub_domain,$user_api_token,$product['weight'],$product['replacement_charge'],$product['purchase_price'],$user_site_url,$relative_product_query,$acc,$custom_fields,$site_consumer_key,$site_consumer_secret,$wp_product_start_date,$wp_product_end_date,$data_in_en,$data_in_fr,$sale_price,$discount_start,$discount_end);


                /*} else {
                    continue;
                }*/
              
                            
              
               


              
          }
         
      $data = ['message'=> 'Your products and category sync successfully!'];               

        return response()->json($data); 
    }





    public function syncMgaeProducts($id)
    {
        //echo "<pre>";print_r("test");exit();

        $user_id = $id;

        if (empty($user_id)) {
            return "User id not found!";
        }
        $product_query = Product::where('user_id',$user_id)->get()->toArray();
        $category_query = ProductGroup::where('user_id',$user_id)->get()->toArray();
        $sub_category_query = SubCategory::where('user_id',$user_id)->get()->toArray();

          //echo "<pre>";print_r($category_query);exit();
        $params = array('product' => array());
        $params1 = array('category' => array());
        $params2 = array('category' => array());
        $params3 = array('category' => array());

        function AuthFunction()
        {
          
          $url                 = "http://desiredeffect.mobilegiz.com/index.php/";
          $token_url           = $url."rest/V1/integration/admin/token";
          $username            = "kscope-vishal";
          $password            = "@dmin24";
            $ch = curl_init();
            $data = array("username" => $username, "password" => $password);
            $data_string = json_encode($data);
            $ch = curl_init($token_url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($data_string))
            );
            $token = curl_exec($ch);
            $return_t_u = array('url' => $url,
                                'token' =>$token  );

             // echo "<pre>";print_r($return_t_u);exit();
            return $return_t_u;
        }

        function getSGname($id,$user_id,$responce_id)
        {
          $auth = AuthFunction();
          $token = $auth['token'];
          $url = $auth['url']. "/rest/default/V1/categories";

          $terms = explode(',',$id);
          $result_data = SubCategory::select('*')
                        ->ByUser($user_id)
                        ->where(function($result_data) use($terms) {
                            foreach($terms as $term) {
                                $result_data->orWhere('id', '=', $term);
                            };
                        })
                        ->get()
                        ->toArray();
        $cat_data = array();
        foreach ($result_data as $key => $value) 
        { 
          $params2['category'] = array(
                                'name'               => $value['name'],
                                "parent_id"          => $responce_id,
                                "is_active"          => true,
                                "is_active"          => true,
                                "position"           => 1,
                                "level"              => 1,
                                "include_in_menu"    => true
        );
        $params_data2 = json_encode($params2);
          $curl = curl_init();
          
          curl_setopt_array($curl, array(
          CURLOPT_URL => $url,
          CURLOPT_RETURNTRANSFER => true,
          CURLOPT_ENCODING => "",
          CURLOPT_MAXREDIRS => 10,
          CURLOPT_TIMEOUT => 30,
          CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
          CURLOPT_CUSTOMREQUEST => "POST",
          CURLOPT_POSTFIELDS => $params_data2,
          CURLOPT_HTTPHEADER => array(
          "accept: application/json",
          "content-type: application/json",
          "authorization: Bearer " . json_decode($token),
          ),
          ));
          $response = curl_exec($curl);
          return $response;
        }
               
        }

        /*get parent category of sub category*/
        function getParentCate($subcat_id,$user_id)
        {   
            $perent_data = ProductGroup::where('user_id',$user_id)->where('id',$subcat_id)->get()->toArray();
            return $perent_data;
        }

        /*check category exist*/
        function check_category_exist($name)
        {

          $auth = AuthFunction();
          $token = $auth['token'];
          
          $check_url= "http://desiredeffect.mobilegiz.com/custapi/getcatbyname.php?name=".$name;
          
          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => $check_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
              "accept: application/json",
              "content-type: application/json"
            ),
          ));
          $response = curl_exec($curl);
          $response_data = json_decode($response);

          if ($response_data == null) {
            return null;
          } else {
           return $response_data;
          }
        }


        /*insert main category if category not exist*/
        if ($category_query != null) {
            foreach ($category_query as $key => $category) {
              $check_category_exist = check_category_exist($category['name']);
                
              $params3['category'] = array(
                    'name'            => $category['name'],
                    "parent_id"       => 2,
                    "is_active"       => true,
                    "is_active"       => true,
                    "position"        => 1,
                    "level"           => 1,
                    "include_in_menu" => true
                    );
              $params_data = json_encode($params3);
              if ($check_category_exist == null) {                  

                  $url="http://desiredeffect.mobilegiz.com/index.php/";
                  $token_url=$url."rest/V1/integration/admin/token";
                  $product_url=$url. "/rest/default/V1/categories";
                  $username="kscope-vishal";
                  $password="@dmin24";
                  
                  
                  $ch = curl_init();
                  $data = array("username" => $username, "password" => $password);
                  $data_string = json_encode($data);
                  $ch = curl_init($token_url);
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  'Content-Type: application/json',
                  'Content-Length: ' . strlen($data_string))
                  );
                  $token = curl_exec($ch);
                  $ch = curl_init("http://desiredeffect.mobilegiz.com/index.php//rest/default/V1/categories");
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
                  $result = curl_exec($ch);
                  $result = json_decode($result, 1);
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                  CURLOPT_URL => $url . "/rest/default/V1/categories/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $params_data,
                  CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "content-type: application/json",
                    "authorization: Bearer " . json_decode($token),
                  ),
                  ));
                  $response = curl_exec($curl);
                  $responce_array = json_decode($response);
              }
            }
        }


        $responce_tree  = array();
        foreach ($sub_category_query as $key => $sub_category) {        
            $perent_cate = getParentCate($sub_category['p_group'],$user_id);
            foreach ($perent_cate as $key => $value) {
              $check_category_exist = check_category_exist($value['name']);
                if ($check_category_exist == null) {
                  $params1['category'] = array(
                      'name'            => $value['name'],
                      "parent_id"       => 2,
                      "is_active"       => true,
                      "is_active"       => true,
                      "position"        => 1,
                      "level"           => 1,
                      "include_in_menu" => true
                      );
                  $params_data = json_encode($params1);

                  $url="http://desiredeffect.mobilegiz.com/index.php/";
                  $token_url=$url."rest/V1/integration/admin/token";
                  $product_url=$url. "/rest/default/V1/categories";
                  $username="kscope-vishal";
                  $password="@dmin24";
                  
                  
                  $ch = curl_init();
                  $data = array("username" => $username, "password" => $password);
                  $data_string = json_encode($data);
                  $ch = curl_init($token_url);
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_POSTFIELDS, $data_string);
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                  'Content-Type: application/json',
                  'Content-Length: ' . strlen($data_string))
                  );
                  $token = curl_exec($ch);
                  $ch = curl_init("http://desiredeffect.mobilegiz.com/index.php//rest/default/V1/categories");
                  curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
                  curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                  curl_setopt($ch, CURLOPT_HTTPHEADER, array("Content-Type: application/json", "Authorization: Bearer " . json_decode($token)));
                  $result = curl_exec($ch);
                  $result = json_decode($result, 1);
                  $curl = curl_init();
                  curl_setopt_array($curl, array(
                  CURLOPT_URL => $url . "/rest/default/V1/categories/",
                  CURLOPT_RETURNTRANSFER => true,
                  CURLOPT_ENCODING => "",
                  CURLOPT_MAXREDIRS => 10,
                  CURLOPT_TIMEOUT => 30,
                  CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                  CURLOPT_CUSTOMREQUEST => "POST",
                  CURLOPT_POSTFIELDS => $params_data,
                  CURLOPT_HTTPHEADER => array(
                    "accept: application/json",
                    "content-type: application/json",
                    "authorization: Bearer " . json_decode($token),
                  ),
                  ));
                  $response = curl_exec($curl);
                    //echo "<pre>";print_r($response);exit();
                  $responce_array = json_decode($response);
                  getSGname($sub_category['id'],$user_id,$responce_array->id);
                } else {                  
                  $response = getSGname($sub_category['id'],$user_id,$check_category_exist->entity_id);
                }
                
            }
                
        
        }
        

        //function for the check Allowed Stock Type
        function checkAllowedStockType($type)
        {
          if ($type == "Sale") {
            return array(2);
          } else if($type == "Rental") {
            return array(3);
          }else{
            return array(2,3);
          }
          
        }

        //function for the get User Api Key
        function getUserApiKey($user_id)
        {
          $user_query = User::select('api_token')->where('id',$user_id)->get()->first();
            return $api_token = $user_query['api_token'];
        }

        //function for the check Allowed Stock view
        function CheckStoreView($type)
        {
          if ($type == "Sale") {
            return 'sales_storeview';
          } else if($type == "Rental") {
            return 'rentals_storeview';
          }else{
            return 'default';
          }
          
        }

        function getProductImages($pid)
        {
          $get_images = File::where('target_id',$pid)->get()->toArray();
          $return_data = array();
          $images = [];
          if (!empty($get_images)) {
            foreach ($get_images as $key => $image) {
                $explode_seg = explode("/", $image['path']);
                $imagePath = "https://platform.crimson.dev/".'/storage'.$image['path'];
                $imagePath = str_replace('/public', '', $imagePath);
                $type = pathinfo($imagePath, PATHINFO_EXTENSION);
                $data = file_get_contents($imagePath);

                $base64 = 'data:image/' . $type . ';base64,' . base64_encode($data);
                $images[] = ['id' => $image['id'], 'name' => $explode_seg[3], 'path' => $imagePath , 'type' => $type , 'base64' =>$base64];                  
                if($type == "jpg"){
                  $type = "jpeg";
                }
                $content = array('base64_encoded_data' => $base64, 'type' => "image/".$type, 'name' => $explode_seg[3] );
                
                $return_data[] = array('file' => $explode_seg[3] , 'content' => $content );
            }
            
                return $return_data;
                  
          }
        }


        function getCategoryName($id){
          $Groupname = ProductGroup::select('name')->where('id',$id)->first()->toArray();
          return $Groupname['name'];
        }

        function getCategoryId($name)
        {
          $auth = AuthFunction();
          $token = $auth['token'];

          $check_url= "http://desiredeffect.mobilegiz.com/custapi/getcatbyname.php?name=".$name;
          
          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => $check_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_HTTPHEADER => array(
              "accept: application/json",
              "content-type: application/json"
            ),
          ));
          $response = curl_exec($curl);
          $response_data = json_decode($response);
          $response_data = (array)$response_data;
          if ($response_data != null) {
            return $cate_data = array('entity_id' => $response_data['entity_id'],'position' => $response_data['position']);
          }else{
            return $cate_data = array('entity_id' => '','position' => '');

          }
        }


        //function for the check product exist
        function checkProductExist($product_sku,$StoreView)
        { 
          $auth = AuthFunction();
          $token = $auth['token'];
          $check_url = $auth['url'];

          
          $product_url=$check_url. "/rest/".$StoreView."/V1/products/".$product_sku;
          

          $curl = curl_init();

          curl_setopt_array($curl, array(
            CURLOPT_URL => $product_url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
              "accept: application/json",
              "content-type: application/json",
              "authorization: Bearer " . json_decode($token),
            ),
          ));
          $response = curl_exec($curl);
          $responce_data = json_decode($response);
          if (!empty($responce_data->sku)) {
               $replay_for_exist = array('exist' => 1,'StoreView' => $StoreView); 
            return $replay_for_exist;
          } else {
            $replay_for_exist = array('exist' => 0,'StoreView' => $StoreView);
            return $replay_for_exist;
          }

        }


        if(!empty($product_query)){

        //$l = 0;
        
        foreach ($product_query as $key => $value) 
        {

          if ($value['active'] == 1) {
                $status = 1;
            } else {
                $status = 0;
            }
            $StockType = checkAllowedStockType($value['allowed_stock_type_name']); 

            $StoreView = CheckStoreView($value['allowed_stock_type_name']); 

            $user_api_key = getUserApiKey($value['user_id']);
            
            if (!empty($value['name'])) {
                $name1 = str_replace(' ', '_', $value['name']);
                $sku = str_replace('"', '', $name1);
                
            }
                  

            
            if (!empty($value['custom_fields'])) {
                $notes = $value['custom_fields']['notes'];
                $label_status = $value['custom_fields']['label_status'];
                $maintenance_procedure = $value['custom_fields']['maintenance_procedure'];
                $new_item_check_in_procedure = $value['custom_fields']['new_item_check_in_procedure'];                
            }



            $product_image = getProductImages($value['id']);
            $media_gallery_entries = array();
            $i = 0;
            
            foreach ((array)$product_image as $key => $producti) 
            {
              if ($i == 0) {
                $types = array("image","small_image","thumbnail");
              }else{
                $types = array();

              }

              $file_type = $producti['file'];
              $type = explode('.', $file_type);            
              
             $media_gallery_entries[] = array(
                                    
                                      "media_type" => "image",
                                      "label" => "Image",
                                      "position" => $i,
                                      "disabled" => 0,
                                      "types" => $types,
                                      "file" => $producti['file'],
                                      "content" => str_replace("data:image/".$type[1].";base64,", '', $producti['content'])
                                    
                                  );
           
             $i++;
            }

            if ($media_gallery_entries != null) {
              $gallery_entries = $media_gallery_entries;
            }else{
              $gallery_entries = array();
            }



            $CategoryName  = getCategoryName($value['product_group_id']);
            $Category_detail  = getCategoryId($CategoryName);
              //echo "<pre>";print_r($Category_detail);exit();

            

            $params['product']  = array(
                'sku'                  => $sku,
                'name'                 => $value['name'],
                'attribute_set_id'     => 4,                
                'price'                => $value['purchase_price'],
                'status'               => $status,
                'visibility'           => '4',
                'type_id'              => 'simple',
                'created_at'           => date('y-m-d'),
                'updated_at'           => date('y-m-d'),
                'weight'               => $value['weight'],
                
                'extension_attributes' => 
                  array( 
                      'category_links' => 
                        array( 
                            array(
                            'position' => $Category_detail['position'],
                            'category_id'  => $Category_detail['entity_id'],
                            )                                          
                        ),
                      'stock_item' => 
                        array(
                            'qty' => 10,
                            'is_in_stock'  => true,
                            'use_config_min_qty'=> true,
                            'min_sale_qty'=> 0,
                            'use_config_max_sale_qty' => true,
                            'max_sale_qty' => 0,
                            'use_config_notify_stock_qty' => true,
                            'notify_stock_qty' => 0,
                            'use_config_qty_increments' => true,
                            'qty_increments' => 0,
                             'use_config_enable_qty_inc' => true,
                            'enable_qty_increments' => true,
                        ),
                    "website_ids" => $StockType,

                    ), 
                  'media_gallery_entries' => $gallery_entries,                  
                  'custom_attributes' => array(                     
                    array(
                    'attribute_code' => 'description',
                    'value'  => $value['description']
                    ),
                    array(
                    'attribute_code' => 'tax_class_id',
                    'value'  => 2
                    ),
                    array(
                    'attribute_code' => 'material',
                    'value'  => 148
                    ),
                    array(
                    'attribute_code' => 'pattern',
                    'value'  => 196
                    ),
                    array(
                    'attribute_code' => 'color',
                    'value'  => 52
                    ),
                    array(
                    'attribute_code' => 'size',
                    'value'  => 168
                    ),
                    array(
                    'attribute_code' => 'crms_product_id',
                    'value'  => $value['crms_id']
                    ),
                    array(
                    'attribute_code' => 'notes',
                    'value'  => $notes
                    ),
                    array(
                    'attribute_code' => 'label_status',
                    'value'  => $label_status
                    ),
                    array(
                    'attribute_code' => 'maintenance_procedure',
                    'value'  => $maintenance_procedure
                    ),
                    array(
                    'attribute_code' => 'new_item_check_in_procedure',
                    'value'  => $new_item_check_in_procedure
                    ),
                )
                                    
            );
            $product_exist = checkProductExist($sku,$StoreView);                 

            if ($product_exist['StoreView'] == 'rentals_storeview') {
                  $custom_attributes=  array(
                                            'attribute_code' => 'user_key',
                                            'value'  => $user_api_key
                                            );
                    array_push($params['product']['custom_attributes'] ,$custom_attributes);
                } else {
                  $custom_attributes =  array(
                                            'attribute_code' => 'user_key',
                                            'value'  => ''
                                            );
                   array_push($params['product']['custom_attributes'] ,$custom_attributes);
                }

            //echo "<pre>";print_r($params);exit();
            $datas =  json_encode($params);

            if ($product_exist['exist'] == 0) 
            {
              $auth = AuthFunction();
              $token = $auth['token'];
              $url = $auth['url'];

              

              $curl = curl_init();

              curl_setopt_array($curl, array(
                CURLOPT_URL => $url . "rest/".$product_exist['StoreView']."/V1/products/",
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "POST",
                CURLOPT_POSTFIELDS => $datas,
                CURLOPT_HTTPHEADER => array(
                  "accept: application/json",
                  "content-type: application/json",
                  "authorization: Bearer " . json_decode($token),
                ),
              ));
              $response = curl_exec($curl);
               // echo "<pre>";print_r($response);exit();
              $err = curl_error($curl);

              curl_close($curl);
            } else {

              $auth = AuthFunction();
              $token = $auth['token'];
              $url = $auth['url'];
              $product_url=$url. "/rest/".$product_exist['StoreView']."/V1/products/".$sku;
              $curl = curl_init();

              curl_setopt_array($curl, array(
                CURLOPT_URL => $product_url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => "",
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 30,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => "PUT",
                CURLOPT_POSTFIELDS => $datas,
                CURLOPT_HTTPHEADER => array(
                  "accept: application/json",
                  "content-type: application/json",
                  "authorization: Bearer " . json_decode($token),
                ),
              ));
              $response = curl_exec($curl);
               // echo "<pre>";print_r($response);exit();

             
              $err = curl_error($curl);

              curl_close($curl);
            }
        }
      }


      $data = [
                  'success' => true,
                  'message'=> 'Your products and category sync successfully!'
                ] ;               

        return response()->json($data);

          
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
