<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Posts extends Model
{
    use SoftDeletes;

    protected $guarded = [];
    public $timestamps = false;
    protected $hidden = ['user_id'];

    public function attachments(): HasMany {
        return $this->hasMany(PostAttachments::class, "post_id");
    }

    public function user() {
        return $this->belongsTo(User::class, "user_id");
    }
}
