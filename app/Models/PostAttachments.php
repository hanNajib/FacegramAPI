<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PostAttachments extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    /**
     * Get the post that owns the PostAttachments
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function post(): BelongsTo
    {
        return $this->belongsTo(Posts::class, 'post_id', 'id');
    }

    protected $hidden = ['post_id'];
}
