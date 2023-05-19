<?php

namespace Database\Seeders;

use App\Models\PostCategory;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PostCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cat1 = new PostCategory;

        $cat1->description = 'Articles about getting involved with the community, shelters and rescues and advertising pets.';
        $cat1->slug = 'get-involved';
        $cat1->title = 'Get Involved';

        $cat1->save();

        $cat2 = new PostCategory;

        $cat2->description = 'Articles about tips and tricks for pet adoption and pet interaction in general.';
        $cat2->slug = 'pet-tips-and-tricks';
        $cat2->title = 'Pet Tips and Tricks';

        $cat2->save();
    }
}
