<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    /**
     * Do not protect any attributes from mass assignment.
     * yolo
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * Always eager loads the pet for each ad.
     *
     * @var array
     */
    // protected $with = ['pet'];

    protected $casts = [
        'budget' => 'integer',
        'start_time' => 'datetime:m-d-Y',
        'end_time' => 'datetime:m-d-Y',
    ];

    /**
     * The relationship between Ad and Pet
     *
     * @return void
     */
    public function pet()
    {
        return $this->belongsToOne(Pet::class);
    }

    /**
     * The relationship between Ad and User
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsToOne(User::class);
    }
}
