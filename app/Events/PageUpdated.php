<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\Page;

class PageUpdated
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $page;
    public $original;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Page $page, array $original)
    {
        $this->page = $page;
        $this->original = $original;
    }
}
