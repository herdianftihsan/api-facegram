<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    protected $guarded = [];
    public $timestamps = false;

    public function user()
    {
        return $this->belongsTo(User::class); // user_id
    }

    public function postAttachments()
    {
        return $this->hasMany(PostAttachment::class);
    }

    public function attachments(){
        return $this->hasMany(PostAttachment::class,);
    }
}
