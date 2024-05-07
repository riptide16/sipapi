<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\Resource;
use App\Http\Controllers\Controller;
use App\Models\Accreditation;
use App\Models\Institution;
use App\Models\Role;
use Illuminate\Http\Request;
use Cache;
use DB;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = $request->user();
        switch ($user->role->name) {
        case Role::ASSESSEE:
            $data = $this->collectAssesseeData($user);
            break;
        case Role::ASSESSOR:
            $data = $this->collectAssessorData($user);
            break;
        case Role::SUPER_ADMIN:
        case Role::ADMIN:
            $data = $this->collectAdminData($user);
            break;
        default:
            $data = [];
        }

        return new Resource($data);
    }

    private function collectAssesseeData($user)
    {
        $accredited = Accreditation::where('user_id', $user->id)
                                   ->accredited()
                                   ->orderBy('id', 'desc')
                                   ->take(5)
                                   ->get();
        $inprogress = Accreditation::where('user_id', $user->id)
                                   ->inProgress()
                                   ->orderBy('id', 'desc')
                                   ->take(5)
                                   ->get();
        $perYear = Accreditation::where('user_id', $user->id)
                                ->select(DB::raw('YEAR(created_at) as period, count(id) as total'))
                                ->groupBy('period')
                                ->orderBy('period', 'asc')
                                ->get();
        $total = 0;
        foreach ($perYear as $acc) {
            $total += $acc->total;
        }

        return [
            'latest_accreditations' => $accredited,
            'submitted_accreditations' => $inprogress,
            'per_period' => $perYear,
            'total' => $total,
        ];
    }

    private function collectAssessorData($user)
    {
        $toEvaluates = $user->evaluationAssignments()
                            ->with('accreditation')
                            ->where('scheduled_date', '>=', now()->format('Y-m-d'))
                            ->orderBy('scheduled_date', 'asc')
                            ->take(5)
                            ->get();

        $status = [
            'dinilai' => 0,
            'ditinjau' => 0,
            'penilaian_rapat' => 0,
            'terakreditasi' => 0,
            'banding' => 0,
        ];
        $states = DB::table('accreditations AS a')
                    ->join('evaluation_assignments AS ea', 'a.id', 'ea.accreditation_id')
                    ->join('evaluation_assignment_user AS eau', 'eau.evaluation_assignment_id', 'ea.id')
                    ->where('eau.user_id', $user->id)
                    ->select(DB::raw("IF(a.appealed_at is null,a.status,'banding') as state, count(a.id) as total"))
                    ->groupBy('state')
                    ->get();
        $total = 0;
        foreach ($states as $state) {
            $status[$state->state] = $state->total;
            $total += $state->total;
        }

        return [
            'evaluation_schedule' => $toEvaluates,
            'evaluation_status' => $status,
            'total' => $total,
        ];
    }

    private function collectAdminData($user)
    {
        $categoryData = Institution::groupBy('category')
            ->select(DB::raw("category, count(id) AS total"))
            ->get();
        $categories = [
            'Perpustakaan Desa' => 0,
            'Kecamatan' => 0,
            'Kabupaten Kota' => 0,
            'Provinsi' => 0,
            'SD MI' => 0,
            'SMP MTs' => 0,
            'SMA SMK MA' => 0,
            'Perguruan Tinggi' => 0,
            'Khusus' => 0,
        ];
        foreach ($categoryData as $category) {
            $categories[$category->category] = $category->total;
        }

        $statusData = DB::table('institutions AS i')
            ->leftJoin('accreditations AS a', function ($join) {
                $join->on(
                    'a.id',
                    DB::raw('(SELECT MAX(acc.id) FROM accreditations acc WHERE acc.institution_id = i.id)')
                );
            })
            ->select(DB::raw("IF(a.status = 'terakreditasi','Terakreditasi','Tidak Terakreditasi') AS status, COUNT(i.id) AS total"))
            ->groupBy('status')
            ->get();

        $statuses = [
            'Terakreditasi' => 0,
            'Tidak Terakreditasi' => 0,
        ];
        foreach ($statusData as $status) {
            $statuses[$status->status] = $status->total;
        }

        $predicateData = DB::table('institutions AS i')
            ->leftJoin('accreditations AS a', function ($join) {
                $join->on(
                    'a.id',
                    DB::raw('(SELECT MAX(acc.id) FROM accreditations acc WHERE acc.institution_id = i.id)')
                );
            })
            ->select(DB::raw("a.predicate, COUNT(i.id) AS total"))
            ->groupBy('predicate')
            ->where('a.status', 'terakreditasi')
            ->get();

        $predicates = [
            'A' => 0,
            'B' => 0,
            'C' => 0,
        ];
        foreach ($predicateData as $predicate) {
            $predicates[$predicate->predicate] = $predicate->total;
        }

        $latestAccreditations = Accreditation::select('code', 'status', 'created_at', 'updated_at')
            ->orderBy('updated_at', 'desc')
            ->take(5)
            ->get();

        $totalAccreditations = Accreditation::count();

        return [
            'categories' => $categories,
            'statuses' => $statuses,
            'predicates' => $predicates,
            'latest_accreditations' => $latestAccreditations,
            'total_accreditations' => $totalAccreditations,
        ];
    }
}
