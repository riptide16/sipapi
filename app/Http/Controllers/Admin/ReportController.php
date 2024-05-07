<?php

namespace App\Http\Controllers\Admin;

use DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Resource;
use App\Models\Accreditation;
use App\Models\Institution;
use App\Exports\Reports\TotalAccreditationExport;
use App\Exports\Reports\TotalAccreditationPerYearExport;
use App\Exports\Reports\TotalAccreditationByYearDetailExport;
use App\Exports\Reports\TotalAccreditationPerProvinceInYearExport;
use App\Exports\Reports\TotalAccreditationByProvincePerYearExport;
use App\Exports\Reports\TotalAccreditedLibraryExport;
use App\Exports\Reports\TotalAccreditedLibraryWithinYearExport;
use App\Exports\Reports\TotalAccreditedLibraryPerYearExport;
use App\Exports\Reports\TotalAccreditedLibraryByProvinceInYearExport;
use App\Exports\Reports\TotalAccreditedLibraryByProvincePerYearExport;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    private $categories = [
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

    public function totalAccreditations(Request $request)
    {
        $perCategories = $this->categories;
        $perCategoriesTotal = 0;
        $categoryData = DB::table('accreditations AS a')
            ->join('institutions AS i', 'a.institution_id', 'i.id')
            ->select(DB::raw("i.category, count(i.id) AS total"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->groupBy('i.category')
            ->get();
        foreach ($categoryData as $data) {
            $perCategories[$data->category] = $data->total;
            $perCategoriesTotal += $data->total;
        }

        $perProvinces = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', 'i.province_id', 'p.id')
            ->leftJoin('accreditations AS a', 'a.institution_id', 'i.id')
            ->select(DB::raw("p.name, count(a.id) AS total"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();
        $perProvincesTotal = $perProvinces->splice(count($perProvinces)-1, 1);

        $data = [
            'per_categories' => $perCategories,
            'per_categories_total' => $perCategoriesTotal,
            'per_provinces' => $perProvinces,
            'per_provinces_total' => $perProvincesTotal[0]->total,
        ];

        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditationExport($data))->download($today . ' - Report Jumlah Akreditasi.xlsx');
        }

        return new Resource($data);
    }

    public function totalAccreditationsPerYear(Request $request)
    {
        if ($request->has('category')) {
            return $this->totalAccreditationsPerYearByCategory($request);
        }

        if ($request->get('export')) {
            $today = now()->format('Ymd');

            $years = DB::table('accreditations AS a')
                ->select(DB::raw("DISTINCT YEAR(a.created_at) AS tahun"))
                ->where('a.status', Accreditation::STATUS_ACCREDITED)
                ->orderBy('tahun', 'asc')
                ->pluck('tahun')
                ->toArray();

            $yearSql = "";
            foreach ($years as $year) {
                $yearSql .= "SUM(CASE WHEN YEAR(a.created_at)={$year} THEN 1 ELSE 0 END) AS '{$year}', ";
            }

            $data = DB::table('instruments AS it')
                ->leftJoin('institutions AS i', 'i.category', 'it.category')
                ->leftJoin('accreditations AS a', function ($join) {
                    $join->on('i.id', 'a.institution_id')
                         ->where('a.status', Accreditation::STATUS_ACCREDITED);
                }) 
                ->select(DB::raw("it.category, {$yearSql} count(a.id) AS total"))
                ->groupByRaw('it.category WITH ROLLUP')
                ->get();

            return (new TotalAccreditationPerYearExport($data))->download($today . ' - Report Jumlah Akreditasi Berdasarkan Jenis Perpustakaan Dalam Tahun.xlsx');
        }

        $perYear = [
            'Perpustakaan Desa' => [],
            'Kecamatan' => [],
            'Kabupaten Kota' => [],
            'Provinsi' => [],
            'SD MI' => [],
            'SMP MTs' => [],
            'SMA SMK MA' => [],
            'Perguruan Tinggi' => [],
            'Khusus' => [],
        ];
        $totals = [];
        $perYearTotal = 0;
        $yearData = DB::table('accreditations AS a')
            ->join('institutions AS i', 'a.institution_id', 'i.id')
            ->select(DB::raw("i.category, YEAR(a.created_at) as tahun, count(i.id) AS total"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->groupBy('i.category', 'tahun')
            ->get();
        foreach ($yearData as $data) {
            $perYear[$data->category][$data->tahun] = $data->total;
            if (isset($perYear[$data->category]['total'])) {
                $perYear[$data->category]['total'] += $data->total;
            } else {
                $perYear[$data->category]['total'] = $data->total;
            }
            if (isset($totals[$data->tahun])) {
                $totals[$data->tahun] += $data->total;
            } else {
                $totals[$data->tahun] = $data->total;
            }
            if (isset($totals['total'])) {
                $totals['total'] += $data->total;
            } else {
                $totals['total'] = $data->total;
            }
        }

        $data = [
            'per_year' => $perYear,
            'totals' => $totals,
        ];

        return new Resource($data);
    }

    public function totalAccreditationsPerYearByCategory(Request $request)
    {
        $predicates = [
            'A' => (object) [],
            'B' => (object) [],
            'C' => (object) [],
        ];
        $yearData = DB::table('accreditations AS a')
            ->join('institutions AS i', 'a.institution_id', 'i.id')
            ->select(DB::raw("a.predicate, YEAR(a.created_at) as tahun, count(i.id) AS total"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->where('i.category', $request->get('category'))
            ->groupBy('a.predicate', 'tahun')
            ->get();
        foreach ($yearData as $data) {
            $predicates[$data->predicate]->{$data->tahun} = $data->total;
        }

        return new Resource([
            'per_year' => $predicates,
        ]);
    }

    public function totalAccreditationsByYear(Request $request)
    {
        if ($request->has('year')) {
            return $this->totalAccreditationsByYearDetail($request);
        }

        $years = DB::table('accreditations AS a')
            ->select(DB::raw("DISTINCT YEAR(a.created_at) AS tahun"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->pluck('tahun')
            ->toArray();

        return new Resource([
            'years' => $years,
        ]);
    }

    public function totalAccreditationsByYearDetail(Request $request)
    {
        if ($request->get('export')) {
            $predicateSql = "";
            foreach (['A', 'B', 'C'] as $predicate) {
                $predicateSql .= "SUM(CASE WHEN a.predicate='{$predicate}' THEN 1 ELSE 0 END) AS '{$predicate}', ";
            }

            $data = DB::table('instruments AS it')
                ->leftJoin('institutions AS i', 'i.category', 'it.category')
                ->leftJoin('accreditations AS a', function ($join) use ($request) {
                    $join->on('i.id', 'a.institution_id')
                         ->where('a.status', Accreditation::STATUS_ACCREDITED)
                         ->where(DB::raw('YEAR(a.created_at)'), $request->get('year'));
                }) 
                ->select(DB::raw("it.category, {$predicateSql} count(a.id) AS '{$request->get('year')}'"))
                ->groupByRaw('it.category WITH ROLLUP')
                ->get();

            $today = now()->format('Ymd');
            return (new TotalAccreditationByYearDetailExport($data))->download($today . " - Report Jumlah Akreditasi Berdasarkan Jenis Perpustakaan Dalam Tahun {$request->get('year')}.xlsx");
        }
        $predicates = [
            'A' => (object) [],
            'B' => (object) [],
            'C' => (object) [],
        ];
        $categories = [
            'Perpustakaan Desa' => [],
            'Kecamatan' => [],
            'Kabupaten Kota' => [],
            'Provinsi' => [],
            'SD MI' => [],
            'SMP MTs' => [],
            'SMA SMK MA' => [],
            'Perguruan Tinggi' => [],
            'Khusus' => [],
        ];
        $yearData = DB::table('accreditations AS a')
            ->join('institutions AS i', 'a.institution_id', 'i.id')
            ->select(DB::raw("i.category, a.predicate, count(i.id) AS total"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->where(DB::raw('YEAR(a.created_at)'), $request->get('year'))
            ->groupBy('i.category', 'a.predicate')
            ->get();
        foreach ($yearData as $data) {
            $categories[$data->category][$data->predicate] = $data->total;
            if (isset($categories[$data->category]['total'])) {
                $categories[$data->category]['total'] += $data->total;
            } else {
                $categories[$data->category]['total'] = $data->total;
            }
        }

        return new Resource([
            'data' => $categories,
        ]);
    }

    public function totalAccreditationsByProvinceInYear(Request $request)
    {
        $years = DB::table('accreditations AS a')
            ->select(DB::raw("DISTINCT YEAR(a.created_at) AS tahun"))
            ->where('a.status', Accreditation::STATUS_ACCREDITED)
            ->orderBy('tahun', 'asc')
            ->pluck('tahun')
            ->toArray();

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= "SUM(CASE WHEN YEAR(a.created_at)={$year} THEN 1 ELSE 0 END) AS '{$year}', ";
        }

        $perProvinces = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', 'i.province_id', 'p.id')
            ->leftJoin('accreditations AS a', function ($join) {
                $join->on('a.institution_id', 'i.id')
                     ->where('a.status', Accreditation::STATUS_ACCREDITED);
            })
            ->select(DB::raw("p.name, {$yearSql} count(a.id) AS total"))
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();
        $perProvincesTotal = $perProvinces->splice(count($perProvinces)-1, 1);

        $data = [
            'per_provinces' => $perProvinces,
            'per_provinces_total' => $perProvincesTotal,
        ];

        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditationPerProvinceInYearExport(array_merge($data, ['years' => $years])))->download($today . " - Report Jumlah Akreditasi Berdasarkan Provinsi Dalam Tahun.xlsx");
        }

        return new Resource($data);
    }

    public function totalAccreditationsByProvincePerYear(Request $request)
    {
        if (!$request->has('year')) {
            return $this->totalAccreditationsByYear($request);
        }

        $predicateSql = "";
        foreach (['A', 'B', 'C'] as $predicate) {
            $predicateSql .= "SUM(CASE WHEN a.predicate='{$predicate}' THEN 1 ELSE 0 END) AS '{$predicate}', ";
        }

        $perProvinces = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', 'i.province_id', 'p.id')
            ->leftJoin('accreditations AS a', function ($join) use ($request) {
                $join->on('a.institution_id', 'i.id')
                     ->where('a.status', Accreditation::STATUS_ACCREDITED)
                     ->where(DB::raw('YEAR(a.created_at)'), $request->get('year'));
            })
            ->select(DB::raw("p.name, {$predicateSql} count(a.id) AS total"))
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();
        $perProvincesTotal = $perProvinces->splice(count($perProvinces)-1, 1);

        $data = [
            'per_provinces' => $perProvinces,
            'per_provinces_total' => $perProvincesTotal,
        ];
        if ($request->get('export')) {
            $year = $request->get('year');
            $today = now()->format('Ymd');
            return (new TotalAccreditationByProvincePerYearExport(array_merge($data, ['year' => $year])))->download($today . " - Report Jumlah Akreditasi Berdasarkan Provinsi Per Tahun {$year}.xlsx");
        }

        return new Resource($data);
    }

    public function totalAccreditedLibraries(Request $request)
    {
        $accreditations = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', 'it.category', 'i.category')
            ->select(DB::raw("it.category, count(i.id) as total, sum(case when i.accreditation_expires_at > now() then 1 else 0 end) as terakreditasi, sum(case when i.id is not null and (i.accreditation_expires_at is null or i.accreditation_expires_at <= now()) then 1 else 0 end) as belum_akreditasi"))
            ->groupByRaw('it.category WITH ROLLUP')
            ->get();
        $accreditationTotals = $accreditations->splice(count($accreditations)-1, 1);

        $provinces = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', 'p.id', 'i.province_id')
            ->select(DB::raw("p.name, count(i.id) as total, sum(case when i.accreditation_expires_at > now() then 1 else 0 end) as terakreditasi, sum(case when i.id is not null and (i.accreditation_expires_at is null or i.accreditation_expires_at <= now()) then 1 else 0 end) as belum_akreditasi"))
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();
        $provinceTotals = $provinces->splice(count($provinces)-1, 1);

        $data = [
            'libraries' => $accreditations,
            'library_total' => $accreditationTotals,
            'provinces' => $provinces,
            'province_total' => $provinceTotals,
        ];

        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditedLibraryExport($data))->download($today . " - Report Jumlah Perpustakaan Terakreditasi Terkini.xlsx");
        }

        return new Resource([
            'libraries' => $accreditations,
            'library_total' => $accreditationTotals,
            'provinces' => $provinces,
            'province_total' => $provinceTotals,
        ]);
    }

    public function totalAccreditedLibrariesWithinYear(Request $request)
    {
        if ($request->has('category')) {
            return $this->totalAccreditedLibrariesWithinYearByCategory($request);
        }
        $years = DB::table('institutions AS i')
            ->select(DB::raw("DISTINCT YEAR(i.accredited_at) AS tahun"))
            ->whereNotNull('i.accreditation_expires_at')
            ->where('i.accreditation_expires_at', '>', now())
            ->orderBy('tahun', 'asc')
            ->pluck('tahun')
            ->toArray();

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= "SUM(CASE WHEN i.id IS NOT NULL AND YEAR(i.accredited_at)={$year} AND i.accreditation_expires_at IS NOT NULL AND i.accreditation_expires_at > NOW() THEN 1 ELSE 0 END) AS '{$year}', ";
        }

        $yearData = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('it.category', 'i.category')
                     ->whereNotNull('i.accreditation_expires_at')
                     ->where('i.accreditation_expires_at', '>', now());
            })
            ->selectRaw("it.category, {$yearSql} COUNT(i.id) AS total")
            ->groupByRaw('it.category WITH ROLLUP')
            ->get();
        $totals = $yearData->splice(count($yearData)-1, 1);

        $data = [
            'year_data' => $yearData,
            'totals' => $totals,
        ];
        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditedLibraryWithinYearExport(array_merge($data, ['years' => $years])))->download($today . " - Report Jumlah Perpustakaan Terakreditasi Berdasarkan Jenis Perpustakaan Dalam Tahun.xlsx");
        }

        return new Resource($data);
    }

    public function totalAccreditedLibrariesWithinYearByCategory(Request $request)
    {
        $category = $request->get('category');
        $status = [
            'Jumlah Perpustakaan' => 0,
            'Total Terakreditasi' => 0,
            'Belum Terakreditasi' => 0,
        ];
        $earliestYear = DB::table('institutions AS i')
            ->select(DB::raw("DISTINCT YEAR(i.created_at) AS tahun"))
            ->orderBy('tahun', 'asc')
            ->first();
        $currentYear = now()->format('Y');
        $earliestYear = $earliestYear ? $earliestYear->tahun : $currentYear;
        $years = range((int) $earliestYear, (int) $currentYear);

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= ", SUM(CASE WHEN i.id IS NOT NULL AND YEAR(i.accredited_at) <= {$year} AND YEAR(i.accreditation_expires_at) >= {$year} THEN 1 ELSE 0 END) AS '{$year}'";
        }
        $accredited = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('i.category', 'it.category');
            })
            ->select(DB::raw("it.category {$yearSql}"))
            ->where('it.category', $category)
            ->first();
        $status['Total Terakreditasi'] = $accredited;

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= ", SUM(CASE WHEN i.id IS NOT NULL AND (i.accredited_at IS NULL OR YEAR(i.accredited_at) > {$year} OR i.accreditation_expires_at IS NULL OR YEAR(i.accreditation_expires_at) < {$year}) THEN 1 ELSE 0 END) AS '{$year}'";
        }
        $unaccredited = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('i.category', 'it.category');
            })
            ->select(DB::raw("it.category {$yearSql}"))
            ->where('it.category', $category)
            ->first();
        $status['Belum Terakreditasi'] = $unaccredited;

        $status['Jumlah Perpustakaan'] = (object) ['category' => $unaccredited->category];
        foreach ($years as $year) {
            $status['Jumlah Perpustakaan']->{$year} = $accredited->{$year} + $unaccredited->{$year};
        }

        return new Resource([
            'detail' => $status,
        ]);
    }

    public function totalAccreditedLibrariesPerYear(Request $request)
    {
        if ($request->has('year')) {
            return $this->totalAccreditedLibrariesPerYearByYear($request);
        }

        $years = DB::table('institutions AS i')
            ->select(DB::raw("DISTINCT YEAR(i.accredited_at) AS tahun"))
            ->whereNotNull('i.accreditation_expires_at')
            ->where('i.accreditation_expires_at', '>', now())
            ->orderBy('tahun', 'asc')
            ->pluck('tahun')
            ->toArray();

        return new Resource([
            'years' => $years,
        ]);
    }

    public function totalAccreditedLibrariesPerYearByYear(Request $request)
    {
        $year = $request->get('year');
        $yearData = DB::table('instruments AS it')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('it.category', 'i.category');
            })
            ->selectRaw("it.category, count(i.id) as total, sum(case when YEAR(i.accredited_at) <= ? AND YEAR(i.accreditation_expires_at) >= ? then 1 else 0 end) as terakreditasi, sum(case when i.id is not null and (i.accreditation_expires_at is null or YEAR(i.accreditation_expires_at) < ? OR YEAR(i.accredited_at) > ?) then 1 else 0 end) as belum_akreditasi",
                [$year, $year, $year, $year]
            )
            ->groupByRaw('it.category WITH ROLLUP')
            ->get();

        $yearTotals = $yearData->splice(count($yearData)-1, 1);

        $data = [
            'year_data' => $yearData,
            'year_totals' => $yearTotals,
        ];
        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditedLibraryPerYearExport(array_merge($data, ['year' => $year])))->download($today . " - Report Jumlah Perpustakaan Terakreditasi Berdasarkan Jenis Perpustakaan Pertahun {$year}.xlsx");
        }

        return new Resource($data);
    }

    public function totalAccreditedLibrariesByProvincesInYear(Request $request)
    {
        if ($request->has('province')) {
            return $this->totalAccreditedLibrariesByProvincesInYearDetail($request);
        }

        $earliestYear = DB::table('institutions AS i')
            ->select(DB::raw("DISTINCT YEAR(i.created_at) AS tahun"))
            ->orderBy('tahun', 'asc')
            ->first();
        $currentYear = now()->format('Y');
        $earliestYear = $earliestYear ? $earliestYear->tahun : $currentYear;
        $years = range((int) $earliestYear, (int) $currentYear);

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= ", SUM(CASE WHEN i.id IS NOT NULL AND YEAR(i.accredited_at) <= {$year} AND YEAR(i.accreditation_expires_at) >= {$year} THEN 1 ELSE 0 END) AS '{$year}'";
        }
        $provinces = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('i.province_id', 'p.id');
            })
            ->select(DB::raw("p.name {$yearSql}"))
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();
        $provinceTotals = $provinces->splice(count($provinces)-1, 1);

        $data = [
            'provinces' => $provinces,
            'province_totals' => $provinceTotals,
        ];
        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditedLibraryByProvinceInYearExport(array_merge($data, ['years' => $years])))->download($today . " - Report Jumlah Perpustakaan Terakreditasi Berdasarkan Provinsi Dalam Tahun.xlsx");
        }

        return new Resource($data);
    }

    public function totalAccreditedLibrariesByProvincesInYearDetail(Request $request)
    {
        $province = $request->get('province');
        $status = [
            'Jumlah Perpustakaan' => 0,
            'Total Terakreditasi' => 0,
            'Belum Terakreditasi' => 0,
        ];
        $earliestYear = DB::table('institutions AS i')
            ->select(DB::raw("DISTINCT YEAR(i.created_at) AS tahun"))
            ->orderBy('tahun', 'asc')
            ->first();
        $currentYear = now()->format('Y');
        $earliestYear = $earliestYear ? $earliestYear->tahun : $currentYear;
        $years = range((int) $earliestYear, (int) $currentYear);

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= ", SUM(CASE WHEN i.id IS NOT NULL AND YEAR(i.accredited_at) <= {$year} AND YEAR(i.accreditation_expires_at) >= {$year} THEN 1 ELSE 0 END) AS '{$year}'";
        }
        $accredited = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('i.province_id', 'p.id');
            })
            ->select(DB::raw("p.name {$yearSql}"))
            ->where('p.name', $province)
            ->first();
        $status['Total Terakreditasi'] = $accredited;

        $yearSql = "";
        foreach ($years as $year) {
            $yearSql .= ", SUM(CASE WHEN i.id IS NOT NULL AND (i.accredited_at IS NULL OR YEAR(i.accredited_at) > {$year} OR i.accreditation_expires_at IS NULL OR YEAR(i.accreditation_expires_at) < {$year}) THEN 1 ELSE 0 END) AS '{$year}'";
        }
        $unaccredited = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('i.province_id', 'p.id');
            })
            ->select(DB::raw("p.name {$yearSql}"))
            ->where('p.name', $province)
            ->first();
        $status['Belum Terakreditasi'] = $unaccredited;

        $status['Jumlah Perpustakaan'] = (object) ['name' => $unaccredited->name];
        foreach ($years as $year) {
            $status['Jumlah Perpustakaan']->{$year} = $accredited->{$year} + $unaccredited->{$year};
        }

        return new Resource([
            'detail' => $status,
        ]);
    }

    public function totalAccreditedLibrariesByProvincePerYear(Request $request)
    {
        $year = $request->get('year');
        if (!$year) {
            return $this->totalAccreditedLibrariesPerYear($request);
        }

        $yearData = DB::table('provinces AS p')
            ->leftJoin('institutions AS i', function ($join) {
                $join->on('p.id', 'i.province_id');
            })
            ->selectRaw("p.name, count(i.id) as total, sum(case when i.id IS NOT NULL AND YEAR(i.accredited_at) <= ? AND YEAR(i.accreditation_expires_at) >= ? then 1 else 0 end) as terakreditasi, sum(case when i.id is not null and (i.accreditation_expires_at is null or YEAR(i.accreditation_expires_at) < ? OR YEAR(i.accredited_at) > ?) then 1 else 0 end) as belum_akreditasi",
                [$year, $year, $year, $year]
            )
            ->groupByRaw('p.name WITH ROLLUP')
            ->get();

        $yearTotals = $yearData->splice(count($yearData)-1, 1);

        $data = [
            'year_data' => $yearData,
            'year_totals' => $yearTotals,
        ];
        if ($request->get('export')) {
            $today = now()->format('Ymd');
            return (new TotalAccreditedLibraryByProvincePerYearExport(array_merge($data, ['year' => $year])))->download($today . " - Report Jumlah Perpustakaan Terakreditasi Berdasarkan Provinsi Per Tahun {$year}.xlsx");
        }

        return new Resource($data);
    }
}
