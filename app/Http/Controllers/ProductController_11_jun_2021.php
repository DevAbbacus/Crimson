<?php

namespace App\Http\Controllers;

use App\Clients\CRMSClient;
use Inertia\Inertia;
use Illuminate\Support\Facades\DB;
use App\Models\Product;
use App\Models\ProductSubCategory;
use App\Enums\StockMethod;
use App\Models\ProductGroup;
use App\Enums\AllowedStockType;
use Illuminate\Support\Facades\Auth;
use App\Services\CRMSService;
use Illuminate\Support\Facades\Redirect;
use App\Enums\CostGroup;
use App\Enums\RateDefinition;
use App\Enums\RevenueGroup;
use App\Jobs\ProcessSync;
use App\Models\AlternativeProducts;
use App\Models\Rates;
use App\Models\SubCategory;
use Exception;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request as IRequest;
use Log;

class ProductController extends Controller
{
    private $crmsClient;
    private $customFields;

    public function __construct()
    {
        $this->crmsClient = new CRMSClient();
        $this->customFields = ['product_height_mm', 'product_width_mm','weight_kg','specname_1', 'specvalue_1','specname_2', 'specvalue_2','specname_3', 'specvalue_3','specname_4', 'specvalue_4','specname_5', 'specvalue_5','specname_6', 'specvalue_6','colour_temperature', 'power_type', 'output_at_8m', 'output_at_5m', 'output_at_2m', 'power_input_watts','optional_accessory_1', 'optional_accessory_2', 'optional_accessory_3', 'optional_accessory_4', 'discount_start', 'discount_end', 'discount_percentage','discount_amount','lighthouse_category','alternate_search_terms', 'when_booked_separately', 'lighthouse_sort_order','usability','gaffer_tips','gaffer_notes', 'prep_tasks', 'post_tasks'];
    }

    public function index(IRequest $request)
    {

        $response = $this->list($request, true, true);
        return Inertia::render('Dashboard', array_merge($response, [
            'productGroups' => ProductGroup::ByUser(Auth::id())->get(),
            'allowedStockTypes' => AllowedStockType::all(),
            'stockMethodTypes' => StockMethod::all(),
            'rateDefinitions' => RateDefinition::all(),
            'revenueGroups' => RevenueGroup::all(),
            'costGroups' => CostGroup::all()
        ]));
    }



