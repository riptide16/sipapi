<?php

namespace App\Http\Controllers\Admin;

use App\Models\Instrument;
use App\Http\Controllers\Controller;
use App\Http\Resources\InstrumentCollection;
use App\Http\Resources\InstrumentResource;
use App\Http\Resources\Resource;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class InstrumentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $instruments = Instrument::get();

        return new InstrumentCollection($instruments);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $instrument = Instrument::findOrFail($id);

        return new InstrumentResource($instrument);
    }
}
