<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Http\Resources\UserCollection;
use App\Http\Resources\UserResource;

class AssessorController extends Controller
{
    public function index(Request $request)
    {
        if ($request->get('is_homepage') == true) {
            $assessors = User::with('region')
                        ->assessors()
                        ->active()
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->per_page ?? 20)
                        ->withQueryString();
        } else {
            $assessors = User::with('region')
                        ->assessors()
                        ->orderBy('created_at', 'DESC')
                        ->paginate($request->per_page ?? 20)
                        ->withQueryString();
        }

        return new UserCollection($assessors);
    }

    public function show($id)
    {
        $assessor = User::with('region')
                        ->assessors()
                        ->findOrFail($id);

        return new UserResource($assessor);
    }
}
