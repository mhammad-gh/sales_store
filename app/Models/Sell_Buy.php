<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Sell_Buy extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'id',
        'product_id',
        'usersell_id',
        'userbuy_id',
    ];

    public function Usersell()
    {
        return $this->belongsTo('App\Models\User');
    }
    public function Userbuy()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function Product()
    {
        return $this->belongsTo('App\Models\Product');
    }

    protected $dates = ['deleted_at'];
}

