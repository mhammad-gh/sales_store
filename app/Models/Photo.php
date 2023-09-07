<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Photo extends Model
{
    use HasFactory;
    protected $fillable= [
        'id',
        'product_id',
        'photo',
    ];
protected $primarykey = 'id';
protected $foreignkey = 'product_id';

public function Product()
    {
        return $this->belongsTo('App\Models\Model\Product');
    }
}
