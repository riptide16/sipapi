<?php

namespace App\Http\Controllers\Admin;

use App\Models\AccreditationSimulation;
use App\Models\AccreditationSimulationContent;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use App\Models\User;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubmitAccreditationRequest;
use App\Http\Resources\AccreditationSimulationCollection;
use App\Http\Resources\AccreditationSimulationResource;
use App\Http\Resources\AccreditationSimulationContentCollection;
use App\Http\Resources\Resource;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Storage;

class AccreditationSimulationController extends Controller
{
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitAccreditationRequest $request)
    {
        $user = $request->user();

        $contents = $request->all()['contents'];
        $type = $request->get('type');

        $accreditation = AccreditationSimulation::orderBy('created_at', 'desc')->firstOrNew([
            'institution_id' => $user->institution->id,
            'user_id' => $user->id,
            'status' => AccreditationSimulation::STATUS_INCOMPLETE,
        ]);
        if (!$accreditation->exists) {
            \DB::transaction(function () use ($accreditation, $type) {
                $accreditation->fill([
                    'code' => AccreditationSimulation::newCode(),
                ])->save();
            }, 3);
        }

        $savedContents = [];
        foreach ($contents as $content) {
            $data = [
                'accreditation_simulation_id' => $accreditation->id,
                'type' => $content['type'],
            ];

            $aspect = InstrumentAspect::find($content['instrument_aspect_id']);
            $data['aspect'] = $aspect->aspect;
            $data['aspectable_type'] = InstrumentAspect::class;
            $data['aspectable_id'] = $aspect->id;
            if ($aspect->isMultiAspect()) {
                $childAspects = $aspect->children->pluck('id')->toArray();
                $point = InstrumentAspectPoint::whereIn('instrument_aspect_id', $childAspects)
                    ->find($content['instrument_aspect_point_id']);
            } else {
                $point = $aspect->points()->find($content['instrument_aspect_point_id']);
            }
            $data['instrument_aspect_point_id'] = $point->id;
            $data['main_component_id'] = $aspect->instrumentComponent->ancestor()->id;
            $data['statement'] = $point ? $point->statement : null;
            $data['value'] = $point ? $point->value : null;

            $savedContent = AccreditationSimulationContent::firstOrNew([
                'accreditation_simulation_id' => $data['accreditation_simulation_id'],
                'type' => $data['type'],
                'aspectable_type' => $data['aspectable_type'],
                'aspectable_id' => $data['aspectable_id'],
            ]);
            if (!$savedContent->exists) {
                $savedContent->fill($data)->save();
            } else {
                if (isset($data['file']) && $savedContent->file) {
                    Storage::disk('local')->delete($savedContent->file);
                }
                $savedContent->update($data);
            }
            $savedContents[] = $savedContent;
        }

        $accreditation->refresh()->load('contents');

        return new AccreditationSimulationResource($accreditation, 201);
    }

    public function finalize(Request $request, $id)
    {
        $accreditation = AccreditationSimulation::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'is_finalized' => 'required|boolean',
        ]);

        if ((bool) $request->get('is_finalized')) {
            $accreditation->setSubmitted()->save();
            $accreditation->loadResult();
            $accreditation->predicate = $accreditation->finalResult['predicate'];

            AccreditationSimulation::where('id', $accreditation->id)->update(['predicate' => $accreditation->predicate]);
        }

        return new AccreditationSimulationResource($accreditation);
    }
}
