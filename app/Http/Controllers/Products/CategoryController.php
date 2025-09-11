<?php

namespace App\Http\Controllers\Products;

use Inertia\Inertia;
use App\Models\Category;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use Illuminate\Support\Facades\Session;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = CategoryResource::collection(Category::with('products')->get());
        return Inertia::render('categories/Index', ['categories' => $categories]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'min:3'],
            'description' => ['required', 'string', 'min:5']
        ]);

        $slug = Str::slug($request->name);
        $validated['slug'] = $slug;

        Category::create($validated);
        Session::flash('message', 'New category has been created.');
    }

    public function update(Request $request, $id)
    {

        $category = Category::find($id);

        if ($category) {

            $category->update([
                'name' => $request->name,
                'description' => $request->description,
                'slug' => Str::slug($request->name)
            ]);


            Session::flash('message', 'Category has been updated.');
        }
    }

    public function destroy(Category $category)
    {
        $category->delete();

        Session::flash('message', 'Category has been deleted.');
    }
}
