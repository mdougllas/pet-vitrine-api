<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Organization extends Model
{
    use HasFactory;

    protected $guarded = [];

    /**
     * The relationship between Organization and Pets
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function pets()
    {
        return $this->hasMany(Pet::class);
    }
}
