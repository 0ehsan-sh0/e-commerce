<?php

namespace App\Http\Controllers\Admin;

use App\Models\Tag;
use App\Models\Brand;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Admin\ProductImageController;
use App\Http\Controllers\Admin\ProductVariationController;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $products = Product::latest()->paginate(20);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $brands = Brand::all();
        $tags = Tag::all();
        $categories = Category::where('parent_id', '!=', 0)->get();
        return view('admin.products.create', compact('brands', 'tags', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'is_active' => 'required|boolean',
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id',
            'description' => 'required',
            'primary_image' => 'required|mimes:jpg,jpeg,png,svg|max:1024',
            'images' => 'required|array',
            'images.*' => 'mimes:jpg,jpeg,png,svg|max:1024',
            'category_id' => 'required|exists:categories,id',
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'required',
            'variation_values' => 'required|array',
            'variation_values.*' => 'required|array',
            'variation_values.value.*' => 'required|string|max:255',
            'variation_values.sku.*' => 'required|string|max:255',
            'variation_values.quantity.*' => 'required|integer',
            'variation_values.price.*' => 'required|integer',
            'delivery_amount' => 'required|integer',
            'delivery_amount_per_product' => 'nullable|integer',
        ]);
        try {
            DB::beginTransaction();
            $ProductImageController = new ProductImageController();
            $fileNameImages = $ProductImageController->upload($request->primary_image, $request->images);

            $product = Product::create([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'category_id' => $request->category_id,
                'primary_image' => $fileNameImages['fileNamePrimaryImage'],
                'description' => $request->description,
                'is_active' => $request->is_active,
                'delivery_amount' => $request->delivery_amount,
                'delivery_amount_per_product' => $request->delivery_amount_per_product,
            ]);

            foreach ($fileNameImages['fileNameImages'] as $imageName) {
                ProductImage::create([
                    'product_id' => $product->id,
                    'image' => $imageName,
                ]);
            }

            $ProductAttributeController = new ProductAttributeController();
            $ProductAttributeController->store($request->attribute_ids, $product);

            $attributeId = Category::find($request->category_id)->attributes()->wherePivot('is_variation', 1)->first()->id;
            $ProductVariationController = new ProductVariationController();
            $ProductVariationController->store($request->variation_values, $attributeId, $product);

            $product->tags()->attach($request->tag_ids);
            DB::commit();
            alert()->success('موفق', 'محصول با موفقیت ایجاد شد');
            return redirect()->route('admin.products.index');
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ایجاد محصول', $ex->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Product $product)
    {
        $productAttributes = $product->attributes()->with('attribute')->get();
        $productVariations = $product->variations;
        $productImages = $product->images;
        $tags = $product->tags;
        return view(
            'admin.products.show',
            compact('product', 'productAttributes', 'productVariations', 'productImages', 'tags')
        );
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Product $product)
    {
        $brands = Brand::all();
        $tags = Tag::all();
        $productAttributes = $product->attributes()->with('attribute')->get();
        $productVariations = $product->variations;
        return view('admin.products.edit', compact('product', 'brands', 'tags', 'productAttributes', 'productVariations'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Product $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'brand_id' => 'required|exists:brands,id',
            'is_active' => 'required|boolean',
            'tag_ids' => 'required|array',
            'tag_ids.*' => 'exists:tags,id',
            'description' => 'required',
            'attribute_values' => 'required|array',
            'attribute_values.*' => 'required|string|max:255',
            'variation_values' => 'required|array',
            'variation_values.*' => 'required|array',
            'variation_values.*.price' => 'required|integer',
            'variation_values.*.quantity' => 'required|integer',
            'variation_values.*.sku' => 'required|string|max:255',
            'variation_values.*.sale_price' => 'nullable|integer',
            'variation_values.*.date_on_sale_from' => 'nullable|date',
            'variation_values.*.date_on_sale_to' => 'nullable|date',
            'delivery_amount' => 'required|integer',
            'delivery_amount_per_product' => 'nullable|integer',
        ]);
        try {
            DB::beginTransaction();

            $product->update([
                'name' => $request->name,
                'brand_id' => $request->brand_id,
                'description' => $request->description,
                'is_active' => $request->is_active,
                'delivery_amount' => $request->delivery_amount,
                'delivery_amount_per_product' => $request->delivery_amount_per_product,
            ]);

            $ProductAttributeController = new ProductAttributeController();
            $ProductAttributeController->update($request->attribute_values);

            $ProductVariationController = new ProductVariationController();
            $ProductVariationController->update($request->variation_values);

            $product->tags()->sync($request->tag_ids);
            DB::commit();
            alert()->success('موفق', 'محصول با موفقیت ویرایش شد');
            return redirect()->route('admin.products.index');
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ویرایش محصول', $ex->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Edit category and variation values.
     */
    public function editCategory(Product $product)
    {
        $categories = Category::where('parent_id', '!=', 0)->get();
        return view('admin.products.edit_category', compact('product', 'categories'));
    }

    /**
     * Update category and variation values.
     */
    public function updateCategory(Request $request, Product $product)
    {
        $request->validate([
            'category_id' => 'required|exists:categories,id',
            'attribute_ids' => 'required|array',
            'attribute_ids.*' => 'required',
            'variation_values' => 'required|array',
            'variation_values.*' => 'required|array',
            'variation_values.value.*' => 'required|string|max:255',
            'variation_values.sku.*' => 'required|string|max:255',
            'variation_values.quantity.*' => 'required|integer',
            'variation_values.price.*' => 'required|integer',
        ]);
        try {
            DB::beginTransaction();

            $product->update([
                'category_id' => $request->category_id,
            ]);

            $ProductAttributeController = new ProductAttributeController();
            $ProductAttributeController->change($request->attribute_ids, $product);

            $attributeId = Category::find($request->category_id)->attributes()->wherePivot('is_variation', 1)->first()->id;
            $ProductVariationController = new ProductVariationController();
            $ProductVariationController->change($request->variation_values, $attributeId, $product);
            DB::commit();
            alert()->success('موفق', 'محصول با موفقیت ایجاد شد');
            return redirect()->route('admin.products.index');
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در ایجاد محصول', $ex->getMessage());
            return redirect()->back();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Product $product)
    {

    }

}
