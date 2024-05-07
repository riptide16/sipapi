<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\GalleryCollection;
use App\Http\Resources\GalleryResource;
use App\Http\Resources\GalleryAlbumCollection;
use App\Http\Resources\GalleryAlbumResource;
use App\Http\Resources\Resource;
use App\Models\Gallery;
use App\Models\GalleryAlbum;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GalleryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $galleries = Gallery::with('album')->get();

        return new GalleryCollection($galleries);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function albums()
    {
        $albums = GalleryAlbum::whereNotNull('slug')->get();

        return new GalleryAlbumCollection($albums);
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
            'image_file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'caption' => 'required|max:65535',
            'published_date' => 'required|date|date_format:Y-m-d H:i:s',
            'album' => 'required|max:191',
        ]);

        $data = $request->all();
        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "galleries/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );
        }

        $data['album_id'] = GalleryAlbum::firstOrCreate(['name' => $data['album'], 'slug' => str_replace(' ','-',$data['album'])])->id;
        $gallery = Gallery::create($data);

        return new GalleryResource($gallery->load('album'), 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $gallery = Gallery::with('album')->findOrFail($id);

        return new GalleryResource($gallery);
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
        $gallery = Gallery::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|max:191',
            'image_file' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'caption' => 'sometimes|max:65535',
            'published_date' => 'sometimes|date|date_format:Y-m-d H:i:s',
            'album' => 'sometimes|max:191',
        ]);

        $data = $request->all();
        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "galleries/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );
            if ($gallery->image) {
                Storage::disk('public')->delete($gallery->image);
            }
        }

        if ($request->has('album')) {
            $data['album_id'] = GalleryAlbum::firstOrCreate(['name' => $data['album'], 'slug' => str_replace(' ','-',$data['album'])])->id;
        }

        $gallery->update($data);

        return new GalleryResource($gallery->load('album'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $gallery = Gallery::findOrFail($id);
        if ($gallery->image) {
            Storage::disk('public')->delete($gallery->image);
        }
        $gallery->delete();

        return new Resource();
    }
}
