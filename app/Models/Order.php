<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;
    protected $table = 'order';
    protected $fillable = [
        'user_id',
        'address',
        'email',
        'phone_number',
        'description'
    ];
    public function orderDetail(){
        return $this->hasMany(OrderDetail::class,'order_id','id');
    }
}
