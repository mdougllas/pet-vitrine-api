<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Mews\Purifier\Casts\CleanHtmlInput;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Post extends Model
{
    use HasFactory;

    protected $casts = [
        'body' => CleanHtmlInput::class
    ];

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function postCategory()
    {
        return $this->belongsTo(PostCategory::class);
    }

    public function postSubCategory()
    {
        return $this->belongsTo(PostSubCategory::class);
    }
}
