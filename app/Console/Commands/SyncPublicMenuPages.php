<?php

namespace App\Console\Commands;

use App\Models\Page;
use App\Models\PublicMenu;
use Illuminate\Console\Command;

class SyncPublicMenuPages extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sync:public-menu-pages';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Sync public menu page with corresponding pages via URL';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $menus = PublicMenu::where('is_default', false)->get();
        foreach ($menus as $menu) {
            $page = Page::where('slug', $menu->url)->first();
            if ($page) {
                $menu->page_id = $page->id;
                $menu->saveQuietly();
                $menu->syncUrl();
            }
        }
    }
}
