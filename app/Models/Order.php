<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getStatusAttribute($status)
    {
        return $status ? 'پرداخت شده' : 'در انتظار پرداخت';
    }

    public function getPaymentStatusAttribute($status)
    {
        return $status ? 'موفق' : 'ناموفق';
    }

    public function getPaymentTypeAttribute($paymentType)
    {
        switch ($paymentType) {
            case 'pos':
                $paymentType = 'دستگاه pos';
                break;

            case 'online':
                $paymentType = 'خرید اینترنتی';
                break;
            default:
                $paymentType = 'خرید اینترنتی';
                break;
        }
        return $paymentType;
    }

    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function coupon()
    {
        return $this->belongsTo(Coupon::class);
    }

    public function address()
    {
        return $this->belongsTo(UserAddress::class);
    }
}
