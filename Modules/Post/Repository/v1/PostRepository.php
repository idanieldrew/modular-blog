<?php

namespace Module\Post\Repository\v1;

use Illuminate\Support\Facades\Gate;
use Module\Post\Models\Post;
use Module\Share\Repository\Repository;

class PostRepository implements Repository
{
    /**
     * Specify Model
     * Abstract function
     */
    public function model()
    {
        return Post::query();
    }

    /**
     * Paginate $this->model
     * @param int $number
     * @return \Illuminate\Database\Eloquent\Model
     */
    public function paginate($number = 10)
    {
        return $this->model()->get();
    }

    /**
     * Show $this->model
     * @param string $post
     * @return \Module\Post\Models\Post
     */
    public function show($post)
    {
        return $this->model()->firstOrFail();
    }

    /**
     * Destroy User model
     *
     * @param  \Module\User\Models\User $user
     * @return boolean
     */
    public function destroy($user)
    {
        if (Gate::denies('delete', [Post::class, $user])) {
            abort(403);
        }

        return $user->delete();
    }
}