<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pet extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $casts = [
        'petfinder_id' => 'integer',
        'photo_urls' => 'array',
    ];

    /**
     * The relationship between Pet and Ad
     *
     * @return object
     */
    public function ad()
    {
        return $this->belongsTo(Ad::class);
    }

    /**
     * The relationship between Pet and Organization
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function organization()
    {
        return $this->belongsTo(Organization::class);
    }
}
