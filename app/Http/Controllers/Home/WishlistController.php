<?php

namespace App\Http\Controllers\Home;

use App\Models\Product;
use App\Models\Wishlist;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WishlistController extends Controller
{
    public function add(Product $product)
    {
        if (auth()->check()) {
            if ($product->checkUserWishlist(auth()->id())) {
                alert()->warning('دقت کنید', 'محصول در علاقه مندی ها وجود دارد');
                return redirect()->back();
            } else {
                Wishlist::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id
                ]);

                alert()->success('با تشکر', 'محصول مورد نظر به لیست علاقه مندی ها اضافه شد');
                return redirect()->back();
            }
        } else {
            alert()->warning('دقت کنید', 'برای اضافه کردن به علاقه مندی ها ابتدا وارد سایت شوید');
            return redirect()->back();
        }
    }

    public function remove(Product $product)
    {
        if (auth()->check()) {
            Wishlist::where('product_id', $product->id)->where('user_id', auth()->id())->delete();
            alert()->success('با تشکر', 'محصول مورد نظر از لیست علاقه مندی ها حذف شد');
            return redirect()->back();
        } else {
            alert()->warning('دقت کنید', 'برای حذف از علاقه مندی ها ابتدا وارد سایت شوید');
            return redirect()->back();
        }
    }

    public function usersProfileIndex()
    {
        $wishlist = Wishlist::where('user_id', auth()->id())->get();
        return view('home.users_profile.wishlist', compact('wishlist'));
    }
}
