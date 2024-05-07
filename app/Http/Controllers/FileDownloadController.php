<?php

namespace App\Http\Controllers;

use App\Models\FileDownload;
use App\Http\Resources\Resource;
use App\Http\Resources\FileDownloadResource;
use App\Http\Resources\FileDownloadCollection;
use Illuminate\Http\Request;

class FileDownloadController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $files = FileDownload::filter($request->all())
                             ->published()
                             ->orderBy('created_at', 'desc');
        $files = $files->paginate($request->per_page ?? 20)->withQueryString();

        return new FileDownloadCollection($files);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $file = FileDownload::published()->findOrFail($id);

        return new FileDownloadResource($file);
    }
}
