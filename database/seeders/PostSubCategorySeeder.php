<?php

namespace Database\Seeders;

use App\Models\PostSubCategory;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PostSubCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cat1 = new PostSubCategory;

        $cat1->description = 'Articles about advertising pets and helping shelters make sapace.';
        $cat1->post_category_id = 1;
        $cat1->slug = 'advertising-pets';
        $cat1->title = 'Advertising Pets';

        $cat1->save();

        $cat2 = new PostSubCategory;

        $cat2->description = 'Articles about getting involved and helping with shelters and rescues.';
        $cat2->post_category_id = 1;
        $cat2->slug = 'shelters-and-rescues';
        $cat2->title = 'Shelters and Rescues';

        $cat2->save();

        $cat3 = new PostSubCategory;

        $cat3->description = 'Articles about tips and tricks for cats.';
        $cat3->post_category_id = 2;
        $cat3->slug = 'cat-tips-and-tricks';
        $cat3->title = 'Cat Tips and Tricks';

        $cat3->save();

        $cat4 = new PostSubCategory;

        $cat4->description = 'Articles about tips and tricks for dogs.';
        $cat4->post_category_id = 2;
        $cat4->slug = 'dog-tips-and-tricks';
        $cat4->title = 'Dog Tips and Tricks';

        $cat4->save();
    }
}
