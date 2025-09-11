<?php

namespace App\Http\Controllers\Products;

use Inertia\Inertia;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProductResource;
use App\Models\ProductImages;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;

use function Laravel\Prompts\alert;
use function Laravel\Prompts\error;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {


        $products = Product::with('category')
            ->orderBy('created_at', 'desc')
            ->when($request->search, function ($query, $search) {
                $query->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            })
            ->when($request->status, function ($query, $status) {
                $is_active = $status === 'active' ? 1 : 0;
                $query->where('is_active', $is_active);
            })
            ->paginate(6)
            ->withQueryString();


        return Inertia::render(
            'products/Index',
            [
                'products' => ProductResource::collection($products),
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return Inertia::render('products/Create', ['categories' => Category::all()]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_images' => ['required', 'max:3'],
            'product_images.*' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:2080'],
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['numeric', 'required'],
            'weight' => ['numeric', 'required'],
            'stock' => ['numeric', 'required'],
        ]);

        $product = Product::create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);


        foreach ($request->file('product_images') as $image) {
            $path =  $image->store('product_images', 'public');

            $product->images()->create([
                'image_path' => $path
            ]);
        }

        return redirect()->back()->with('message', 'Product created successfully');
    }

    // /**
    //  * Display the specified resource.
    //  */
    // public function show(string $id)
    // {
    //     //
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        return Inertia::render(
            'products/Edit',
            [
                'product' => Product::with(['images', 'category'])->whereId($id)->first(),
                'categories' => Category::all()
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {


        $request->validate([
            'product_images' => ['max:3'],
            'product_images.*' => ['image', 'mimes:png,jpg,jpeg,webp', 'max:2080'],
            'name' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['required', 'string'],
            'price' => ['numeric', 'required'],
            'weight' => ['numeric', 'required'],
            'stock' => ['numeric', 'required'],
        ]);

        $product = Product::with('images')->find($id);
        $product->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'price' => $request->price,
            'weight' => $request->weight,
            'stock' => $request->stock,
            'category_id' => $request->category_id,
        ]);




        // jika ada files baru di upload
        if ($request->file('product_images') !== null) {

            $productImages = ProductImages::where('product_id', $id)->get();

            // delete old images
            foreach ($productImages as $oldImage) {
                Storage::disk('public')->delete($oldImage->image_path);
                $oldImage->delete();
            }

            // create images baru
            foreach ($request->file('product_images') as $image) {
                $path =  $image->store('product_images', 'public');

                $product->images()->create([
                    'image_path' => $path
                ]);
            }
        }

        return redirect()->back()->with('message', 'Product updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $product = Product::with('images')->find($id);
        if ($product) {
            foreach ($product->images as $value) {
                Storage::disk('public')->delete($value->image_path);
                $value->delete();
            }

            $product->delete();

            Session::flash('message', 'Product has been deleted.');
        } else {
            error('Product not found');
        }
    }


    public function updateStatus(Request $request)
    {
        $request->validate([
            'id' => ['required'],
            'is_active' => ['required', 'boolean']
        ]);

        $is_active = $request->is_active == true ? 1 : 0;
        $product = Product::where('id', $request->id)
            ->update([
                'is_active' => $is_active
            ]);

        Session::flash('message', 'Product status has been updated');
    }
}
