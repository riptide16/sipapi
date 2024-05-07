<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\RegionCollection;
use App\Http\Resources\RegionResource;
use App\Models\Region;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $regions = Region::with(['provinces', 'creator'])->filter($request->all());
        $regions = $request->has('per_page') && $request->per_page <= -1
            ? $regions->get()
            : $regions->paginate($request->per_page ?? 20)->withQueryString();

        return new RegionCollection($regions);
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
            'name' => 'required|string|max:191',
            'province_ids' => 'required',
        ]);

        $provinceIds = explode(',', $request->get('province_ids'));
        $this->provincesValidator($provinceIds);

        $region = new Region($request->all());
        $region->created_by = $request->user()->id;
        $region->save();
        $region->provinces()->sync($provinceIds);
        $region->load(['provinces', 'creator']);

        return new RegionResource($region, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $region = Region::with(['creator', 'provinces'])->findOrFail($id);

        return new RegionResource($region);
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
        $region = Region::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|string|max:191',
            'province_ids' => 'sometimes',
        ]);

        if ($request->has('province_ids')) {
            $provinceIds = explode(',', $request->get('province_ids'));
            $this->provincesValidator($provinceIds);
        }

        $region->update($request->all());
        if ($request->has('province_ids')) {
            $region->provinces()->sync($provinceIds);
        }

        $region->load(['creator', 'provinces']);

        return new RegionResource($region);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $region = Region::with(['creator', 'provinces'])->findOrFail($id);
        $region->delete();

        return new RegionResource($region);
    }

    protected function provincesValidator($provinceIds)
    {
        foreach ($provinceIds as $provinceId) {
            app('validator')->make(
                ['province_ids' => $provinceId],
                ['province_ids' => 'required|exists:provinces,id'],
                [],
                ['province_ids' => 'province ' . $provinceId]
            )->validate();
        }
    }
}