    public function update(IRequest $request, Product $product)
    {
        $user = Auth::user();

        $p_group = json_encode($request->p_group);

        $subp_group = isset($p_group) ? $p_group : "" ;

        $productGroup = ProductGroup::findOrFail($request['product_group_id']);
        $input = [
            'name' => $request->name,
            'description' => $request->description,
            'weight' => $request->weight,
            'purchase_price' => $request->purchase_price,
            'sub_rental_price' => $request->sub_rental_price,
            'replacement_charge' => $request->replacement_charge,
            'notes' => $request->notes,
            'custom_fields' => json_decode($request->custom_fields, true),
            'stock_method' => (int) $request->stock_method,
            'allowed_stock_type' => (int) $request->allowed_stock_type,
            'product_group_id' => (int) $request->product_group_id,
            'sale_revenue_group_id' => (int) $product->sale_revenue_group_id,
            'purchase_cost_group_id' => (int) $product->purchase_cost_group_id
        ];
        $updated = $product->update($input);

        // Update Image
        $product->load('image');
        if ($request->hasFile('files')) {
            $cond = [];
            foreach ($request->all()['files'] as $file) {
                $product->image()->create(['path' => $file]);
            }
            $product->refresh();
        }
            $custom_fields_data = json_decode($request['custom_fields'], true);
                 
       /* $test = DB::table('custom_fields')->where('fieldable_id', $product->id)->where('fieldable_type', 'Product')->update($custom_fields_data);*/
          //echo "<pre>";print_r($test);exit();

        // SYNC ALTERNATIVES
        $alternativeProducts = $request->get('alternative_products', []);
        $aIds = array_map(function ($obj) {
            return (int)$obj;
        }, $alternativeProducts);
        $dcode_data = array();
        foreach ($alternativeProducts as $key => $value) {
                $d= json_decode($value);
                $dcode_data[] = $d->value;
        }

        $AProducts = AlternativeProducts::select('id')
                                ->where('relatable_id', '=', $product->id)
                                ->get()
                                ->toArray();
        foreach ($AProducts as $AP) {
            $AlProducts = AlternativeProducts::find($AP['id']);
            $AlProducts->delete();
        }
        foreach ($dcode_data as $id) {
            AlternativeProducts::create([
                'related_id' => $id,
                'relatable_id' => $product->id
            ]);
        }

        // SYNC Subcategory
        $p_group = $request->get('p_group', []);
        $aIds = array_map(function ($obj) {
            return (int)$obj;
        }, (array)$p_group);
        $groups = ProductSubCategory::select('id')
                                ->where('sub_id', '=', $product->id)
                                ->get()
                                ->toArray();
        foreach ($groups as $group) {
            $productSubCategory = ProductSubCategory::find($group['id']);
            $productSubCategory->delete();
        }
        foreach ($aIds as $id) {
            ProductSubCategory::create([
                'relatable_id' => $id,
                'sub_id' => $product->id
            ]);
        }

        $rates = $request->rates;
        $redirect = Redirect::back()->with('success', 'Product updated.');
        if ($updated && $product->crms_id) {
            try {
                $credentials = [
                    'subdomain' => $user->sub_domain,
                    'key' => $user->api_token
                ];
                $input['product_group_id'] = (int) ($productGroup->crms_id);
                // dd($input);
                unset($input['image']);
                $body = ['product' => $input];
                $this->crmsClient->setCredentials($credentials);
                // dd($body);
                $this->crmsClient->put("products/{$product->crms_id}", $body);
                if ($rates !== null && count($rates) > 0) {
                    foreach ($rates as $rate) {
                        $rate = json_decode($rate, true);
                        $rateId = $rate['crms_id'];
                        $rateBody = [
                            "store_id" => $rate['store_id'],
                            "transaction_type" => $rate['transaction_type'],
                            "rate_definition_id" => $rate['rate_definition_id'],
                            "price" => $rate['price']
                        ];
                        $rateUpdated = Rates::find($rate['id'])->update($rate);
                        if ($rateUpdated)
                            $this->crmsClient->put("products/{$product->crms_id}/rates/{$rateId}", ['rate' => $rateBody]);
                    }
                }
            } catch (ClientException $e) {
                Log::info($e->getMessage());
                $errorBody = json_decode($e->getResponse()->getBody(true), true);
                $key = array_key_first($errorBody['errors']);
                $errorMessage = $errorBody['errors'][$key];
                if (is_array($errorMessage)) {
                    $errorMessage = array_shift($errorMessage);
                    $errorMessage = "'$key' {$input[$key]} $errorMessage";
                }
                $redirect->with('error', "CurrentRMS ERROR: $errorMessage");
            }
        }
        return $redirect;
    }

    public function sync()
    {
        try {
            ProcessSync::dispatch(Auth::user());
            return Redirect::back()->with('success', 'Sync success!');
        } catch (Exception $e) {
            return Redirect::back()->with('error', 'Sync Error!');
        }
    }

    public function start_insert_after( $array, $key, $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;
        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }

    public function end_insert_after( $array, $key, $new ) {
        $keys = array_keys( $array );
        $index = array_search( $key, $keys );
        $pos = false === $index ? count( $array ) : $index + 1;
        return array_merge( array_slice( $array, 0, $pos ), $new, array_slice( $array, $pos ) );
    }

    

