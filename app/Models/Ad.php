<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ad extends Model
{
    use HasFactory;

    protected $guarded = []; //yolo

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
