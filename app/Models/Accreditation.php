<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Str;

class Accreditation extends Model
{
    use HasFactory;
    use Traits\HasUuidPrimaryKey;
    use Traits\Loggable;
    use Traits\AccreditationAware;
    use Traits\Filterable;

    public const STATUS_SUBMITTED = 'diajukan';
    public const STATUS_VERIFIED = 'dinilai';
    public const STATUS_REVIEWED = 'ditinjau';
    public const STATUS_EVALUATED = 'penilaian_rapat';
    public const STATUS_ACCREDITED = 'terakreditasi';
    public const STATUS_INCOMPLETE = 'belum_lengkap';

    public const TYPE_NEW = 'baru';
    public const TYPE_REACCREDITATION = 'reakreditasi';
    public const TYPE_APPEAL = 'banding';

    public const CERT_STATUS_ACCREDITED = 'terakreditasi';
    public const CERT_STATUS_SIGNED = 'ditandatangani';
    public const CERT_STATUS_SENT = 'dikirim';
    public const CERT_STATUS_PRINTED = 'cetak_sertifikat';

    protected $fillable = [
        'code',
        'institution_id',
        'user_id',
        'notes',
        'status',
        'type',
        'certificate_file',
        'recommendation_file',
    ];

    protected $filterable = [
        'code',
        'institution:agency_name',
    ];

    protected $casts = [
        'meeting_date' => 'date',
        'certificate_expires_at' => 'datetime',
        'certificate_sent_at' => 'date',
    ];

    protected static $logAttributes = [
        'notes',
        'predicate',
        'accredited_at',
        'certificate_status',
        'certificate_file',
        'recommendation_file',
        'certificate_sent_at',
        'certificate_expires_at',
        'meeting_date',
    ];
    
    public function institution()
    {
        return $this->belongsTo(Institution::class);
    }

    public function contents()
    {
        return $this->hasMany(AccreditationContent::class);
    }

