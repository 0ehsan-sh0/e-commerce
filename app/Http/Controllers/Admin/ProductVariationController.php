<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;

class ProductVariationController extends Controller
{
    public function store($variations, $attributeId, $product)
    {
        $counter = count($variations['value']);
        for ($i = 0; $i < $counter; $i++) {
            ProductVariation::create([
                'attribute_id' => $attributeId,
                'product_id' => $product->id,
                'value' => $variations['value'][$i],
                'price' => $variations['price'][$i],
                'quantity' => $variations['quantity'][$i],
                'sku' => $variations['sku'][$i],
            ]);
        }
    }

    public function update($variationValues){
        foreach ($variationValues as $key => $variationValue) {
            // update
            ProductVariation::findOrFail($key)->update([
                'price' => $variationValue['price'],
                'quantity' => $variationValue['quantity'],
                'sku' => $variationValue['sku'],
                'sale_price' => $variationValue['sale_price'],
                'date_on_sale_from' => ShamsiToGregorian($variationValue['date_on_sale_from']),
                'date_on_sale_to' => ShamsiToGregorian($variationValue['date_on_sale_to']),
            ]);
        }
    }

    public function change($variations, $attributeId, $product)
    {
        ProductVariation::where('product_id', $product->id)->delete();
        $counter = count($variations['value']);
        for ($i = 0; $i < $counter; $i++) {
            ProductVariation::create([
                'attribute_id' => $attributeId,
                'product_id' => $product->id,
                'value' => $variations['value'][$i],
                'price' => $variations['price'][$i],
                'quantity' => $variations['quantity'][$i],
                'sku' => $variations['sku'][$i],
            ]);
        }
    }
}
