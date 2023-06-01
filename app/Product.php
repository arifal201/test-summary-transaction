<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $table = 'products';
    protected $fillable = ['name', 'stock', 'type', 'price'];

    public function transaction(){
        return $this->hasMany('App\Transaction');
    }
}
