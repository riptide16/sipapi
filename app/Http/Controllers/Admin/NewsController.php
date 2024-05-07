<?php

namespace App\Http\Controllers\Admin;

use App\Models\News;
use App\Http\Resources\Resource;
use App\Http\Resources\NewsResource;
use App\Http\Resources\NewsCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $news = News::with('author');
        $news = $request->has('per_page') && $request->per_page <= -1
            ? $news->get()
            : $news->paginate($request->per_page ?? 20)->withQueryString();

        return new NewsCollection($news);
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
            'title' => 'required|max:191',
            'body' => 'required|max:65535',
            'image_file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'published_date' => 'nullable|date|date_format:Y-m-d H:i:s',
        ]);

        $data = $request->all();
        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "news/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );
        }

        $news = new News($data);
        $news->author_id = $request->user()->id;
        $news->save();

        $news->load('author');

        return new NewsResource($news, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $news = News::with('author')->findOrFail($id);

        return new NewsResource($news);
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
        $news = News::with('author')->findOrFail($id);

        $request->validate([
            'title' => 'sometimes|max:191',
            'body' => 'sometimes|max:65535',
            'image_file' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'published_date' => 'nullable|date|date_format:Y-m-d H:i:s',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "news/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );

            if ($news->image) {
                Storage::disk('public')->delete($news->getRawOriginal('image'));
            }
        }

        $news->update($data);

        return new NewsResource($news);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $news = News::findOrFail($id);
        if ($news->image) {
            Storage::disk('public')->delete($news->getRawOriginal('image'));
        }
        $news->delete();

        return new Resource();
    }
}
