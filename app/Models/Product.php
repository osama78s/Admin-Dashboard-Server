<?php

namespace App\Models;

use App\Traits\ApiTrait;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $guarded  = [];

    public function user(){
        return $this->belongsTo(User::class);
    }
}
