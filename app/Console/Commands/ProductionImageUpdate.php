<?php

namespace App\Console\Commands;

use App\Models\File;
use App\Models\Product;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProductionImageUpdate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'productImage:update';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Product Image update on System';

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
        $products = Product::all();
        $this->info('Product Image Command starting...');
        if (isset($products) && sizeof($products) > 0) {
            $this->info('Number of products :-' . sizeof($products));
            foreach ($products as $product) {
                $user = $product->user;
                $files = File::where('target_type', 'App\Models\Product')
                    ->where('target_id', $product->id)
                    ->get();

                if (isset($files) && sizeof($files) > 0) {
                    Log::info("Product id:- " . $product->id . "Name:- " . $product->name . " has already product image...");
                    $this->info("Product id:- " . $product->id . "Name:- " . $product->name . " has already product image...");
                } else {
                    if (isset($product->icon) && $product->icon != '' && $user->site_url == 'https://www.tvlights.co.uk' && $user->id == '24') {
                        $image = file_get_contents($product->icon);
                        list($usec, $sec) = explode(" ", microtime());
                        $filename = str_replace('.', '', ((float)$usec + ((float)$sec))) . '.' . 'jpeg';
                        \Storage::disk('public')->put('files/'.$filename, $image);

                        Log::info("Product Detail:- " . $product);
                        $file = new File(['path' => '/public/files/'.$filename]);
                        $images = $product->image()->save($file);
                        Log::info("Product Image:- " . $images);
                        $this->info("Product Image updated Successfully for Product Id:- " . $product->id);
                    }
                }
            }
        } else {
            $this->error("No Product found in DB");
        }
        $this->info('Successfully update!!');
    }
}
