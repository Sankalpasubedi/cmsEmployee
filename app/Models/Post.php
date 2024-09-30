<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;
    protected $guarded = [];

    const STATUS_INACTIVE = 0;
    const STATUS_PRIVATE = 1;
    const STATUS_VISIBLE = 2;
    const STATUS_PUBLIC = 3;

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
