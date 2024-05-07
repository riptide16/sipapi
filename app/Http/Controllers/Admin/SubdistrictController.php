<?php

namespace App\Http\Controllers\Admin;

use App\Models\Subdistrict;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\SubdistrictResource;
use App\Http\Resources\SubdistrictCollection;
use App\Rules\Alphaspace;
use Illuminate\Http\Request;

class SubdistrictController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $subdistricts = Subdistrict::with('city.province')->filter($request->all());
        $subdistricts = $request->has('per_page') && $request->per_page <= -1
            ? $subdistricts->get()
            : $subdistricts->paginate($request->per_page ?? 20)->withQueryString();

        return new SubdistrictCollection($subdistricts);
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
            'city_id' => 'required|exists:cities,id',
        ]);

        $subdistrict = Subdistrict::create($request->all());
        $subdistrict->load('city');

        return new SubdistrictResource($subdistrict, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $subdistrict = Subdistrict::with('city.province')->findOrFail($id);

        return new SubdistrictResource($subdistrict);
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
        $subdistrict = Subdistrict::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', new Alphaspace(), 'min:3', 'max:191'],
            'city_id' => 'sometimes|exists:cities,id',
        ]);

        $subdistrict->update($request->all());
        $subdistrict->load('city');

        return new SubdistrictResource($subdistrict);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $subdistrict = Subdistrict::findOrFail($id);
        $subdistrict->delete();

        return new Resource();
    }
}
