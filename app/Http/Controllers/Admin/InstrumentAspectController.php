<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Http\Requests\StoreBulkInstrumentAspectRequest;
use App\Http\Requests\UpdateBulkInstrumentAspectRequest;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\InstrumentAspectCollection;
use App\Http\Resources\InstrumentAspectResource;
use App\Http\Resources\Resource;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Arr;

class InstrumentAspectController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request, $instrumentId)
    {
        $instrument = Instrument::findOrFail($instrumentId);
        return new InstrumentAspectCollection(
            $instrument->aspects()->with(['points', 'instrumentComponent'])->whereNull('parent_id')->get()
        );
    }

    /**
     * Store a newly created resource in bulk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function bulkStore(StoreBulkInstrumentAspectRequest $request, $instrumentId)
    {
        $instrument = Instrument::findOrFail($instrumentId);

        $data = $request->all();
        $aspects = new Collection();
        foreach ($data['aspects'] as $aspectSubmission) {
            $aspect = InstrumentAspect::create(array_merge($aspectSubmission, ['instrument_id' => $instrument->id]));

            if ($aspect->isMultiAspect()) {
                foreach ($aspectSubmission['children'] as $childAspect) {
                    $child = InstrumentAspect::create(array_merge($childAspect, [
                        'instrument_id' => $instrument->id,
                        'instrument_component_id' => $aspectSubmission['instrument_component_id'],
                        'parent_id' => $aspect->id,
                        'type' => 'choice',
                    ]));
                    $child = $this->storePointsToAspect($childAspect['points'], $child);
                }
                $aspect->load('children.points');
            } else {
                $aspect = $this->storePointsToAspect($aspectSubmission['points'], $aspect);
                $aspect->load('points');
            }
            $aspects->push($aspect);
        }

        return new InstrumentAspectCollection($aspects, 201);
    }

    private function storePointsToAspect(array $points, InstrumentAspect $aspect)
    {
        // Sort based on order
        usort($points, function ($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }

            return $a['order'] < $b['order'] ? -1 : 1;
        });

        // Get only the first 5 points
        $points = array_slice($points, 0, 5);

        // Append value to choices
        if ($aspect->isChoice()) {
            $size = count($points);
            $points = array_map(function ($point) use (&$size) {
                return array_merge($point, [
                    'value' => $size--,
                ]);
            }, $points);
        }

        $aspect->points()->createMany($points);

        return $aspect;
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($instrumentId, $aspectId)
    {
        $aspect = InstrumentAspect::whereHas('instrument', function ($q) use ($instrumentId) {
            $q->where('id', $instrumentId);
        })->with(['points', 'instrumentComponent.parent.parent', 'children.points'])->findOrFail($aspectId);

        return new InstrumentAspectResource($aspect);
    }

    /**
     * Update the specified resource in bulk.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function bulkUpdate(UpdateBulkInstrumentAspectRequest $request, $instrumentId)
    {
        $instrument = Instrument::findOrFail($instrumentId);

        $data = $request->all();
        $aspects = new Collection();
        foreach ($data['aspects'] as $aspectSubmission) {
            if (isset($aspectSubmission['id'])) {
                $aspect = InstrumentAspect::find($aspectSubmission['id']);
                $aspect->update(Arr::only($aspectSubmission, $aspect->getFillable()));
            } else {
                $aspect = InstrumentAspect::create(array_merge($aspectSubmission, ['instrument_id' => $instrument->id]));
            }

            if ($aspect->isMultiAspect()) {
                foreach ($aspectSubmission['children'] as $childAspect) {
                    if (isset($childAspect['id'])) {
                        $child = InstrumentAspect::find($childAspect['id']);
                        $child->update(Arr::only($childAspect, $child->getFillable()));
                    } else {
                        $child = InstrumentAspect::create(array_merge($childAspect, [
                            'instrument_id' => $instrument->id,
                            'instrument_component_id' => $aspectSubmission['instrument_component_id'],
                            'parent_id' => $aspect->id,
                            'type' => 'choice',
                        ]));
                    }
                    $child = $this->updateOrStorePointsToAspect($childAspect['points'], $childAspect, $child);
                }
                $aspect->load('children.points');
            } else {
                $aspect = $this->updateOrStorePointsToAspect($aspectSubmission['points'], $aspectSubmission, $aspect);
                $aspect->load('points');
            }

            $aspect->load('points');
            $aspects->push($aspect);
        }

        return new InstrumentAspectCollection($aspects);
    }

    private function updateOrStorePointsToAspect(array $points, array $aspectSubmission, InstrumentAspect $aspect)
    {
        $currentPointIds = $aspect->points->pluck('id')->toArray();

        // Sort based on order
        usort($points, function ($a, $b) {
            if ($a['order'] == $b['order']) {
                return 0;
            }

            return $a['order'] < $b['order'] ? -1 : 1;
        });

        // Get only the first 5 points
        $points = array_slice($points, 0, 5);

        // Append value to choices
        if ($aspect->isChoice()) {
            $size = count($points);
            $points = array_map(function ($point) use (&$size) {
                return array_merge($point, [
                    'value' => $size--,
                ]);
            }, $points);
        }

        // Update existing data and distinguish newly created point
        $new = $existingPointIds = [];
        foreach ($points as $point) {
            if (isset($aspectSubmission['id']) && isset($point['id'])) {
                $aspectPoint = InstrumentAspectPoint::find($point['id']);
                $aspectPoint->update($point);

                $existingPointIds[] = $point['id'];
                continue;
            }

            $new[] = $point;
        }

        $aspect->points()->createMany($new);

        // Delete points that's not present
        $deleteIds = array_diff($currentPointIds, $existingPointIds);
        InstrumentAspectPoint::whereIn('id', $deleteIds)->forceDelete();

        return $aspect;
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($instrumentId, $aspectId)
    {
        $aspect = InstrumentAspect::whereHas('instrument', function ($q) use ($instrumentId) {
            $q->where('id', $instrumentId);
        })->findOrFail($aspectId);

        $aspect->points()->delete();
        $aspect->delete();

        return new Resource();
    }
}
