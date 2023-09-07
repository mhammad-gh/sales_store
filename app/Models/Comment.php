<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;
    protected $fillable = [
        'id',
        'product_id',
        'user_id',
        'comment',
    ];

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function Product()
    {
        return $this->belongsTo('App\Models\Model\Product');
    }
}
