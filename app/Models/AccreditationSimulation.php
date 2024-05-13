<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class AccreditationSimulation extends Model
{
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;
    use Traits\AccreditationAware;
    use Traits\Filterable;

    public const STATUS_SUBMITTED = 'diajukan';
    public const STATUS_INCOMPLETE = 'belum_lengkap';

    protected $fillable = [
        'code',
        'status',
        'predicate',
        'institution_id',
        'user_id',
        'type',
    ];

    protected $filterable = [
        'code',
        'institution:agency_name',
    ];

    protected static $logAttributes = [
        'predicate',
    ];

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function contents()
    {
        return $this->hasMany(AccreditationSimulationContent::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', static::STATUS_INCOMPLETE);
    }

    public function isSubmitted()
    {
        return $this->status === static::STATUS_SUBMITTED;
    }

    public function setSubmitted()
    {
        $this->status = static::STATUS_SUBMITTED;
        return $this;
    }

    public function loadResult()
    {
        $this->result = $this->results();
        $this->finalResult = $this->finalResult();
        return $this;
    }

    public function results()
    {
        $result = [];

        $mainComponents = \DB::table('accreditation_simulation_contents AS ac')
            ->join('instrument_components AS ic', 'ac.main_component_id', 'ic.id')
            ->where('ac.accreditation_simulation_id', $this->id)
            ->orderBy('ic.order', 'asc')
            ->select('main_component_id', 'ic.name', 'ic.weight')
            ->distinct()
            ->get();

        foreach ($mainComponents as $comp) {
            $contents = $this->contents()
                             ->where('main_component_id', $comp->main_component_id)
                             ->where('type', 'choice')
                             ->select(\DB::raw("COUNT(id) as jml_soal, SUM(value) as total, COUNT(id) * 5 as jml_skor"))
                             ->first();
            $result[] = [
                'instrument_component' => $comp->name,
                'instrument_component_id' => $comp->main_component_id,
                'total_instrument' => $contents->jml_soal,
                'total_score' => $contents->jml_skor,
                'total_value' => (int) $contents->total,
                'weight' => $comp->weight,
                'score' => $contents->jml_soal ? $this->calculateScore($contents->jml_soal, $contents->total, $comp->weight) : 0,
            ];
        }

        return $result;
    }

    public function finalResult()
    {
        if (!isset($this->result)) {
            $this->result = $this->results();
        }

        $finalResult = [
            'total_instrument' => 0,
            'total_score' => 0,
            'total_value' => 0,
            'weight' => 0,
            'score' => 0,
        ];
        foreach ($this->result as $result) {
            foreach ($finalResult as $key => $value) {
                $finalResult[$key] += $result[$key];
            }
        }

        $finalResult['predicate'] = $finalResult['score'] >= 60 ? 'Selamat, perpustakaan Anda telah memenuhi Standar Nasional Perpustakaan. Silahkan ajukan akreditasi' : 'Maaf, perpustakaan anda belum memenuhi Standar Nasional Perpustakaan sehingga belum dapat diakreditasi. Silahkan berkonsultasi pada Dinas Perpustakaan Setempat (Kab./Kota atau Provinsi) untuk mendapatkan pembinaan.';

        return $finalResult;
    }
}
