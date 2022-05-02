<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Module\Category\Database\Seeders\CategorySeeder;
use Module\Category\Models\Category;
use Module\Image\Models\Image;
use Module\Post\Models\Post;
use Module\User\Models\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // Category
        $this->call([
        CategorySeeder::class
        ]);

        User::factory(5)->create()->each(function ($user){
            $user->posts()->save(Post::factory()->make())->each(function ($post){
                $post->images()->save(Image::factory()->make());
            });
        });
    }
}