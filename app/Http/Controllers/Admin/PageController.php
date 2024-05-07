<?php

namespace App\Http\Controllers\Admin;

use App\Events\PageUpdated;
use App\Models\Page;
use App\Http\Resources\Resource;
use App\Http\Resources\PageResource;
use App\Http\Resources\PageCollection;
use App\Http\Resources\ErrorResource;
use App\Http\Controllers\Controller;
use App\Rules\Slugurl;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $pages = Page::filter($request->all())->orderBy('id', 'desc');
        $pages = $request->has('per_page') && $request->per_page <= -1
            ? $pages->get()
            : $pages->paginate($request->per_page ?? 20)->withQueryString();

        return new PageCollection($pages);
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
            'slug' => [
                'required',
                new Slugurl(),
                'max:191',
                'unique:pages,slug',
            ],
            'body' => 'nullable|max:4294967294',
            'is_published' => 'required|boolean',
        ]);

        $page = Page::create($request->all());

        return new PageResource($page);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $page = Page::findOrFail($id);

        return new PageResource($page);
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
        $page = Page::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|max:191',
            'slug' => [
                'nullable',
                new Slugurl(),
                'max:191',
                Rule::unique('pages')->ignore($page),
            ],
            'body' => 'nullable|max:4294967294',
            'is_published' => 'sometimes|boolean',
        ]);

        $original = $page->toArray();
        $page->update($request->all());
        event(new PageUpdated($page, $original));

        return new PageResource($page);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $page = Page::findOrFail($id);
        if (!$page->publicMenus->isEmpty()) {
            return new ErrorResource(__('errors.constraint_violation'), 406, 'ERR4506');
        }
        $page->delete();

        return new Resource();
    }
}
