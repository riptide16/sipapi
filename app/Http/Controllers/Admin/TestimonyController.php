<?php

namespace App\Http\Controllers\Admin;

use App\Models\Testimony;
use App\Http\Resources\Resource;
use App\Http\Resources\TestimonyResource;
use App\Http\Resources\TestimonyCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class TestimonyController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $testimonies = Testimony::filter($request->all());
        $testimonies = $request->has('per_page') && $request->per_page <= -1
            ? $testimonies->get()
            : $testimonies->paginate($request->per_page ?? 20)->withQueryString();

        return new TestimonyCollection($testimonies);
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
            'name' => 'required|min:3|max:191',
            'content' => 'required|max:65535',
            'photo_file' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();
        if ($request->hasFile('photo_file')) {
            $today = today();
            $data['photo'] = $request->file('photo_file')
                                     ->storePublicly(
                                         "testimonies/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );
        }

        $testimony = Testimony::create($data);

        return new TestimonyResource($testimony, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $testimony = Testimony::findOrFail($id);

        return new TestimonyResource($testimony);
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
        $testimony = Testimony::findOrFail($id);

        $request->validate([
            'name' => 'sometimes|min:3|max:191',
            'content' => 'sometimes|max:65535',
            'photo_file' => 'sometimes|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $data = $request->all();

        if ($request->hasFile('photo_file')) {
            $today = today();
            $data['photo'] = $request->file('photo_file')
                                     ->storePublicly(
                                         "testimonies/{$today->format('Y')}/{$today->format('m')}",
                                         'public'
                                     );

            if ($testimony->photo) {
                Storage::disk('public')->delete($testimony->photo);
            }
        }

        $testimony->update($data);

        return new TestimonyResource($testimony);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $testimony = Testimony::findOrFail($id);
        if ($testimony->photo) {
            Storage::disk('public')->delete($testimony->photo);
        }
        $testimony->delete();

        return new Resource();
    }
}
