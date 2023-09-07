<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rate_User extends Model
{
    use HasFactory;

    protected $fillable = ['id', 'user_id','rate'];
    protected $primarykey = 'id';
    protected $foreignkey = 'user_id';

    public function User()
    {
        return $this->belongsTo('App\Models\User');
    }
    protected $table = 'rate_users';
}
