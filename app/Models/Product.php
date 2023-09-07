<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = ['id', 'section', 'tybe', 'title', 'description', 'price', 'location_x', 'location_y', 'photo', 'old_new', 'user_id'];
    protected $primarykey = 'id';
    protected $foreignkey = 'user_id';

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function Photo()
    {
        return $this->hasOne('App\Models\Model\Photo');
    }

    public function Comment()
    {
        return $this->hasOne('App\Models\Model\Comment');
    }

    public function Like()
    {
        return $this->hasOne('App\Models\Model\Like');
    }

    public function Sell_Buy()
    {
        return $this->hasOne('App\Models\Model\Sell_Buy');
    }
}
