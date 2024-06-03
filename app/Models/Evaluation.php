<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Filterable;
    use Traits\Loggable;
    use Traits\AccreditationAware;

    protected $fillable = [
        'accreditation_id',
        'institution_id',
        'assessor_id',
        'recommendations',
    ];

    protected $filterable = [
        'accreditation:code',
        'institution:library_name',
    ];

    protected $casts = [
        'recommendations' => 'array',
    ];

    public function accreditation()
    {
        return $this->belongsTo(Accreditation::class);
    }

    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessor_id');
    }

    public function contents()
    {
        return $this->hasMany(EvaluationContent::class);
    }

    public function loadResult()
    {
        $this->result = $this->evaluationResult();
        $this->finalResult = $this->finalResult();
        return $this;
    }

    public function evaluationResult()
    {
        $result = [];

        $mainComponents = \DB::table('accreditation_contents AS ac')
            ->join('instrument_components AS ic', 'ac.main_component_id', 'ic.id')
            ->where('ac.accreditation_id', $this->accreditation_id)
            ->orderBy('ic.order', 'asc')
            ->select('main_component_id', 'ic.name', 'ic.weight')
            ->distinct()
            ->get();

        foreach ($mainComponents as $comp) {

            $contents = \DB::table('evaluation_contents AS ec')
                ->join('accreditation_contents AS ac', 'ec.accreditation_content_id', 'ac.id')
                ->join('instrument_aspects AS ia', 'ac.aspectable_id', 'ia.id')
                ->join('instrument_components AS ic', 'ia.instrument_component_id', 'ic.id')
                ->where('ec.evaluation_id', $this->id)
                ->where('ac.main_component_id', $comp->main_component_id)
                ->whereNull('ia.deleted_at')
                ->select(\DB::raw("COUNT(ec.id) as jml_soal, SUM(ec.value) as total, COUNT(ec.id) * 5 as jml_skor"))
                ->first();

            $onthespot = \DB::table('evaluation_contents AS ec')
                ->join('accreditation_contents AS ac', 'ec.accreditation_content_id', 'ac.id')
                ->join('instrument_aspects AS ia', 'ac.aspectable_id', 'ia.id')
                ->join('instrument_components AS ic', 'ia.instrument_component_id', 'ic.id')
                ->where('ec.evaluation_id', $this->id)
                ->where('ac.main_component_id', $comp->main_component_id)
                ->whereNull('ia.deleted_at')
                ->select(\DB::raw("ec.value as total"))
                ->get();    

            $result[] = [
                'instrument_component' => $comp->name,
                'instrument_component_id' => $comp->main_component_id,
                'total_instrument' => $contents->jml_soal,
                'total_value' => (int) $contents->total,
                'total_score' => $contents->jml_skor,
                'weight' => $comp->weight,
                'score' => $contents->jml_soal ? $this->calculateScore($contents->jml_soal, $contents->total, $comp->weight) : 0,
                'onthespot' => $onthespot,
            ];

        }

        $this->evaluationResult = $result;

        return $result;
    }

    public function finalResult()
    {
        if (!isset($this->evaluationResult)) {
            $this->evaluationResult = $this->evaluationResult();
        }

        $finalResult = [
            'total_instrument' => 0,
            'total_score' => 0,
            'total_value' => 0,
            'weight' => 0,
            'score' => 0,
        ];
        foreach ($this->evaluationResult as $result) {
            foreach ($finalResult as $key => $value) {
                $finalResult[$key] += $result[$key];
            }
        }

        $finalResult['predicate'] = $this->calculatePredicate($finalResult['score']);

        return $finalResult;
    }
}
