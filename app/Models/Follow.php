<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Follow extends Model
{
    protected $guarded = [];
    protected $table = "follow";
    public $timestamps = false;
}
