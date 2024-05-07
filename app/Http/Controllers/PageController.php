<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\PublicMenu;
use App\Http\Resources\Resource;
use App\Http\Resources\PageResource;
use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PageController extends Controller
{
    public function show($slug)
    {
        $menu = PublicMenu::with(['page' => function ($query) {
            $query->where('is_published', true);
        }])
            ->where('url', $slug)
            ->firstOrFail();
        if (!$menu->page) {
            throw (new ModelNotFoundException())->setModel(Page::class);
        }

        return new PageResource($menu->page);
    }
}
