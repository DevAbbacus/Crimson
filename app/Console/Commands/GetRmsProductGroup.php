<?php

namespace App\Console\Commands;

use App\Models\ProductGroup;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Command;

class GetRmsProductGroup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'RmsProductGroup:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get products groups from the current RMS and delete if not available on RMS';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
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

                $crms_id_array[] = $groups->id; 
                
        }
            
        $category_from_crimson = ProductGroup::where('user_id', '39')->get();
        foreach ($category_from_crimson as $key => $crimson_category) {
             
            if (!in_array($crimson_category->crms_id, $crms_id_array)) {                    
                    
                  $delete_from_crimson =  DB::table('product_groups')->where('user_id',39)
                        ->where('crms_id',$crimson_category->crms_id)
                        ->delete();                   
                         echo $crimson_category->name." Category Deleted from Crimson!!";
                     
                }
        }
        //return 0;
    }
}
