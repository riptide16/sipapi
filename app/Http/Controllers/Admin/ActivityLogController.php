<?php

namespace App\Http\Controllers\Admin;

use App\Models\ActivityLog;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Resources\ActivityLogResource;
use App\Http\Resources\ActivityLogCollection;

class ActivityLogController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $logs = ActivityLog::filter($request->all())->orderBy('created_at', 'desc');
        $logs = $request->has('per_page') && $request->per_page <= -1
            ? $logs->get()
            : $logs->paginate($request->per_page ?? 20)->withQueryString();

        return new ActivityLogCollection($logs);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $log = ActivityLog::findOrFail($id);

        return new ActivityLogResource($log);
    }
}
