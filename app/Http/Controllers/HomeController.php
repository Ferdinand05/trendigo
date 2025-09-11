<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Http\Resources\ProductResource;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;
use Inertia\Inertia;

use function Laravel\Prompts\error;

class HomeController extends Controller
{
    public function index(Request $request)
    {


        $products = Product::with('category')
            ->whereNot('is_active', 0)
            ->when($request->k, function ($query, $slug) {
                if ($slug !== 'semua') {
                    $category = Category::where('slug', $slug)->first();
                    if ($category) {
                        $query->where('category_id', $category->id);
                    }
                }
            })
            ->when($request->s, function ($query, $keyword) {
                $query->where('name', 'like', "%{$keyword}%");
            })
            ->paginate(12)
            ->withQueryString();


        $new_products = Product::orderBy('created_at', 'desc')->whereNot('is_active', 0)->limit(4)->get();
        return Inertia::render('home/Index', [
            'products' => ProductResource::collection($products),
            'newest_products' => ProductResource::collection($new_products)
        ]);
    }


    public function productInCategory(Request $request, $slug)
    {

        $category = Category::where('slug', $slug)->first();
        if ($category) {
            $products = Product::with('category')
                ->where('category_id', $category->id)
                ->whereNot('is_active', 0)
                ->when($request->k, function ($query, $slug) {
                    if ($slug !== 'semua') {
                        $category = Category::where('slug', $slug)->first();
                        if ($category) {
                            $query->where('category_id', $category->id);
                        }
                    }
                })
                ->when($request->s, function ($query, $keyword) {
                    $query->where('name', 'like', "%{$keyword}%");
                })
                ->paginate(12)
                ->withQueryString();
        }


        $new_products = Product::orderBy('created_at', 'desc')->whereNot('is_active', 0)->limit(4)->get();
        return Inertia::render('home/ProductInCategory', [
            'products' =>  ProductResource::collection($products),
            'category' => $category,
            'newest_products' => ProductResource::collection($new_products)
        ]);
    }

    public function detailProduct(Request $request, $slug)
    {
        $product = Product::where('slug', $slug)->first();
        $category = Category::find($product->category_id);
        if (!$product) {
            return redirect()->intended();
        }

        return Inertia::render('home/DetailProduct', [
            'product' => new ProductResource($product),
            'category' => $category
        ]);
    }
}
