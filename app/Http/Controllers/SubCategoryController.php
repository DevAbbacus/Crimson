<?php

namespace App\Http\Controllers;

use App\Models\SubCategory;
use Illuminate\Http\Request;
use App\Models\Package;
use Log;
use Auth;
use Redirect;
use Inertia\Inertia;
use App\Models\ProductGroup;
use Illuminate\Http\Request as IRequest;
use DB;
use Route;


class SubCategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(IRequest $request)
    {
        $requestFrom = Route::current()->getName();

        if (isset($requestFrom) && $requestFrom == 'subcategory.index') {
            $response = $this->subCategoryList($request, true, true);
        } else {
            $response = $this->list($request, true, true);
        }
        //echo "<pre>";print_r($response);exit();
        return Inertia::render('ProductSubCategry/list', $response);
    }

    public function ProductGroup(IRequest $request,$paginate = false, $locally = false)
    {
        $result = ProductGroup::ByUser(Auth::id())->get();
        $json = [
            'items' => $paginate ? $result['data'] : $result
        ];
        return $locally ? $json : response()->json($json);
    }

    public function ProductGroupWithSub(IRequest $request,$paginate = false, $locally = false)
    {
        $result = SubCategory::ByUser(Auth::id())->select('id','name')->with('children')->get();

        $result = $result->map(function ($content, $key) {
            $children = $content->children->toArray();
            $content->childrencoutn = $content->children()->exists();

            if(!$content->children()->exists() && $content->parent_id==0){

                $children = SubCategory::whereId($content->id)->get()->toArray();

            } else {

                if(!$content->children()->exists())
                    $children = SubCategory::whereId($content->id)->get()->toArray();

                if($content->parent_id==0){
                    $x = SubCategory::whereId($content->id)->get()->toArray();//->name;
                    $x[0]['name'] = $x[0]['name'].' (self)';
                    $children = array_merge($children, $x);// $x;
                }
            }
            $content->childs = $children;
            return $content;
        });
        $json = [
            'items' => $paginate ? $result['data'] : $result
        ];
        return $locally ? $json : response()->json($json);
    }
    public function subProductGroup(IRequest $request,$paginate = false, $locally = false)
    {

        $result = SubCategory::ByUser(Auth::id())->get();
        $json = [
            'items' => $paginate ? $result['data'] : $result
        ];
        return $locally ? $json : response()->json($json);
    }


    public function store(IRequest $request)
    {
        $body = $request->all();

        $body['user_id'] = Auth::id();
        $body['crms_id'] = 1;
        $body['p_group'] = isset($body['p_group']) ? implode(',', $body['p_group']): null;
        $body['parent_id'] = @$body['parent_id'];//isset($body['parent_id']) ? implode(',', $body['parent_id']): null;

        $created = SubCategory::create($body);
        if ($created) {
            return Redirect::back()->with('success', 'SubCategory created!');
        } else
            return Redirect::back()->with('error', 'Can\'t create package!');
    }

    public function list(IRequest $request, $paginate = false, $locally = false)
    {
        $params = $request->only('search', 'sort', 'blanks', 'groups');
        $paginate = $request->get('paginate', $paginate);
        $columns = $request->get('columns', '*');
        $user = auth()->user();



        $query = SubCategory::list($params)
            ->byUser(Auth::id());

       /* $query = DB::table('product_subgroup')
            ->leftJoin('product_groups', 'id', '=', 'product_subgroup.p_group')
            ->select('product_subgroup.*','product_groups.*')
            ->where($params)
            ->get();*/

            //echo "<pre>";print_r($params);exit();

       $query = DB::table('product_subgroup')
        ->select('product_groups.name AS p_group_name','product_subgroup.*')
        ->leftJoin('product_groups', 'product_subgroup.p_group', '=', 'product_groups.id')
        ->where("product_subgroup.user_id", "=", Auth::id())
        ->orderBy("product_subgroup.id","desc");
        //dd($query);exit();




            //select `product_subgroup`.*, `product_groups`.* from `product_subgroup` inner join `product_groups` on `id` = `product_subgroup`.`p_group`

            /*->join('contacts', 'users.id', '=', 'contacts.user_id')
            ->join('orders', 'users.id', '=', 'orders.user_id')*/
            


        $result = ($paginate ? $query->paginate() : $query->get($columns))->toArray();

        function getPGname($id){
            $terms = explode(',',$id);
             $result_data = ProductGroup::select('name')
                                ->ByUser(Auth::id())
                                ->where(function($result_data) use($terms) {
                                    foreach($terms as $term) {
                                        $result_data->orWhere('id', '=', $term);
                                    };
                                })
                                ->get()
                                ->toArray();
             $cat_data = array();
             foreach ($result_data as $key => $value) {
                   $cat_data[] = $value['name'];
             }
               $return_data = implode(',', $cat_data);
             return $return_data;
         }

        $cat_name = array();

            //echo "<pre>";print_r((array)$result['data']);exit();

        foreach ($result['data'] as $key => $value) {

            $value = (array)$value;
            if (!empty($value['p_group'])) {
               $p_group_name = getPGname($value['p_group']);
        
                $p_group = explode(',', $value['p_group']);
                $groups = [];
                foreach ($p_group as $id) {
                    $groupInfo = ProductGroup::select('name')
                                    ->ByUser(Auth::id())
                                    ->where(function($result_data) use($id) {
                                        $result_data->Where('id', '=', $id);
                                    })
                                    ->first();
                    if ($groupInfo) {
                        $groups[] = ['id' => $id, 'name' => $groupInfo->name];
                    }
                }
        

                /*$result['data'][$key]['p_group_name']= $p_group_name;
                $result['data'][$key]['p_group'] = $groups;*/
            }
            
                //echo "<pre>";print_r($result['data']);exit();
            
            //if (!empty($value['parent_id']) && $value['parent_id'] != 'undefined') {
                $subcates = SubCategory::select(DB::raw("group_concat(name SEPARATOR ', ') as names"))->whereIn('id',explode(',',$value['parent_id']))->first()->toArray();
                if(@$subcates['names'])
                    @$result['data'][$key]->subcats = $subcates['names'];  //array($value['parent_id'])
                else
                    @$result['data'][$key]->subcats = '';
            //}

                //echo "<pre>";print_r($result['data'][$key]);
            

            $result1['data'] = array();
            foreach ($result['data'] as $key => $value) {   
                $value = (array)$value;            
                if ($user->site_url != "https://my-brand.be") {
                    unset($value["name_fr"]);
                    unset($value["name_en"]);
                }
                $result1['data'][] = $value;
            }

            if ($user->site_url != "https://my-brand.be") {
                    $result['data'] = $result1['data'];
            }

            $json = [
            'items' => $paginate ? $result['data'] : $result,
            'params' => count($params) > 0 ? $params : null,
            'site_url' => $user->site_url,
            ];
           // echo "<pre>";print_r($json);exit();
        }
        //exit(); 

                //echo "<pre>";print_r($json);exit();
        if ($paginate)
            $json['pagination'] = $result['links'];

           //echo "<pre>";print_r(response()->json($json));exit();
        return $locally ? $json : response()->json($json);
    }

    public function subCategoryList(IRequest $request, $paginate = false, $locally = false)
    {   
        $params = $request->only('search', 'sort', 'blanks', 'groups');
        $paginate = $request->get('paginate', $paginate);
        $columns = $request->get('columns', '*');
        $user = auth()->user();


        //echo "<pre>";print_r();exit();

        $query = SubCategory::list($params)
            ->byUser(Auth::id())->orderBy('id', 'DESC');

        $result = ($paginate ? $query->paginate() : $query->get($columns))->toArray();
        //echo "<pre>";print_r($result);exit();
        function getPGname($id){
            $terms = explode(',',$id);
             $result_data = ProductGroup::select('name')
                                ->ByUser(Auth::id())
                                ->where(function($result_data) use($terms) {
                                    foreach($terms as $term) {
                                        $result_data->orWhere('id', '=', $term);
                                    };
                                })
                                ->get()
                                ->toArray();
             $cat_data = array();
             foreach ($result_data as $key => $value) {
                   $cat_data[] = $value['name'];
             }
               $return_data = implode(',', $cat_data);
             return $return_data;
         }

        $cat_name = array();
        foreach ($result['data'] as $key => $value) {

            
                    //echo "<pre>";print_r($value);exit();

            $p_group_name = getPGname($value['p_group']);
            $p_group = explode(',', $value['p_group']);
            $groups = [];
            foreach ($p_group as $id) {
                $groupInfo = ProductGroup::select('name')
                                ->ByUser(Auth::id())
                                ->where(function($result_data) use($id) {
                                    $result_data->Where('id', '=', $id);
                                })
                                ->first();
                if ($groupInfo) {
                    $groups[] = ['id' => $id, 'name' => $groupInfo->name];
                }
            }
            $result['data'][$key]['p_group_name'] = $p_group_name;
            $result['data'][$key]['p_group'] = $groups;

            $subcates = SubCategory::select(DB::raw("group_concat(name SEPARATOR ', ') as names"))->whereIn('id',explode(',',$value['parent_id']))->first()->toArray();
            if(@$subcates['names'])
                $result['data'][$key]['subcats'] = $subcates['names'];  //array($value['parent_id'])
            else
                $result['data'][$key]['subcats'] = '';

            $result1['data'] = array();
            foreach ($result['data'] as $key => $value) {               
                if ($user->site_url != "https://o3.mobilegiz.com") {
                    unset($value["name_fr"]);
                    unset($value["name_en"]);
                }
                $result1['data'][] = $value;
            }

            if ($user->site_url != "https://o3.mobilegiz.com") {
                    $result['data'] = $result1['data'];
            }

                //echo "<pre>";print_r($result['data']);exit();
            $json = [
            'items' => $paginate ? $result['data'] : $result,
            'params' => count($params) > 0 ? $params : null,
            'site_url' => $user->site_url,
            ];
        }

        if ($paginate)
            $json['pagination'] = $result['links'];

           //echo "<pre>";print_r(response()->json($json));exit();
        return $locally ? $json : response()->json($json);
    }

    public function update(SubCategory $subcategory, IRequest $request)
    {
        $body = $request->all();
        $body['p_group'] = isset($body['p_group']) ? implode(',', $body['p_group']): null;
        $body['parent_id'] = @$body['parent_id'];//isset($body['parent_id']) ? implode(',', $body['parent_id']): null;
        unset($body['p_group_name']);
        unset($body['subcats']);
        $updated = $subcategory->update($body);

        /*$productsIds = $body['products'];
        if ($productsIds)
            $subCategory->products()->sync($productsIds);
*/

        if ($updated)
            return Redirect::back()->with('success', 'SubCategory updated!');
        else
            return Redirect::back()->with('error', 'Can\'t update subCategory!');
    }

    public function destroy(SubCategory $subcategory, IRequest $request)
    {
        $deleted = $subcategory->delete();
        if ($deleted)
            return Redirect::back()->with('success', 'SubCategory deleted!');
        else
            return Redirect::back()->with('error', 'Can\'t delete subcategory!');
    }
    public function singleCategory($id)
    {
        return SubCategory::whereId($id)->first();
    }
}
