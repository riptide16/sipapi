<?php

namespace App\Listeners;

use App\Events\PageUpdated;
use App\Models\PublicMenu;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SyncPublicMenuUrl
{
    /**
     * Handle the event.
     *
     * @param  PageUpdated  $event
     * @return void
     */
    public function handle(PageUpdated $event)
    {
        if ($event->page->wasChanged('slug')) {
            $menus = PublicMenu::where('url', $event->original['slug'])->get();
            foreach ($menus as $menu) {
                $menu->url = $event->page->slug;
                $menu->save();
            }
        }

    }
}
