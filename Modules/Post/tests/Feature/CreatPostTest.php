<?php

namespace Module\Post\tests\Feature;

use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Storage;
use Module\Category\Models\Category;
use Module\Post\Events\PostPublish;
use Str;
use Tests\TestCase;

class CreatPostTest extends TestCase
{
    use DatabaseMigrations, WithFaker;

    private function storePost($attachments = false, $titles = null, $details = null): array
    {
        $img = 'banner.png';
        $extension = '.png';

        $this->WithoutEvents();

        //Create user and category
        $this->CreateUser();
        $categories = Category::factory()->create(['user_id' => auth()->user()]);

        Storage::fake('local');

        $attachments = $attachments ?
            [
                uploadedFile::fake()->image('image1.png'),
                UploadedFile::fake()->image('image2.png')
            ] :
            null;

        $this->post(route('post.store'), [
            'title' => $title = $titles ?? $this->faker->name,
            'details' => $details ?? $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'banner' => UploadedFile::fake()->image($img),
            'category' => [$categories->name],
            'tag' => ['tag_1'],
            'attachment' => $attachments
        ])
            ->assertValid()
            ->assertCreated();

        return array($title, $extension);
    }

    /** @test */
    public function store_post_without_attachments()
    {
        $res = $this->storePost();

        Storage::disk('local')->assertExists('public/' . Str::slug($res[0]) . $res[1]);
    }

    /** @test */
    public function store_post_with_attachments()
    {
        $res = $this->storePost(true);

        Storage::disk('local')
            ->assertExists('public/' . Str::slug($res[0]) . $res[1]);

        for ($i = 0; $i <= 2; $i++) {
            $attachments = Storage::disk('local')->listContents('private')[$i];
            preg_match("/(.*?).png/", $attachments['basename'], $match);

            Storage::disk('local')
                ->assertExists(['private/' . $match[0]]);
        }
    }

    /** @test */
    public function required_fields_when_store_post()
    {
        //Create user and category
        $this->CreateUser();

        $this->post(route('post.store'), [
            'title' => null,
            'details' => null,
            'description' => null,
            'banner' => null,
            'category' => null,
            'tag' => null,
        ])
            ->assertJsonValidationErrors(['title', 'details', 'description', 'banner', 'category', 'tag']);
    }

    /** @test */
    public function handle_length_title_post()
    {
        $this->CreateUser();

        $this->post(route('post.store'), [
            'title' => "te",
        ])->assertJsonValidationErrors('title');
    }

    /** @test */
    public function handle_unique_title_post()
    {
        $this->CreateUser();

        // Store posts when title is equals
        $this->storePost(false, "test title");
        $this->post(route('post.store'), [
            'title' => "test title",
        ])->assertJsonValidationErrors('title');
    }

    /** @test */
    public function handle_length_details_post()
    {
        $this->CreateUser();

        $this->post(route('post.store'), [
            'details' => "te",
        ])->assertJsonValidationErrors('details');
    }

    /** @test */
    public function handle_unique_details_post()
    {
        $this->CreateUser();

        // Store posts when details are equals
        $this->storePost(false, "test title", "test details");
        $this->post(route('post.store'), [
            'title' => "test title",
            'details' => "test details"
        ])->assertJsonValidationErrors('details');
    }

    /** @test */
    public function handle_type_banner_post()
    {
        $this->CreateUser();

        // Specify disk
        Storage::fake('local');

        // Store posts when title is equals
        $this->post(route('post.store'), [
            'banner' => UploadedFile::fake()->image("test.pdf"),
        ])->assertJsonValidationErrors('banner');
    }

    /** @test */
    public function handle_exist_category()
    {
        $this->CreateUser();

        $this->post(route('post.store'), [
            'category' => "test category",
        ])->assertJsonValidationErrors('category');
    }

    /** @test */
    public function store_event_mailing_with_mock()
    {
        Event::fake([
            PostPublish::class
        ]);

        $this->CreateUser();

        $this->storePost();

        Event::assertDispatched(PostPublish::class);
    }
}
