<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Customer;
use App\Product;

class Transaction extends Model
{
    protected $table = 'transactions';
    protected $fillable = ['quantity', 'total', 'product_id', 'customer_id', 'product_stock'];

    public function product(){
        return $this->belongsTo('App\Product');
    }

    public function customer(){
        return $this->belongsTo('App\Customer');
    }
}
