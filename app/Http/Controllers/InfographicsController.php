<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Institution;
use App\Models\User;
use App\Models\Accreditation;
use App\Models\Infographic;
use App\Models\Province;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InfographicsController extends Controller
{
    public function index()
    {
        $countLibrary = Institution::select('library_name', DB::raw('count(`library_name`) as library_names'))
                                ->groupBy('library_name')
                                ->having('library_names', '=', 1)
                                ->count();

        $countAccreditationLibrary = Accreditation::where('status', 'terakreditasi')
                                            ->whereDate('certificate_expires_at', '>=', Carbon::today())
                                            ->count();

        $countAssessor = User::assessors()->count();

        $countPredicateA = Accreditation::where('predicate', 'A')->count();

        $data = [
            'library' => $countLibrary,
            'accreditationLibrary' => $countAccreditationLibrary,
            'assessor' => $countAssessor,
            'predicateA' => $countPredicateA
        ];

        return response()->json($data, 200);
    }

    public function infographic()
    {
        $data = Institution::leftJoin('provinces', 'provinces.id', '=', 'institutions.province_id')
                        ->leftJoin('infographics', 'infographics.province_name', '=', 'provinces.name')
                        ->leftJoin('accreditations', 'accreditations.institution_id', '=', 'institutions.id')
                        ->selectRaw('infographics.province_code, count(*) as total')
                        ->where('accreditations.status', 'terakreditasi')
                        ->whereDate('accreditations.certificate_expires_at', '>=', Carbon::today())
                        ->groupBy('infographics.province_name')
                        ->get();

        return response()->json($data, 200);
    }
}
