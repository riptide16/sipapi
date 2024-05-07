<?php

namespace App\Http\Controllers\Admin;

use App\Models\Video;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\VideoResource;
use App\Http\Resources\VideoCollection;
use Illuminate\Http\Request;

class VideoController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $videos = $request->has('per_page') && $request->per_page <= -1
            ? Video::get()
            : Video::paginate($request->per_page ?? 20)->withQueryString();

        return new VideoCollection($videos);
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
            'title' => 'required|string|max:191',
            'youtube_id' => 'required|string|max:191'
        ]);

        $video = Video::create($request->all());

        return new VideoResource($video, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $video = Video::findOrFail($id);

        return new VideoResource($video);
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
        $video = Video::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:191',
            'youtube_id' => 'sometimes|string|max:191'
        ]);

        $video->update($request->all());

        return new VideoResource($video);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $video = Video::findOrFail($id);
        $video->delete();

        return new Resource();
    }
}
