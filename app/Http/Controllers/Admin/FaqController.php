<?php

namespace App\Http\Controllers\Admin;

use App\Models\Faq;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Http\Resources\FaqResource;
use App\Http\Resources\FaqCollection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FaqController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $faqs = Faq::filter($request->all())->orderBy('created_at', 'desc');
        $faqs = $request->has('per_page') && $request->per_page <= -1
            ? $faqs->get()
            : $faqs->paginate($request->per_page ?? 20)->withQueryString();

        return new FaqCollection($faqs);
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
            'content' => 'required|string|max:65535',
            'order' => 'required|numeric|min:1',
        ]);

        $data = $request->all();

        $faq = Faq::create($data);

        return new FaqResource($faq, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $faq = Faq::findOrFail($id);

        return new FaqResource($faq);
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
        $faq = Faq::findOrFail($id);

        $request->validate([
            'title' => 'sometimes|string|max:191',
            'content' => 'sometimes|string|max:65535',
            'order' => 'sometimes|numeric|min:1',
        ]);

        $faq->update($request->all());

        return new FaqResource($faq);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $faq = Faq::findOrFail($id);

        $faq->delete();

        return new Resource();
    }
}
