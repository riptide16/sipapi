<?php

namespace App\Http\Controllers\Admin;

use App\Models\Banner;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\BannerResource;
use App\Http\Resources\BannerCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class BannerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $banners = Banner::get();

        return new BannerCollection($banners);
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
            'image_file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
            'order' => 'required|numeric|min:1',
            'url' => 'nullable|url',
            'is_active' => 'required|boolean',
        ]);

        $data = $request->all();
        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "banners/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );
        }

        $banner = Banner::create($data);

        return new BannerResource($banner, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $banner = Banner::findOrFail($id);

        return new BannerResource($banner);
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
        $banner = Banner::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|max:191',
            'image_file' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
            'order' => 'sometimes|numeric|min:1',
            'url' => 'nullable|url',
            'is_active' => 'sometimes|boolean',
        ]);

        $data = $request->all();

        if ($request->hasFile('image_file')) {
            $today = today();
            $data['image'] = $request->file('image_file')
                                     ->storePublicly(
                                         "banners/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );

            if ($banner->image) {
                Storage::disk('public')->delete($banner->getRawOriginal('image'));
            }
        }

        $banner->update($data);

        return new BannerResource($banner);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $banner = Banner::findOrFail($id);
        if ($banner->image) {
            Storage::disk('public')->delete($banner->getRawOriginal('image'));
        }
        $banner->delete();

        return new Resource();
    }
}
