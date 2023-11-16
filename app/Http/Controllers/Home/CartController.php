<?php

namespace App\Http\Controllers\Home;

use Cart;
use App\Models\Product;
use App\Models\Province;
use App\Models\UserAddress;
use Illuminate\Http\Request;
use App\Models\ProductVariation;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    public function index()
    {
        return view('home.cart.index');
    }

    public function add(Request $request)
    {
        $request->validate([
            'product_id' => 'required|integer',
            'qtybutton' => 'required|integer',
        ]);
        $product = Product::findOrFail($request->product_id);
        $productVariation = ProductVariation::findOrFail(json_decode($request->variation)->id);
        if ($request->qtybutton > $productVariation->quantity) {
            alert()->error('دقت کنید', 'محصول در انبار موجود نیست');
            return redirect()->back();
        }

        $rowId = $product->id . '-' . $productVariation->id;
        if (Cart::get($rowId)) {
            alert()->warning('دقت کنید', 'محصول در لیست سبد خرید شما وجود دارد');
            return redirect()->back();
        }
        Cart::add(array(
            'id' => $rowId,
            'name' => $product->name,
            'price' => $productVariation->is_sale ? $productVariation->sale_price : $productVariation->price,
            'quantity' => $request->qtybutton,
            'attributes' => $productVariation->toArray(),
            'associatedModel' => $product
        ));
        alert()->success('با تشکر', 'محصول به لیست خرید اضافه شد');
        return redirect()->back();
    }

    public function update(Request $request)
    {
        $request->validate([
            'qtybutton' => 'required',
        ]);
        foreach ($request->qtybutton as $rowId => $quantity) {
            $item = Cart::get($rowId);

            if ($quantity > $item->attributes->quantity) {
                alert()->error('دقت کنید', 'محصول در انبار موجود نیست');
                return redirect()->back();
            }

            Cart::update($rowId, array(
                'quantity' => array(
                    'relative' => false,
                    'value' => $quantity
                ),
            ));
        }
        alert()->success('عملیات موفق', 'سبد خرید با موفقیت بروزرسانی شد');
        return redirect()->back();
    }

    public function clear()
    {
        Cart::clear();
        alert()->success('عملیات موفق', 'سبد خرید با موفقیت حذف شد');
        return redirect()->back();
    }

    public function remove($rowId)
    {
        Cart::remove($rowId);
        alert()->success('عملیات موفق', 'محصول از سبد خرید حذف شد');
        return redirect()->back();
    }

    public function checkCoupon(Request $request)
    {
        $request->validate([
            'code' => 'required'
        ]);
        if (!auth()->check()) {
            alert()->error('خطا', 'لطفا ابتدا وارد سایت شوید');
            return redirect()->back();
        }
        $result = checkCoupon($request->code);
        if (array_key_exists('error', $result))
            alert()->error('خطا', $result['error']);
        else
            alert()->success('عملیات موفق', $result['success']);
        return redirect()->back();
    }

    public function checkout()
    {
        if (Cart::isEmpty()) {
            alert()->warning('دقت کنید', 'سبد خرید شما خالی است');
            return redirect()->route('home.index');
        }
        $addresses = UserAddress::where('user_id', auth()->id())->get();
        $provinces = Province::all();
        return view('home.cart.checkout', compact('provinces', 'addresses'));
    }
}
