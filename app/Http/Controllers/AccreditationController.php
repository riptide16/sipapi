<?php

namespace App\Http\Controllers;

use DB;
use App\Models\Institution;
use App\Http\Resources\Resource;
use Illuminate\Http\Request;

class AccreditationController extends Controller
{
    public function browseAccredited(Request $request)
    {
        $filters = [
            'province',
            'city',
            'category',
            'predicate',
            'name',
        ];

        $continue = false;
        foreach ($request->all() as $field => $val) {
            if (in_array($field, $filters) && $request->get($field)) {
                $continue = true;
                break;
            }
        }
        if (!$continue) {
            return new Resource([]);
        }

        $query = DB::table('institutions AS i');
        if ($request->get('province')) {
            $query->where('province_id', $request->get('province'));
        }
        if ($request->get('city')) {
            $query->where('city_id', $request->get('city'));
        }
        if ($request->get('category')) {
            $query->where('category', $request->get('category'));
        }
        if ($request->get('predicate')) {
            $query->where('predicate', $request->get('predicate'));
        }
        if ($request->get('name')) {
            $query->where('library_name', 'LIKE', "%{$request->get('predicate')}%");
        }
        $query->whereNotNull('accreditation_expires_at')
              ->where('accreditation_expires_at', '>=', now())
              ->select('library_name', 'category', DB::raw('YEAR(accredited_at) AS tahun'), 'predicate', 'address');
        $data = $query->paginate($request->per_page ?? 10)->withQueryString();

        return new Resource($data);
    }

    public function totalByCategory()
    {
        $perCategory = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', function ($join) {
                $now = now();
                $join->on('i.category', 'it.category')
                     ->whereNotNull('i.accreditation_expires_at')
                     ->where('i.accreditation_expires_at', '>', $now);
            })
            ->select(DB::raw("it.category, count(i.id) AS total"))
            ->groupByRaw('it.category WITH ROLLUP')
            ->get();
        $perCategoryTotal = $perCategory->splice(count($perCategory)-1, 1);

        return new Resource([
            'per_category' => $perCategory,
            'per_category_total' => $perCategoryTotal,
        ]);
    }
}
