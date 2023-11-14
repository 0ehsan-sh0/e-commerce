<?php

namespace App\Http\Controllers\Home;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\ProductRate;
use Illuminate\Support\Facades\Validator;

class CommentController extends Controller
{
    public function store(Request $request, Product $product)
    {
        $validator = Validator::make($request->all(), [
            'text' => 'required|min:5|max:700',
            'rate' => 'required|digits_between:0,5'
        ]);

        if ($validator->fails()) {
            return redirect()->to(url()->previous() . '#comments')->withErrors($validator);
        }

        if (auth()->check()) {
            try {
                DB::beginTransaction();
                Comment::create([
                    'user_id' => auth()->id(),
                    'product_id' => $product->id,
                    'text' => $request->text,
                ]);
                $productRate = $product->rates()->where('user_id', auth()->id())->first();
                if ($productRate) {
                    $productRate->update([
                        'rate' => $request->rate
                    ]);
                } else {
                    ProductRate::create([
                        'user_id' => auth()->id(),
                        'product_id' => $product->id,
                        'rate' => $request->rate,
                    ]);
                }
                DB::commit();
                alert()->success('موفق', 'نظر با موفقیت ثبت شد');
                return redirect()->back();
            } catch (\Exception $ex) {
                DB::rollback();
                alert()->error('مشکل در ثبت نظر', $ex->getMessage());
                return redirect()->back();
            }
        } else {
            alert()->warning('دقت کنید', 'برای ثبت نظر لطفا وارد سایت شوید');
            return redirect()->back();
        }
    }

    public function usersProfileIndex()
    {
        $comments = Comment::where('user_id', auth()->id())->where('approved', 1)->get();
        return view('home.users_profile.comments', compact('comments'));
    }
}
