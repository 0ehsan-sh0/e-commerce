<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;
    protected $guarded = [];

    public function getStatusAttribute($status)
    {
        return $status ? 'موفق' : 'نا موفق';
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
