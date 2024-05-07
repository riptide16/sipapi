<?php

namespace App\Http\Controllers\Admin;

use App\Models\FileDownload;
use App\Http\Resources\Resource;
use App\Http\Resources\FileDownloadResource;
use App\Http\Resources\FileDownloadCollection;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FileDownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $files = FileDownload::filter($request->all())->orderBy('created_at', 'desc');
        $files = $request->has('per_page') && $request->per_page <= -1
            ? $files->get()
            : $files->paginate($request->per_page ?? 20)->withQueryString();

        return new FileDownloadCollection($files);
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
            'filename' => 'required|min:3|max:191',
            'is_published' => 'required|boolean',
            'attachment_file' => 'required|file|max:5120',
        ]);

        $data = $request->all();
        if ($request->hasFile('attachment_file')) {
            $today = today();
            $data['attachment'] = $request->file('attachment_file')
                                          ->storePublicly(
                                              "files/{$today->format('Y')}/{$today->format('m')}",
                                              'public'
                                          );
        }

        $file = FileDownload::create($data);

        return new FileDownloadResource($file, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = FileDownload::findOrFail($id);

        return new FileDownloadResource($file);
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
        $file = FileDownload::findOrFail($id);

        $request->validate([
            'filename' => 'sometimes|min:3|max:191',
            'is_published' => 'sometimes|boolean',
            'attachment_file' => 'sometimes|file|max:5120',
        ]);

        $data = $request->all();

        if ($request->hasFile('attachment_file')) {
            $today = today();
            $data['attachment'] = $request->file('attachment_file')
                                          ->storePublicly(
                                              "files/{$today->format('Y')}/{$today->format('m')}",
                                              'public'
                                          );

            if ($file->attachment) {
                Storage::disk('public')->delete($file->attachment);
            }
        }

        $file->update($data);

        return new FileDownloadResource($file);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $file = FileDownload::findOrFail($id);
        if ($file->attachment) {
            Storage::disk('public')->delete($file->attachment);
        }
        $file->delete();

        return new Resource();
    }
}
