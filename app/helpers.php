<?php

use Carbon\Carbon;
use App\Models\Order;
use App\Models\Coupon;

function generateFileName($image)
{
    $timestamp = Carbon::now()->timestamp;
    $microSecond = Carbon::now()->microsecond;
    $fileNameImagePrimary = $timestamp . '_' . $microSecond . '_' . $image->getClientOriginalName();
    return $fileNameImagePrimary;
}

function ShamsiToGregorian($shamsiDateTime)
{
    // checking if the timestamp is null
    if (!$shamsiDateTime) {
        return null;
    }
    // spliting the year, month and day and time into an array
    $shamsiDate = preg_split("/[-\s]/", $shamsiDateTime);
    // turning into gregorian
    $gregorianDate = verta()->jalaliToGregorian($shamsiDate[0], $shamsiDate[1], $shamsiDate[2]);
    // adding the time
    return implode("-", $gregorianDate) . " " . $shamsiDate[3];
}

function cartTotalSaleAmount()
{
    $cartTotalSaleAmount = 0;
    foreach (\Cart::getContent() as $item) {
        if ($item->attributes->is_sale) {
            $cartTotalSaleAmount += $item->quantity * ($item->attributes->price - $item->attributes->sale_price);
        }
    }

    return $cartTotalSaleAmount;
}

function cartTotalDeliveryAmount()
{
    $cartTotalDeliveryAmount = 0;
    foreach (\Cart::getContent() as $item) {
        $cartTotalDeliveryAmount += $item->associatedModel->delivery_amount;
    }

    return $cartTotalDeliveryAmount;
}

function checkCoupon($code)
{
    $coupon = Coupon::where('code', $code)->where('expired_at', '>', Carbon::now())->first();
    if (!$coupon)
        return ['error' => 'کد تخفیف وارد شده وجود ندارد'];

    if (Order::where('user_id', auth()->id())->where('coupon_id', $coupon->id)->where('payment_status', 1)->exists())
        return ['error' => 'شما قبلا از این کد تخفیف استفاده کرده اید'];

    if ($coupon->getRawOriginal('type') === 'amount')
        session()->put('coupon', [
            'code' => $coupon->code,
            'amount' => $coupon->amount,
        ]);
    else {
        $total = \Cart::getTotal();
        $amount = (($total * $coupon->percentage) / 100) > $coupon->max_percentage_amount ? $coupon->max_percentage_amount : (($total * $coupon->percentage) / 100);
        session()->put('coupon', [
            'code' => $coupon->code,
            'percentage' => $coupon->percentage,
            'amount' => $amount,
        ]);
    }

    return ['success' => 'کد تخفیف برای شما اعمال شد'];
}

function cartTotalAmount()
{
    $totalAmount = \Cart::getTotal();
    if (session()->has('coupon')) {
        if (session()->get('coupon.amount') > ($totalAmount + cartTotalDeliveryAmount()))
            return 0;
        else return ($totalAmount + cartTotalDeliveryAmount()) - session()->get('coupon.amount');
    } else
        $totalAmount + cartTotalDeliveryAmount();
}
