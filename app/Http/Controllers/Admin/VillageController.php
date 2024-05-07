<?php

namespace App\Http\Controllers\Admin;

use App\Models\Village;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\VillageResource;
use App\Http\Resources\VillageCollection;
use App\Rules\Alphaspace;
use Illuminate\Http\Request;

class VillageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $villages = Village::with('subdistrict.city.province')->filter($request->all());
        $villages = $request->has('per_page') && $request->per_page <= -1
            ? $villages->get()
            : $villages->paginate($request->per_page ?? 20)->withQueryString();

        return new VillageCollection($villages);
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
            'name' => ['required', new Alphaspace(), 'min:3', 'max:191'],
            'postal_code' => 'required|digits:5',
            'subdistrict_id' => 'required|exists:subdistricts,id',
        ]);

        $village = Village::create($request->all());
        $village->load('subdistrict');

        return new VillageResource($village, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $village = Village::with('subdistrict.city.province')->findOrFail($id);

        return new VillageResource($village);
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
        $village = Village::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', new Alphaspace(), 'min:3', 'max:191'],
            'postal_code' => 'sometimes|digits:5',
            'subdistrict_id' => 'sometimes|exists:subdistricts,id',
        ]);

        $village->update($request->all());
        $village->load('subdistrict');

        return new VillageResource($village);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $village = Village::findOrFail($id);
        $village->delete();

        return new Resource();
    }
}
