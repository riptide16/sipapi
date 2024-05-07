<?php

namespace App\Http\Controllers\Admin;

use App\Models\Province;
use App\Models\ProvinceRegion;
use App\Http\Controllers\Controller;
use App\Http\Resources\ProvinceCollection;
use Illuminate\Http\Request;

class ProvinceRegionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $id)
    {
        $region = ProvinceRegion::where('region_id', $id)->get();
        $provinceRegion = ProvinceRegion::get();
        $provinceDiff = array_diff($provinceRegion->pluck('province_id')->toArray(), $region->pluck('province_id')->toArray());
        $provinces = Province::whereNotIn('id', array_values($provinceDiff))->filter($request->all());
        $provinces = $request->has('per_page') && $request->per_page <= -1
            ? $provinces->get()
            : $provinces->paginate($request->per_page ?? 20)->withQueryString();

        return new ProvinceCollection($provinces);
    }

    public function available(Request $request)
    {
        $provinceRegion = ProvinceRegion::get();
        $provinces = Province::whereNotIn('id', $provinceRegion->pluck('province_id'))->filter($request->all());
        $provinces = $request->has('per_page') && $request->per_page <= -1
            ? $provinces->get()
            : $provinces->paginate($request->per_page ?? 20)->withQueryString();

        return new ProvinceCollection($provinces);
    }

    public function provinceByRegion(Request $request)
    {
        $provinces = Province::leftJoin('province_region', 'province_region.province_id', '=', 'provinces.id')->where('region_id', $request->region_id);
        $provinces = $request->has('per_page') && $request->per_page <= -1
            ? $provinces->get()
            : $provinces->paginate($request->per_page ?? 20)->withQueryString();

        return new ProvinceCollection($provinces);
    }
}