    public function evaluationAssignments()
    {
        return $this->hasMany(EvaluationAssignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function evaluation()
    {
        return $this->hasOne(Evaluation::class);
    }

    public function scopeSubmitted($query)
    {
        return $query->where('status', static::STATUS_SUBMITTED);
    }

    public function scopeIncomplete($query)
    {
        return $query->where('status', static::STATUS_INCOMPLETE);
    }

    public function scopeReviewed($query)
    {
        return $query->where('status', static::STATUS_REVIEWED);
    }

    public function scopeEvaluated($query)
    {
        return $query->where('status', static::STATUS_EVALUATED);
    }

    public function scopeAcceptable($query)
    {
        return $query->where('status', static::STATUS_EVALUATED)
                     ->whereNull('appealed_at');
    }

    public function scopeCanBeEvaluated($query, $userId)
    {
        return $query->whereIn('status', [
            static::STATUS_VERIFIED,
            static::STATUS_REVIEWED,
            static::STATUS_EVALUATED,
            static::STATUS_ACCREDITED,
        ])->whereHas('evaluationAssignments', function ($query) use ($userId) {
            return $query->whereHas('assessors', function ($query) use ($userId) {
                return $query->where('user_id', $userId);
            });
        });
    }

    public function scopeAccredited($query)
    {
        return $query->where('status', static::STATUS_ACCREDITED);
    }

    public function scopeInProgress($query)
    {
        return $query->whereIn('status', [
            static::STATUS_SUBMITTED,
            static::STATUS_VERIFIED,
            static::STATUS_REVIEWED,
            static::STATUS_EVALUATED,
            static::STATUS_ACCREDITED,
        ]);
    }

    public function setSubmitted()
    {
        $this->status = static::STATUS_SUBMITTED;
        return $this;
    }

    public function setVerified()
    {
        $this->status = static::STATUS_VERIFIED;
        return $this;
    }

    public function setReviewed()
    {
        $this->status = static::STATUS_REVIEWED;
        return $this;
    }

    public function setEvaluated()
    {
        $this->status = static::STATUS_EVALUATED;
        return $this;
    }

    public function setIncomplete()
    {
        $this->status = static::STATUS_INCOMPLETE;
        return $this;
    }

    public function setAccredited()
    {
        $this->status = static::STATUS_ACCREDITED;
        $this->certificate_status = static::CERT_STATUS_ACCREDITED;
        $this->accredited_at = now();
        return $this;
    }

    public function setAppealed()
    {
        $this->appealed_at = now();
        return $this;
    }

    public function setCertificateExpiration()
    {
        if ($this->meeting_date && $this->predicate) {
            switch ($this->predicate) {
            case 'A':
                $expires = $this->meeting_date->addYears(5);
                break;
            case 'B':
                $expires = $this->meeting_date->addYears(4);
                break;
            case 'C':
                $expires = $this->meeting_date->addYears(3);
                break;
            default:
                $expires = null;
            }

            $this->certificate_expires_at = $expires;
        }

        return $this;
    }

    public function isIncomplete()
    {
        return $this->status === static::STATUS_INCOMPLETE;
    }

    public function isAccredited()
    {
        return $this->status === static::STATUS_ACCREDITED;
    }

    public function hasBeenReviewed()
    {
        $reviewed = array_keys($this->statusList(), static::STATUS_REVIEWED)[0];
        $current = array_keys($this->statusList(), $this->status)[0];

        return $current >= $reviewed;
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

        $mainComponents = \DB::table('accreditation_contents AS ac')
            ->join('instrument_components AS ic', 'ac.main_component_id', 'ic.id')
            ->where('ac.accreditation_id', $this->id)
            ->orderBy('ic.order', 'asc')
            ->select('main_component_id', 'ic.name', 'ic.weight')
            ->distinct()
            ->get();

        foreach ($mainComponents as $comp) {
            $contents = \DB::table('accreditation_contents AS ac')
                ->join('instrument_aspects AS ia', 'ac.aspectable_id', 'ia.id')
                ->join('instrument_components AS ic', 'ia.instrument_component_id', 'ic.id')
                ->where('ac.accreditation_id', $this->id)
                ->where('ac.main_component_id', $comp->main_component_id)
                ->where('ac.type', 'choice')
                ->whereNull('ia.deleted_at')
                ->select(\DB::raw("COUNT(ac.id) as jml_soal, SUM(ac.value) as total, COUNT(ac.id) * 5 as jml_skor"))
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

        return $finalResult;
    }

    public function isCertificateExpired()
    {
        if ($this->certificate_expires_at) {
            return now()->isAfter($this->certificate_expires_at);
        }

        return false;
    }

    public function isReaccreditationEligible()
    {
        if ($this->certificate_expires_at && $this->meeting_date) {
            $now = now();
            return $now->isAfter($this->meeting_date->addYear(1)) 
                && $now->isBefore($this->certificate_expires_at);
        }

        return false;
    }

    public function newAppeal()
    {
        if (!$this->appealed_at) {
            return null;
        }

        $accreditation = new static();
        \DB::transaction(function () use ($accreditation) {
            $accreditation->code = $this->newCode();
            $accreditation->institution_id = $this->institution_id;
            $accreditation->user_id = $this->user_id;
            $accreditation->type = static::TYPE_APPEAL;
            $accreditation->status = static::STATUS_INCOMPLETE;
            $accreditation->save();
        });

        foreach ($this->contents()->where('type', 'choice')->get() as $content) {
            $newContent = new AccreditationContent();
            $newContent->accreditation_id = $accreditation->id;
            $newContent->aspectable_type = $content->aspectable_type;
            $newContent->aspectable_id = $content->aspectable_id;
            $newContent->main_component_id = $content->main_component_id;
            $newContent->instrument_aspect_point_id = $content->instrument_aspect_point_id;
            $newContent->type = $content->type;
            $newContent->aspect = $content->aspect;
            $newContent->statement = $content->statement;
            $newContent->value = $content->value;
            $newContent->file = $content->file;
            $newContent->save();
        }

        return $accreditation;
    }

    public static function statusList()
    {
        return [
            static::STATUS_SUBMITTED,
            static::STATUS_VERIFIED,
            static::STATUS_REVIEWED,
            static::STATUS_EVALUATED,
            static::STATUS_ACCREDITED,
        ];
    }

    public static function typeList()
    {
        return [
            static::TYPE_NEW,
            static::TYPE_REACCREDITATION,
            static::TYPE_APPEAL,
        ];
    }

    public static function predicateList()
    {
        return [
            'A',
            'B',
            'C',
            'Tidak Akreditasi',
        ];
    }

    public static function certificateStatusList()
    {
        return [
            self::CERT_STATUS_ACCREDITED,
            self::CERT_STATUS_SIGNED,
            self::CERT_STATUS_SENT,
            self::CERT_STATUS_PRINTED,
        ];
    }
}
