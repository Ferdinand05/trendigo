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
    public function index(Request $request)
    {

        $categories = Category::query()
            ->when($request->search, function ($query, $keyword) {
                $query
                    ->where('name', 'like', "%{$keyword}%")
                    ->orWhere('description', 'like', "%{$keyword}%");
            })
            ->paginate(10)
            ->withQueryString();
        return Inertia::render('categories/Index', ['categories' => CategoryResource::collection($categories)]);
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
