<?php

namespace App\Http\Controllers;

use App\Models\Testimony;
use App\Http\Resources\TestimonyCollection;
use Illuminate\Http\Request;

class TestimonyController extends Controller
{
    public function index(Request $request)
    {
        $testimonies = Testimony::paginate($request->per_page ?? 20)->withQueryString();

        return new TestimonyCollection($testimonies);
    }
}
