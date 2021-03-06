<?php

namespace Module\Post\Repository\v1;

use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use Module\Post\Http\Resources\v1\PostCollection;
use Module\Post\Http\Resources\v1\PostResource;
use Module\Post\Models\Post;
use Module\Post\Repository\PostRepository as Repository;

class PostRepository extends Repository
{
    /**
     * Paginate $this->model
     *
     * @param int $number
     * @return PostCollection
     */
    public function paginate(int $number = 10): PostCollection
    {
        return Cache::remember('posts.all', 900, function () use ($number) {
            return new PostCollection($this->model()->with(['user', 'tags:name', 'media'])->paginate($number));
        });
    }

    /**
     * Display the specified resource.
     *
     * @param string $post
     * @return PostResource
     */
    public function show(string $post): PostResource
    {
        return Cache::remember("post/{$post}", 900, function () use ($post) {
            return new PostResource(
                $this->model()->where('slug', $post)->with(['user', 'tags', 'media'])->firstOrFail()
            );
        });
    }

    /**
     * Search in Module\Post\Models\Post
     * @param string $keyword
     * @return object
     */
    public function search(string $keyword): object
    {
        return Post::query()
            ->where('title', 'LIKE', "%" . $keyword . "%")
            ->orWhere('slug', 'LIKE', "%" . $keyword . "%")
            ->paginate();
    }

    /**
     * Destroy User model
     *
     * @param Post $post
     * @return bool
     */
    public function destroy(Post $post): bool
    {
        if (Gate::denies('delete', [Post::class, $post])) {
            abort(Response::HTTP_FORBIDDEN);
        }

        return $post->delete();
    }
}
