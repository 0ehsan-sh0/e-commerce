<?php

namespace App\Http\Controllers\Admin;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\File;

class ProductImageController extends Controller
{
    public function removeImage($image)
    {
        if (File::exists(public_path(env('PRODUCT_IMAGES_UPLOAD_PATH') . $image))) {
            File::delete(public_path(env('PRODUCT_IMAGES_UPLOAD_PATH') . $image));
        } else {
            return redirect()->back()->withErrors(['imageError' => 'تصویر مورد نظر یافت نشد']);
        }
    }

    public function upload($primaryImage, $images)
    {
        $fileNameImagePrimary = generateFileName($primaryImage);
        $primaryImage->move(
            public_path(env('PRODUCT_IMAGES_UPLOAD_PATH')),
            $fileNameImagePrimary
        );

        $fileNameImages = [];
        foreach ($images as $image) {
            $fileNameImage = generateFileName($image);
            $image->move(
                public_path(env('PRODUCT_IMAGES_UPLOAD_PATH')),
                $fileNameImage
            );

            array_push($fileNameImages, $fileNameImage);
        }
        return [
            'fileNamePrimaryImage' => $fileNameImagePrimary,
            'fileNameImages' => $fileNameImages
        ];
    }

    public function edit(Product $product)
    {
        return view('admin.products.edit_images', compact('product'));
    }

    public function destroy(ProductImage $image)
    {
        $this->removeImage($image->image);
        ProductImage::destroy($image->id);
        alert()->success('موفق', 'عکس با موفقیت حذف شد');
        return redirect()->back();
    }

    public function setPrimary(Request $request, Product $product)
    {
        $request->validate([
            'image_id' => 'required|exists:product_images,id',
        ]);

        $productImage = ProductImage::findOrFail($request->image_id);
        $primaryImage = $product->primary_image;
        $product->update([
            'primary_image' => $productImage->image
        ]);
        $productImage->update([
            'image' => $primaryImage
        ]);
        alert()->success('موفق', 'تصویر اصلی با موفقیت تغییر کرد');
        return redirect()->back();
    }

    public function add(Request $request, Product $product)
    {
        $request->validate([
            'primary_image' => 'nullable|mimes:jpg,jpeg,png,svg|max:1024',
            'images.*' => 'nullable|mimes:jpg,jpeg,png,svg|max:1024',
        ]);

        if (!$request->primary_image && !$request->images) {
            return redirect()->back()->withErrors(['msg' => 'تصویر یا تصاویر محصول الزامی است']);
        }
        try {
            DB::beginTransaction();

            if ($request->has('primary_image')) {
                $this->removeImage($product->primary_image);
                $fileNameImagePrimary = generateFileName($request->primary_image);
                $request->primary_image->move(
                    public_path(env('PRODUCT_IMAGES_UPLOAD_PATH')),
                    $fileNameImagePrimary
                );
                $product->update([
                    'primary_image' => $fileNameImagePrimary
                ]);
            }

            if ($request->has('images')) {
                foreach ($request->images as $image) {
                    $fileNameImage = generateFileName($image);
                    $image->move(
                        public_path(env('PRODUCT_IMAGES_UPLOAD_PATH')),
                        $fileNameImage
                    );

                    ProductImage::create([
                        'product_id' => $product->id,
                        'image' => $fileNameImage,
                    ]);
                }
            }
            DB::commit();
        } catch (\Exception $ex) {
            DB::rollback();
            alert()->error('مشکل در اضافه کردن عکس های محصول', $ex->getMessage());
            return redirect()->back();
        }
        alert()->success('موفق', 'تصویر اصلی با موفقیت تغییر کرد');
        return redirect()->back();
    }
}
