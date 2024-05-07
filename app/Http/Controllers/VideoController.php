<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Http\Resources\VideoCollection;
use App\Http\Resources\VideoResource;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    public function index(Request $request)
    {
        $videos = Video::orderBy('created_at', 'desc')
                       ->where('is_homepage', 1)
                       ->paginate($request->per_page ?? 20)
                       ->withQueryString();

        return new VideoCollection($videos);
    }

    public function video(Request $request)
    {
        $videos = Video::orderBy('created_at', 'desc')
                       ->paginate($request->per_page ?? 20)
                       ->withQueryString();

        return new VideoCollection($videos);
    }

    public function show($id)
    {
        $video = Video::find($id);

        return new VideoResource($video);
    }
}
