<?php

namespace App\Http\Controllers\Home;

use Illuminate\Http\Request;
use App\Models\ProductVariation;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Models\Coupon;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Transaction;
use App\PaymentGateway\Pay;
use App\PaymentGateway\Zarinpal;
use Illuminate\Support\Facades\Validator;

class PaymentController extends Controller
{
    public function payment(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'address_id' => 'required',
            'payment_method' => 'required',
        ]);

        if ($validator->fails()) {
            alert()->error('انتخاب آدرس تحویل سفارش الزامی می باشد', 'دقت کنید');
            return redirect()->back();
        }

        $checkCart = $this->checkCart();
        if (array_key_exists('error', $checkCart)) {
            alert()->error($checkCart['error'], 'دقت کنید');
            return redirect()->route('home.index');
        }

        $amounts = $this->getAmounts();
        if (array_key_exists('error', $amounts)) {
            alert()->error($amounts['error'], 'دقت کنید');
            return redirect()->route('home.index');
        }

        if ($request->payment_method == 'pay') {
            $payGateway = new Pay();
            $payGatewayResult = $payGateway->send($amounts, $request->address_id);
            if (array_key_exists('error', $payGatewayResult)) {
                alert()->error('دقت کنید', $payGatewayResult['error']);
                return redirect()->back();
            } else {
                return redirect()->to($payGatewayResult['success']);
            }
        } else if ($request->payment_method == 'zarinpal') {
            $zarinpalGateway = new Zarinpal();
            $zarinpalGatewayResult = $zarinpalGateway->send($amounts, 'خرید تستی', $request->address_id);
            if (array_key_exists('error', $zarinpalGatewayResult)) {
                alert()->error($zarinpalGatewayResult['error'], 'دقت کنید');
                return redirect()->back();
            } else {
                return redirect()->to($zarinpalGatewayResult['success']);
            }
        }

        alert()->error('دقت کنید', 'درگاه پرداختی انتخابی اشتباه میباشد');
        return redirect()->back();
    }

    public function paymentVerify(Request $request, $gatewayName)
    {
        if ($gatewayName === 'pay') {
            $payGateway = new Pay();
            $payGatewayResult = $payGateway->verify($request->token, $request->status);

            if (array_key_exists('error', $payGatewayResult)) {
                alert()->error('دقت کنید', $payGatewayResult['error']);
                return redirect()->back();
            } else {
                alert()->success('با تشکر', $payGatewayResult['success']);
                return redirect()->route('home.index');
            }
        }

        if ($gatewayName === 'zarinpal') {
            $amounts = $this->getAmounts();
            if (array_key_exists('error', $amounts)) {
                alert()->error($amounts['error'], 'دقت کنید');
                return redirect()->route('home.index');
            }

            $zarinpalGateway = new Zarinpal();
            $zarinpalGatewayResult = $zarinpalGateway->verify($request->Authority, $amounts['paying_amount']);

            if (array_key_exists('error', $zarinpalGatewayResult)) {
                alert()->error('دقت کنید', $zarinpalGatewayResult['error']);
                return redirect()->back();
            } else {
                alert()->success('با تشکر', $zarinpalGatewayResult['success']);
                return redirect()->route('home.index');
            }
        }

        alert()->error('دقت کنید', 'مسیر بازگشت از درگاه پرداخت اشتباه میباشد');
        return redirect()->route('home.orders.checkout');
    }

    public function checkCart()
    {
        if (\Cart::isEmpty())
            return ['error' => 'سبد خرید شما خالی است'];


        foreach (\Cart::getContent() as $item) {
            $variation = ProductVariation::find($item->attributes->id);
            $variation->is_sale ? $price = $variation->sale_price : $price = $variation->price;
            if ($item->price != $price) {
                \Cart::clear();
                return ['error' => 'قیمت محصول ' . $item->name . ' تغییر پیدا کرده است'];
            }
            if ($item->quantity > $variation->quantity) {
                \Cart::clear();
                return ['error' => 'تعداد موجودی درخواستی محصول ' . $item->name . ' از تعداد در انبار بیشتر است'];
            }

            return ['success' => 'success!'];
        }
    }

    public function getAmounts()
    {
        if (session()->has('coupon')) {
            $checkCoupon = checkCoupon(session()->get('coupon.code'));
            if (array_key_exists('error', $checkCoupon))
                return $checkCoupon;
        }
        return [
            'total_amount' => \Cart::getTotal() + cartTotalSaleAmount(),
            'delivery_amount' => cartTotalDeliveryAmount(),
            'coupon_amount' => session()->has('coupon') ? session()->get('coupon.amount') : 0,
            'paying_amount' => cartTotalAmount(),
        ];
    }
}
