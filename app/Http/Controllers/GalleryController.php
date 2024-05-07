<?php

namespace App\Http\Controllers;

use App\Http\Resources\GalleryCollection;
use App\Http\Resources\GalleryAlbumCollection;
use App\Models\Gallery;
use App\Models\GalleryAlbum;
use Illuminate\Http\Request;

class GalleryController extends Controller
{
    public function index(Request $request)
    {
        $galleries = Gallery::with('album')
                            ->where('is_homepage', 1)
                            ->orderBy('published_date', 'desc')
                            ->paginate($request->per_page ?? 20)
                            ->withQueryString();

        return new GalleryCollection($galleries);
    }

    public function album(Request $request)
    {
        $albums = GalleryAlbum::with('galleries')
                            ->whereHas('galleries', function($q){
                                $q->whereNotNull('image');
                            })
                            ->whereNotNull('slug')
                            ->paginate($request->per_page ?? 20)
                            ->withQueryString();

        return new GalleryAlbumCollection($albums);
    }

    public function galleryByAlbums($slug, Request $request)
    {
        $galleries = Gallery::leftJoin('gallery_albums', 'gallery_albums.id', '=', 'galleries.album_id')
                            ->where('slug', $slug)
                            ->orderBy('published_date', 'desc')
                            ->paginate($request->per_page ?? 20)
                            ->withQueryString();

        return new GalleryCollection($galleries);
    }
}
