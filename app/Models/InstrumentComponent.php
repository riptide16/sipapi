<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class InstrumentComponent extends Model
{
    use HasFactory;
    use SoftDeletes;
    use Traits\HasUuidPrimaryKey;
    use Traits\HasCategory;
    use Traits\Loggable;

    const TYPE_MAIN = 'main';
    const TYPE_SUB_1 = 'sub_1';
    const TYPE_SUB_2 = 'sub_2';

    protected $fillable = [
        'category',
        'name',
        'type',
        'parent_id',
        'weight',
        'order',
    ];

    protected $filterable = [
        'category',
        'type',
        'parent_id',
    ];

    public function parent()
    {
        return $this->belongsTo(static::class, 'parent_id');
    }

    public function children()
    {
        return $this->hasMany(static::class, 'parent_id');
    }

    public function ancestor()
    {
        switch ($this->type) {
        case static::TYPE_MAIN:
            return $this;
        case static::TYPE_SUB_1:
            return $this->parent;
        case static::TYPE_SUB_2:
            return $this->parent->parent;
        }
    }

    public function aspects()
    {
        return $this->hasMany(InstrumentAspect::class)->sort();
    }

    public function accreditationContents()
    {
        return $this->morphMany(AccreditationContent::class, 'aspectable');
    }

    public function instrument()
    {
        return $this->belongsTo(Instrument::class, 'category', 'category');
    }

    public function loadParents()
    {
        if ($this->parent_id) {
            $this->load('parent');
            $this->parent->loadParents();
        }
    }

    public function scopeFilter($query, $filters)
    {
        foreach ($this->filterable as $field) {
            if (isset($filters[$field])) {
                $query->where($field, $filters[$field]);
            }
        }
    }

    public function scopeAssesseeForms($query, $aspectType, $category, $accreditationId = null)
    {
        $instrumentId = Instrument::where('category', $category)->first()->id;

        if (in_array($aspectType, [InstrumentAspect::TYPE_CHOICE, InstrumentAspect::TYPE_MULTI_ASPECT])) {
            $aspectTypes = [InstrumentAspect::TYPE_CHOICE, InstrumentAspect::TYPE_MULTI_ASPECT];
            $query->with([
                'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                    $withs = ['points'];
                    $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                    if ($accreditationId) {
                        $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                            $query->where('accreditation_id', $accreditationId);
                            $query->with('evaluationContent');
                        };
                    }

                    $withs['children'] = function ($query) use ($accreditationId) {
                        $withs = ['points'];
                        if ($accreditationId) {
                            $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                                $query->where('accreditation_id', $accreditationId);
                            };
                        }
                        $query->with($withs);
                    };

                    $query->with($withs);
                },
                'children' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                    $query->with([
                        'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                            $withs = ['points'];
                            $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                            if ($accreditationId) {
                                $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                                    $query->where('accreditation_id', $accreditationId);
                                    $query->with('evaluationContent');
                                };
                            }

                            $withs['children'] = function ($query) use ($accreditationId) {
                                $withs = ['points'];
                                if ($accreditationId) {
                                    $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                                        $query->where('accreditation_id', $accreditationId);
                                    };
                                }
                                $query->with($withs);
                            };

                            $query->with($withs);
                        },
                        'children' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                            $query->with([
                                'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                                    $withs = ['points'];
                                    $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                                    if ($accreditationId) {
                                        $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                                            $query->where('accreditation_id', $accreditationId);
                                            $query->with('evaluationContent');
                                        };
                                    }

                                    $withs['children'] = function ($query) use ($accreditationId) {
                                        $withs = ['points'];
                                        if ($accreditationId) {
                                            $withs['accreditationContents'] = function ($query) use ($accreditationId) {
                                                $query->where('accreditation_id', $accreditationId);
                                            };
                                        }
                                        $query->with($withs);
                                    };

                                    $query->with($withs);
                                },
                            ]);
                        },
                    ]);
                },
            ]);
        } else {
            if ($accreditationId) {
                $query->with(['accreditationContents' => function ($query) use ($accreditationId, $aspectType) {
                    $query->where('accreditation_id', $accreditationId)
                          ->where('type', $aspectType);
                }]);
            }
        }
        $query->where('type', 'main');
    }

    public function scopeAssesseeFormSimulations($query, $aspectType, $category, $accreditationId)
    {
        $instrumentId = Instrument::where('category', $category)->first()->id;

        if (in_array($aspectType, [InstrumentAspect::TYPE_CHOICE, InstrumentAspect::TYPE_MULTI_ASPECT])) {
            $aspectTypes = [InstrumentAspect::TYPE_CHOICE, InstrumentAspect::TYPE_MULTI_ASPECT];
            $query->with([
                'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                    $withs = ['points'];
                    $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                    if ($accreditationId) {
                        $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                            $query->where('accreditation_simulation_id', $accreditationId);
                        };
                    }

                    $withs['children'] = function ($query) use ($accreditationId) {
                        $withs = ['points'];
                        if ($accreditationId) {
                            $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                                $query->where('accreditation_simulation_id', $accreditationId);
                            };
                        }
                        $query->with($withs);
                    };

                    $query->with($withs);
                },
                'children' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                    $query->with([
                        'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                            $withs = ['points'];
                            $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                            if ($accreditationId) {
                                $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                                    $query->where('accreditation_simulation_id', $accreditationId);
                                };
                            }

                            $withs['children'] = function ($query) use ($accreditationId) {
                                $withs = ['points'];
                                if ($accreditationId) {
                                    $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                                        $query->where('accreditation_simulation_id', $accreditationId);
                                    };
                                }
                                $query->with($withs);
                            };

                            $query->with($withs);
                        },
                        'children' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                            $query->with([
                                'aspects' => function ($query) use ($aspectTypes, $accreditationId, $instrumentId) {
                                    $withs = ['points'];
                                    $query->whereIn('type', $aspectTypes)->whereNull('parent_id')->where('instrument_id', $instrumentId);

                                    if ($accreditationId) {
                                        $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                                            $query->where('accreditation_simulation_id', $accreditationId);
                                        };
                                    }

                                    $withs['children'] = function ($query) use ($accreditationId) {
                                        $withs = ['points'];
                                        if ($accreditationId) {
                                            $withs['accreditationSimulationContents'] = function ($query) use ($accreditationId) {
                                                $query->where('accreditation_simulation_id', $accreditationId);
                                            };
                                        }
                                        $query->with($withs);
                                    };

                                    $query->with($withs);
                                },
                            ]);
                        },
                    ]);
                },
            ]);
        } else {
            if ($accreditationId) {
                $query->with(['accreditationSimulationContents' => function ($query) use ($accreditationId, $aspectType) {
                    $query->where('accreditation_simulation_id', $accreditationId)
                          ->where('type', $aspectType);
                }]);
            }
        }
        $query->where('type', 'main');
    }

    public function canBeAttachedWith($type)
    {
        switch ($this->type) {
        case static::TYPE_MAIN:
            if ($type === static::TYPE_SUB_1) {
                return true;
            }
            break;
        case static::TYPE_SUB_1:
            if ($type === static::TYPE_SUB_2) {
                return true;
            }
        default:
            return false;
        }
    }

    public function isMain()
    {
        return $this->type === static::TYPE_MAIN;
    }

    public static function typeList()
    {
        return [
            static::TYPE_MAIN,
            static::TYPE_SUB_1,
            static::TYPE_SUB_2,
        ];
    }
}
