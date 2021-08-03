<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relationship between Pet and Ad
     *
     * @return object
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }
}
