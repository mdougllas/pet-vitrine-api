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
    protected $with = ['pet'];

    /**
     * The relationship between Ad and Pet
     *
     * @return void
     */
    public function pet()
    {
        return $this->hasOne(Pet::class);
    }
}