    public function list(IRequest $request, $paginate = true, $locally = false)
    {
        $paginate = $request->get('paginate', $paginate);
        $params = $request->only('search', 'sort', 'blanks', 'groups');
        $columns = $request->get('columns', '*');
        $blankCustomFields = array();
        if($params){
            if(isset($params['blanks'])){
                $blank  = $params['blanks'];
                $model = new Product();
                $tableColumn = $model->getFillable();
                $blankCustomFields  = array_diff($params['blanks'], $tableColumn);
                $blankProductFields = array_diff($params['blanks'], $blankCustomFields);
                $params['blanks']   = $blankProductFields;
            }
        }

        $auth_detail = DB::table('users')->select('user_type')->where('id',Auth::id())->first();
        $user_type = $auth_detail->user_type;

        if ($user_type == "w") {
            $user_type = "Wordpress";
        }else {
            $user_type = "Magento";            
        }

        $query = Product::list($params, Auth::id(), $blankCustomFields)->with('customFieldsMorph');

        $result = $paginate ? $query->paginate() : $query->get($columns);
        $custom_field_columns = [];


        $result = $result->transform(function ($product) use (&$custom_field_columns) {
            $custom_fields = $product->customFields();
            $custom_field_names = $custom_fields->pluck('field_name');
            /*if($custom_fields->isEmpty()){
                DB::table('custom_fields')->insert(['fieldable_id' => $product->id,'fieldable_type' => 'Product']);
                $custom_fields = $product->customFields();
            }*/
            $icon = '';
            if(count($custom_field_names) > count($custom_field_columns))
                $custom_field_columns = $custom_field_names;

            $images = [];
            /*if($custom_fields->isEmpty()){
                DB::table('custom_fields')->insert(['fieldable_id' => $product->id,'fieldable_type' => 'Product']);
                $custom_fields = $product->customFields();
            }*/

                //echo "<pre>";print_r($product);exit();

                //echo "<pre>";print_r($product->image);exit();

            foreach ($product->image as $image) {
                $imagePath = env('APP_URL').'/storage'.$image->path;
                $imagePath = str_replace('/public', '', $imagePath);
                $images[] = ['id' => $image->id, 'path' => $imagePath ];
            }
            $p_group = ProductSubCategory::select('relatable_id')
                                ->where('sub_id', '=', $product->id)
                                ->get()
                                ->toArray();

            $groups = [];
            if (is_array($p_group)) {
                foreach ($p_group as $id) {
                    $groupInfo = SubCategory::select(['id', 'name'])
                                    ->ByUser(Auth::id())
                                    ->where(function($result_data) use($id) {
                                        $result_data->Where('id', '=', $id['relatable_id']);
                                    })
                                    ->first();
                    if ($groupInfo) {
                        $groups[$groupInfo->id] = ['id' => $groupInfo->id, 'name' => $groupInfo->name];
                    }
                }
                $groups = array_values($groups);
            }

            $defult_start_date = array('wp_product_start_date' => '2020-01-01');
            $defult_end_date = array('wp_product_end_date' => '2030-12-31');

            


            if (!array_key_exists("wp_product_end_date", $product->custom_fields))
            {
                 $product->custom_fields = $this->start_insert_after($product->custom_fields,'webshop_category_iv',$defult_end_date);
            }

            if (!array_key_exists("wp_product_start_date", $product->custom_fields))
            {
                  
                $product->custom_fields = $this->start_insert_after($product->custom_fields,'published_on_my_brand',$defult_start_date); 
            }
            

            
                //echo "<pre>";print_r($product->icon);exit();
            return [
                'id' => $product->id,
                'name' => $product->name,
                'description' => $product->description,
                'stock_method' => $product->stock_method,
                'weight' => $product->weight,
                'product_group_id' => $product->product_group_id,
                'purchase_price' => $product->purchase_price,
                'sub_rental_price' => $product->sub_rental_price,
                'allowed_stock_type' => $product->allowed_stock_type,
                'replacement_charge' => $product->replacement_charge,
                'notes' => $product->notes,
                
                // 'accessories' => $product->accessories->isEmpty() ? 'No' : 'Yes',
                'alternative_products' => $product->alternativeProducts,
                'p_group' => $groups,
                'custom_fields' => $product->custom_fields,
                'rates' => $product->rates,
                'icon' => $product->icon,
                'images' => $images,
            ];
        });
        $result = $result->toArray();
        $params['blanks'] = @array_merge($blankCustomFields, $blankProductFields);
        $json = [
            'items' => $paginate ? $result['data'] : $result,
            'params' => count($params) > 0 ? $params : null,
            'custom_field_columns' => Product::where('user_id',Auth::id())->first()->custom_fields,
            'user_type' => $user_type
        ];


        if ($paginate)
            $json['pagination'] = $result['links'];

        return $locally ? $json : response()->json($json);
    }
    public function deleteImage(IRequest $request) {
        $deleted = false;
        $product = Product::find($request->product_id);
        foreach ($product->image as $image) {
            if ($request->image_id == $image->id) {
                $deleted = $image->delete();
            }
        }
        if ($deleted) {
            return Redirect::back()->with('success', 'Image deleted!');
        } else {
            return Redirect::back()->with('error', 'Can\'t delete Image!');
        }
    }
}
