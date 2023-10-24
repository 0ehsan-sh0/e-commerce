<?php

namespace App\Http\Controllers\Admin;

use App\Models\Category;
use App\Models\Attribute;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $categories = Category::latest()->paginate(20);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $parentCategories = Category::where('parent_id', 0)->get();
        $attributes = Attribute::all();
        return view('admin.categories.create', compact('attributes', 'parentCategories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug',
            'is_active' => 'required|boolean',
            'parent_id' => 'required|numeric',
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'exists:attributes,id',
            'attribute_is_filter_ids' => 'required|array',
            'attribute_is_filter_ids.*' => 'exists:attributes,id',
            'variation_id' => 'required|numeric|exists:attributes,id'
        ]);
        try {
            DB::beginTransaction();
            $category = Category::create([
                'name' => $request->name,
                'slug' => $request->slug,
                'is_active' => $request->is_active,
                'parent_id' => $request->parent_id,
                'icon' => $request->icon,
                'description' => $request->description
            ]);

            foreach ($request->attribute_ids as $index => $attributeId) {
                $category->attributes()->attach($attributeId, [
                    'is_filter' => in_array($attributeId, $request->attribute_is_filter_ids),
                    'is_variation' => $attributeId === $request->variation_id
                ]);
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ایجاد دسته بندی', $ex->getMessage())->persistent('حله');
            return redirect()->back();
        }

        alert()->success('موفق', 'دسته بندی با موفقیت ثبت شد');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Display the specified resource.
     */
    public function show(Category $category)
    {
        return view('admin.categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Category $category)
    {
        $parentCategories = Category::where('parent_id', 0)->get();
        $attributes = Attribute::all();
        return view('admin.categories.edit', compact('category', 'parentCategories', 'attributes'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Category $category)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'required|string|max:255|unique:categories,slug,' . $category->id,
            'is_active' => 'required|boolean',
            'parent_id' => 'required|numeric',
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'exists:attributes,id',
            'attribute_is_filter_ids' => 'required|array',
            'attribute_is_filter_ids.*' => 'exists:attributes,id',
            'variation_id' => 'required|numeric|exists:attributes,id'
        ]);
        try {
            DB::beginTransaction();
            $category->update([
                'name' => $request->name,
                'slug' => $request->slug,
                'is_active' => $request->is_active,
                'parent_id' => $request->parent_id,
                'icon' => $request->icon,
                'description' => $request->description
            ]);

            $category->attributes()->sync($request->attribute_ids);
            foreach ($request->attribute_ids as $attributeId) {
                $category->attributes()->updateExistingPivot($attributeId, [
                    'is_filter' => in_array($attributeId, $request->attribute_is_filter_ids),
                    'is_variation' => $attributeId === $request->variation_id
                ]);
            }

            $category->save();
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ویرایش دسته بندی', $ex->getMessage());
            return redirect()->back();
        }

        alert()->success('موفق', 'دسته بندی با موفقیت ویرایش شد');
        return redirect()->route('admin.categories.index');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Category $category)
    {
        //
    }
}
