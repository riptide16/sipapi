<?php

namespace App\Http\Controllers\Admin;

use App\Models\City;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\CityResource;
use App\Http\Resources\CityCollection;
use App\Rules\Alphaspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->has('no_relations')) {
            $cities = City::filter($request->all());
        } else {
            $cities = City::with('province')->filter($request->all());
        }
        $cities = $request->has('per_page') && $request->per_page <= -1
            ? $cities->get()
            : $cities->paginate($request->per_page ?? 20)->withQueryString();

        return new CityCollection($cities);
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
            'type' => ['string', Rule::in(City::typeList())],
            'province_id' => 'required|exists:provinces,id',
        ]);

        $city = City::create($request->all());
        $city->load('province');

        return new CityResource($city, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $city = City::with('province')->findOrFail($id);

        return new CityResource($city);
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
        $city = City::findOrFail($id);

        $request->validate([
            'name' => ['sometimes', new Alphaspace(), 'min:3', 'max:191'],
            'type' => ['string', Rule::in(City::typeList())],
            'province_id' => 'sometimes|exists:provinces,id',
        ]);

        $city->update($request->all());
        $city->load('province');

        return new CityResource($city);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $city = City::findOrFail($id);
        $city->delete();

        return new Resource();

    }
}
