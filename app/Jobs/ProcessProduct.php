<?php

namespace App\Jobs;

use App\Enums\CostGroup;
use App\Enums\RevenueGroup;
use App\Models\Product;
use App\Models\ProductAccessorie;
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
use App\Helpers\CommonHelper;

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

            if (isset($this->data['rental_rates'])){
                foreach ($this->data['rental_rates'] as $rate){
                    $this->saveRates($rate, $product->id,'rental');
                }
            }

            if (isset($this->data['sale_rate'])){
                $this->saveRates($this->data['sale_rate'], $product->id,'sele');
            }

            if (isset($this->data['icon']))
                $this->saveImage($this->data['icon'], $product);

            if (isset($this->data['accessories']))
                $this->saveAccessories($this->data['accessories'], $product);
            
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

       $sub_domain_name =  $this->user->sub_domain;

        $customFieldsArr = [];
        $tempCustomFields = array (
            'type_of_item' => 1000010,
        );
        $dataCustomFields = $this->data['custom_fields'];

        $commanHelper = new CommonHelper();
        if (isset($dataCustomFields) && sizeof($dataCustomFields) > 0) {
            foreach ($dataCustomFields as $dataCustomFieldKey => $dataCustomFieldValue) {
                $customFieldsArr[$dataCustomFieldKey] = $commanHelper->getCustomFieldValue($dataCustomFieldValue,$sub_domain_name);
            }
        }

        return [
            'name' => isset($this->data['name']) ? $this->data['name'] : '',
            'buffer_percent' => isset($this->data['buffer_percent']) ? $this->data['buffer_percent'] : '',
            'replacement_charge' => isset($this->data['replacement_charge']) ? $this->data['replacement_charge'] : '',
            'weight' => isset($this->data['weight']) ? $this->data['weight'] : '',
            'barcode' => isset($this->data['barcode']) ? $this->data['barcode'] : '',
            'description' => isset($this->data['description']) ? $this->data['description'] : '',
            'purchase_price' => isset($this->data['purchase_price']) ? $this->data['purchase_price'] : '',
            'sub_rental_price' => isset($this->data['sub_rental_price']) ? $this->data['sub_rental_price'] : '',
            'active' => isset($this->data['active']) ? $this->data['active'] : '',
            'accessory_only' => isset($this->data['accessory_only']) ? $this->data['accessory_only'] : '',
            'discountable' => isset($this->data['discountable']) ? $this->data['discountable'] : '',
            'system' => isset($this->data['system']) ? $this->data['system'] : '',
            'tag_list' => isset($this->data['tag_list']) ? $this->data['tag_list'] : '',
            'custom_fields' => $customFieldsArr,
//            'custom_fields' => $this->data['custom_fields'],
            'allowed_stock_type' => isset($this->data['allowed_stock_type']) ? $this->data['allowed_stock_type'] : '',
            'stock_method' => isset($this->data['stock_method']) ? $this->data['stock_method'] : '',
            'post_rent_unavailability' => isset($this->data['post_rent_unavailability']) ? $this->data['post_rent_unavailability'] : '',
            'icon' => isset($this->data['icon']) && isset($this->data['icon']['url']) ? $this->data['icon']['url'] : '',
            'product_group_id' => $group ? $group->id : null,
            'user_id' => isset($this->user->id) ? $this->user->id : '',
            'crms_id' => isset($this->data['id']) ? $this->data['id'] : '',
            'sale_revenue_group_id' => $this->data['sale_revenue_group_id'] ?: RevenueGroup::RENTAL,
            'purchase_cost_group_id' => $this->data['purchase_cost_group_id'] ?: CostGroup::OTHER,
        ];
    }

    public function saveRates($rate, $productId,$type=null)
    {
        if ($type == "sele") {
           $item_id = array_key_exists('item_id', $rate) ? $rate['item_id'] : null;
        }else{
            $item_id = array_key_exists('id', $rate) ? $rate['id'] : null;
        }
        
        Rates::updateOrCreate(
        [
            'crms_id' => $item_id,
            'product_id' => $productId
        ],
        [
            'transaction_type' => $rate['transaction_type'],
            'rate_definition_id' => $rate['rate_definition_id'],
            'price' => $rate['price'],
            'crms_id' => $item_id,
            'product_id' => $productId,
            'store_id' => $rate['store_id'] ?? 1, // Default store to 1 if null fix later
        ]
    );
    }

    public function saveImage($icon, $product)
    {
        $productId = $product->id;

        $fileObj = File::where('target_id', $productId)
            ->where('target_type', 'App\Models\Product')
            ->whereNotNull('crms_url')
            ->first();

        if (isset($fileObj)) {
            if ($fileObj->crms_url != $icon['url']) {
                unlink($fileObj->path);
                $fileObj->delete();

                $image = file_get_contents($product->icon);
                list($usec, $sec) = explode(" ", microtime());
                $filename = str_replace('.', '', ((float)$usec + ((float)$sec))) . '.' . 'jpeg';
                \Storage::disk('public')->put('files/'.$filename, $image);

                $file = new File(
                    [
                        'path' => '/public/files/'.$filename,
                        'crms_url' => $icon['url']
                    ]
                );
                $images = $product->image()->save($file);
            }
        } else {
            $image = file_get_contents($product->icon);
            list($usec, $sec) = explode(" ", microtime());
            $filename = str_replace('.', '', ((float)$usec + ((float)$sec))) . '.' . 'jpeg';
            \Storage::disk('public')->put('files/'.$filename, $image);

            $file = new File(
                [
                    'path' => '/public/files/'.$filename,
                    'crms_url' => $icon['url']
                ]
            );
            $images = $product->image()->save($file);
        }
    }

    public function saveAccessories($accessories, $product) {
        if (isset($accessories) && sizeof($accessories) > 0) {
            foreach ($accessories as $accessory) {
                $id = array_key_exists('related_id', $accessory) ? $accessory['related_id'] : null;
                if (!$id) return;
                $productId = $product->id;
                ProductAccessorie::updateOrCreate(
                    [
                        'crms_id' => $id,
                        'product_id' => $productId
                    ],
                    [
                        'product_id' => $productId,
                        'relatable_id' => $accessory['relatable_id'],
                        'relatable_type' => $accessory['relatable_type'],
                        'related_id' => $accessory['related_id'],
                        'related_type' => $accessory['related_type'],
                        'related_name' => $accessory['related_name'],
                        'related_icon_url' => $accessory['related_icon_url'],
                        'related_icon_thumb_url' => $accessory['related_icon_thumb_url'],
                        'type' => $accessory['type'],
                        'parent_transaction_type' => $accessory['parent_transaction_type'],
                        'parent_transaction_type_name' => $accessory['parent_transaction_type_name'],
                        'item_transaction_type' => $accessory['item_transaction_type'],
                        'item_transaction_type_name' => $accessory['item_transaction_type_name'],
                        'inclusion_type' => $accessory['inclusion_type'],
                        'inclusion_type_name' => $accessory['inclusion_type_name'],
                        'mode' => $accessory['mode'],
                        'mode_name' => $accessory['mode_name'],
                        'quantity' => $accessory['quantity'],
                        'zero_priced' => $accessory['zero_priced'],
                        'sort_order' => $accessory['sort_order'],
                        'crms_id' => $accessory['related_id'],
                    ]
                );
            }
        }
    }
}