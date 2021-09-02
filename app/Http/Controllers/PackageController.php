<?php

namespace App\Http\Controllers;

use App\Models\Package;
use Inertia\Inertia;
use Log;
use Auth;
use Redirect;
use Illuminate\Http\Request as IRequest;

class PackageController extends Controller
{
    public function index(IRequest $request)
    {
        $response = $this->list($request, true, true);
        return Inertia::render('Package/Index', $response);
    }

    public function store(IRequest $request)
    {
        //$body = $request->all();
        $body = $request->except('products');
        $body['user_id'] = Auth::id();
        $request->validate(['image' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $path = $request->image->storeAs('images', $imageName);
            $body['image'] = "/$path";
        }

        $created = Package::create($body);
        $products = $request->products;
        if ($created) {
            //$productsIds = isset($body['products']) ? $body['products'] : [];
            $productsIds = isset($products) ? $products : [];
            $created->products()->sync($productsIds);
            return Redirect::back()->with('success', 'Package created!');
        } else
            return Redirect::back()->with('error', 'Can\'t create package!');
    }

    public function update(Package $package, IRequest $request)
    {
        $body = $request->all();
        if ($request->hasFile('image')) {
            $imageName = time() . '.' . $request->image->extension();
            $path = $request->image->storeAs('images', $imageName);
            $body['image'] = "/$path";
        }
        $updated = $package->update($body);
        $productsIds = $body['products'];
        if ($productsIds)
            $package->products()->sync($productsIds);


        if ($updated)
            return Redirect::back()->with('success', 'Package updated!');
        else
            return Redirect::back()->with('error', 'Can\'t update package!');
    }

    public function destroy(Package $package, IRequest $request)
    {
        $deleted = $package->delete();
        if ($deleted)
            return Redirect::back()->with('success', 'Package deleted!');
        else
            return Redirect::back()->with('error', 'Can\'t delete package!');
    }

    public function list(IRequest $request, $paginate = false, $locally = false)
    {
        $params = $request->only('search', 'sort', 'blanks', 'groups');
        $paginate = $request->get('paginate', $paginate);
        $columns = $request->get('columns', '*');

        $query = Package::list($params)
            ->byUser(Auth::id());

        $result = ($paginate ? $query->paginate() : $query->get($columns))->toArray();
        $json = [
            'items' => $paginate ? $result['data'] : $result,
            'params' => count($params) > 0 ? $params : null,
        ];

        if ($paginate)
            $json['pagination'] = $result['links'];

        return $locally ? $json : response()->json($json);
    }
}
