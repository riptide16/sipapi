<?php

namespace App\Http\Controllers\Admin;

use App\Models\PublicMenu;
use App\Http\Resources\Resource;
use App\Http\Resources\PublicMenuResource;
use App\Http\Resources\PublicMenuCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PublicMenuController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $menus = PublicMenu::with('parent')->filter($request->all())->orderBy('created_at', 'desc');
        $menus = $request->has('per_page') && $request->per_page <= -1
            ? $menus->get()
            : $menus->paginate($request->per_page ?? 20)->withQueryString();

        return new PublicMenuCollection($menus);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:191',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:public_menus,id',
        ]);

        $menu = PublicMenu::create($request->all());
        $menu->syncUrl();
        $menu->load('parent');

        return new PublicMenuResource($menu);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $menu = PublicMenu::with('parent')->findOrFail($id);

        return new PublicMenuResource($menu);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $menu = PublicMenu::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|max:191',
            'page_id' => 'nullable|exists:pages,id',
            'parent_id' => 'nullable|exists:public_menus,id',
        ]);
        $data = $menu->is_default ? $request->except(['page_id', 'url', 'parent_id']) : $request->all();

        $menu->update($data);
        if (!$menu->is_default) {
            $menu->syncUrl();
        }
        $menu->load('parent');

        return new PublicMenuResource($menu);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $menu = PublicMenu::where('is_default', false)->findOrFail($id);
        $menu->delete();

        return new Resource();
    }
}
