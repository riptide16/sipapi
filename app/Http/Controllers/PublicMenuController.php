<?php

namespace App\Http\Controllers;

use App\Http\Resources\PublicMenuCollection;
use App\Models\PublicMenu;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    public function index(Request $request)
    {
        $menus = PublicMenu::ancestors()
                           ->sort()
                           ->get()
                           ->each(function ($menu) {
                               $menu->loadAllChildren();
                           });

        return new PublicMenuCollection($menus);
    }
}
