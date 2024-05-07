<?php

namespace App\Http\Controllers\Admin;

use App\Models\Province;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\ProvinceResource;
use App\Http\Resources\ProvinceCollection;
use App\Rules\Alphaspace;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ProvinceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $provinces = Province::filter($request->all());
        $provinces = $request->has('per_page') && $request->per_page <= -1
            ? $provinces->get()
            : $provinces->paginate($request->per_page ?? 20)->withQueryString();

        return new ProvinceCollection($provinces);
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
            'name' => ['required', new Alphaspace(), 'unique:provinces'],
        ]);

        $province = Province::create($request->all());

        return new ProvinceResource($province, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $province = Province::findOrFail($id);

        return new ProvinceResource($province);
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
        $province = Province::findOrFail($id);

        $request->validate([
            'name' => [
                'sometimes',
                new Alphaspace(),
                Rule::unique('provinces')->ignore($province),
            ],
        ]);

        $province->update($request->all());

        return new ProvinceResource($province);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $province = Province::findOrFail($id);
        $province->delete();

        return new Resource();
    }
}
