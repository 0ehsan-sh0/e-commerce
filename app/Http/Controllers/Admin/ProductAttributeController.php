<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProductAttribute;
use App\Http\Controllers\Controller;

class ProductAttributeController extends Controller
{
    public function store($attributes , $product) {

        foreach ($attributes as $key => $attribute) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_id' => $key,
                'value' => $attribute
            ]);
        }
    }

    public function update($attributeValues){
        foreach ($attributeValues as $key => $attributeValue) {
            ProductAttribute::findOrFail($key)->update([
                'value' => $attributeValue
            ]);
        }
    }

    public function change($attributes , $product){
        ProductAttribute::where('product_id', $product->id)->delete();
        foreach ($attributes as $key => $attribute) {
            ProductAttribute::create([
                'product_id' => $product->id,
                'attribute_id' => $key,
                'value' => $attribute
            ]);
        }
    }
}
