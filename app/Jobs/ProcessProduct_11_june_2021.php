<?php

namespace App\Jobs;

use App\Enums\CostGroup;
use App\Enums\RevenueGroup;
use App\Models\Product;
use App\Models\ProductGroup;
use App\Models\Rates;
use App\Models\File;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Log;

class ProcessProduct implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $data;
    private $user;

    public function __construct($data, $user)
    {
        $this->data = $data;
        $this->user = $user;
    }

    public function handle()
    {
        try {
            $product = Product::updateOrCreate(
                [
                    'user_id' => $this->user->id,
                    'crms_id' => $this->data['id']
                ],
                $this->parsed()
            );
            //echo "<pre>";print_r($product);exit();

            if (isset($this->data['rental_rates']))
                foreach ($this->data['rental_rates'] as $rate)
                    $this->saveRates($rate, $product->id);

            if (isset($this->data['sale_rate']))
                $this->saveRates($this->data['sale_rate'], $product->id);

            if (isset($this->data['icon']))
                $this->saveImage($this->data['icon'], $product);
            
        } catch (Exception $e) {
            Log::error($e->getMessage());
        }
    }

    public function parsed()
    {
        $group = ProductGroup::where([
            'user_id' => $this->user->id,
            'crms_id' => $this->data['product_group_id']
        ])->first();


        return [
            'name' => $this->data['name'],
            'buffer_percent' => $this->data['buffer_percent'],
            'replacement_charge' => $this->data['replacement_charge'],
            'weight' => $this->data['weight'],
            'barcode' => $this->data['barcode'],
            'description' => $this->data['description'],
            'purchase_price' => $this->data['purchase_price'],
            'sub_rental_price' => $this->data['sub_rental_price'],
            'active' => $this->data['active'],
            'accessory_only' => $this->data['accessory_only'],
            'discountable' => $this->data['discountable'],
            'system' => $this->data['system'],
            'tag_list' => $this->data['tag_list'],
            'custom_fields' => $this->data['custom_fields'],
            'allowed_stock_type' => $this->data['allowed_stock_type'],
            'stock_method' => $this->data['stock_method'],
            'post_rent_unavailability' => $this->data['post_rent_unavailability'],
            'icon' => $this->data['icon']['url'],
            'product_group_id' => $group ? $group->id : null,
            'user_id' => $this->user->id,
            'crms_id' => $this->data['id'],
            'sale_revenue_group_id' => $this->data['sale_revenue_group_id'] ?: RevenueGroup::RENTAL,
            'purchase_cost_group_id' => $this->data['purchase_cost_group_id'] ?: CostGroup::OTHER,
        ];
    }

    public function saveRates($rate, $productId)
    {
        $id = array_key_exists('id', $rate) ? $rate['id'] : null;
        if (!$id) return;
        Rates::updateOrCreate(
            [
                'crms_id' => $id,
                'product_id' => $productId
            ],
            [
                'transaction_type' => $rate['transaction_type'],
                'rate_definition_id' => $rate['rate_definition_id'],
                'price' => $rate['price'],
                'crms_id' => $id,
                'product_id' => $productId,
                'store_id' => $rate['store_id'] ?? 1, // Default store to 1 if null fix later
            ]
        );
    }

    public function saveImage($icon, $product)
    {
        
    /*echo "<pre>";
    print_r($product);
    print_r($icon['url']);
    exit(); */
        if ($product->icon != $icon['url']) {

            $image = file_get_contents($product->icon);
            list($usec, $sec) = explode(" ", microtime());
            $filename = str_replace('.', '', ((float)$usec + ((float)$sec))) . '.' . 'jpeg';
            \Storage::disk('public')->put('files/'.$filename, $image);
            
            $file = new File(['path' => '/public/files/'.$filename]);
            $images = $product->image()->save($file);
                //echo "<pre>";print_r($images);exit();
        }        
        
    }
}
