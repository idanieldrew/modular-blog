<?php

namespace Module\Post\Events;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PostPublish
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    protected $post;

    /**
     * Create a new event instance.
     *
     * @var \Module\Post\Models\Post
     * @return void
     */
    public function __construct($post)
    {
        $this->post = $post;
    }
}