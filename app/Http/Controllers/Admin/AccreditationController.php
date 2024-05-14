<?php

namespace App\Http\Controllers\Admin;

use App\Events\AccreditationCompleted;
use App\Models\Accreditation;
use App\Models\AccreditationContent;
use App\Models\EvaluationAssignment;
use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentAspectPoint;
use App\Models\InstrumentComponent;
use App\Models\User;
use App\Models\Role;
use App\Notifications\VerifyNewAccreditation;
use App\Notifications\ResubmitIncompleteAccreditation;
use App\Notifications\AccreditationVerified;
use App\Notifications\EvaluateAccreditation;
use App\Notifications\AcceptAccreditationEvaluation;
use App\Notifications\AccreditationAccepted;
use App\Notifications\AccreditationAppealed;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubmitAccreditationRequest;
use App\Http\Resources\AccreditationCollection;
use App\Http\Resources\AccreditationResource;
use App\Http\Resources\AccreditationContentCollection;
use App\Http\Resources\EvaluationAssignmentResource;
use App\Http\Resources\EvaluationAssignmentCollection;
use App\Http\Resources\InstrumentResource;
use App\Http\Resources\InstrumentComponentCollection;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Resource;
use App\Support\CanPaginate;
use Illuminate\Http\Request;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Support\Facades\Notification;
use Illuminate\Pagination\Paginator;
use Illuminate\Validation\Rule;
use Storage;
use Arr;

class AccreditationController extends Controller
{
    use CanPaginate;

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();

        if ($user->isAssessee() && !optional($user->institution)->isValid()) {
            throw new AuthorizationException();
        }

        $accreditations = Accreditation::with(['institution','evaluationAssignments.assessors'])->orderBy('created_at', 'desc');
        if($user->isSuperAdmin()){
            $accreditations = $accreditations->get();
        } else if($user->isAdmin()){
            $accreditations = $accreditations->whereHas('institution', function($q) use ($user){
                $q->where('region_id', '=', $user["region_id"]);
            })->get();
        } else if($user->isProvince()){
            $accreditations = $accreditations->whereHas('institution', function($q) use ($user){
                $q->where('province_id', '=', $user["province_id"]);
            })->get();
        }else {
            $accreditations = $accreditations->where('user_id', $user->id)->get();
        }

        return new AccreditationCollection($accreditations);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(SubmitAccreditationRequest $request)
    {
        $user = $request->user();

        if (!$user->canUpdateCreateAccreditation()) {
            throw new AuthorizationException();
        }

        $contents = $request->all()['contents'];
        $type = $request->get('type');

        $accreditation = Accreditation::orderBy('created_at', 'desc')->firstOrNew([
            'institution_id' => $user->institution->id,
            'user_id' => $user->id,
            'status' => Accreditation::STATUS_INCOMPLETE,
        ]);
         
        if (!$accreditation->exists) {
            \DB::transaction(function () use ($accreditation, $type) {
                $accreditation->fill([
                    'type' => $type,
                    'code' => Accreditation::newCode(),
                ])->save();
            }, 3);
        }

        $savedContents = [];
        foreach ($contents as $content) {
            $data = [
                'accreditation_id' => $accreditation->id,
                'type' => $content['type'],
            ];
            if ($content['type'] == 'proof') {
                $component = InstrumentComponent::find($content['instrument_component_id'])->ancestor();
                $data['aspectable_type'] = InstrumentComponent::class;
                $data['aspectable_id'] = $component->id;
                $data['main_component_id'] = $component->id;
                $data['aspect'] = $component->name;
                $data['file'] = Storage::disk('local')->putFile(
                    "accreditations/{$user->id}",
                    $content['file_upload']
                );
            } elseif ($content['type'] == 'video') {
                if (empty($content['url'])) {
                    continue;
                }

                $component = InstrumentComponent::find($content['instrument_component_id'])->ancestor();
                $data['aspectable_type'] = InstrumentComponent::class;
                $data['aspectable_id'] = $component->id;
                $data['main_component_id'] = $component->id;
                $data['aspect'] = $component->name;
                $data['url'] = $content['url'];
            } elseif ($content['type'] == 'gdrive') {
                $component = InstrumentComponent::find($content['instrument_component_id'])->ancestor();
                $data['aspectable_type'] = InstrumentComponent::class;
                $data['aspectable_id'] = $component->id;
                $data['main_component_id'] = $component->id;
                $data['aspect'] = $component->name;
                $data['url'] = $content['url'];
            }  else {
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
            }

            $savedContent = AccreditationContent::firstOrNew([
                'accreditation_id' => $data['accreditation_id'],
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

        return new AccreditationResource($accreditation, 201);
    }

    public function finalize(Request $request, $id)
    {
        $accreditation = Accreditation::where('user_id', $request->user()->id)->findOrFail($id);

        $request->validate([
            'is_finalized' => 'required|boolean',
        ]);

        if ((bool) $request->get('is_finalized')) {
            $accreditation->setSubmitted()->save();
            $this->notifyToVerify($accreditation);
        }

        return new Resource();
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $id)
    {
        $user = $request->user();

        $with = ['contents.mainComponent', 'institution', 'evaluation'];
        if ($request->has('with')) {
            $with = array_merge($with, explode(',', $request->get('with')));
        }
        $accreditation = Accreditation::with($with);
        if ($user->isAssessee()) {
            $accreditation = $accreditation->where('user_id', $user->id)->findOrFail($id);
        } else {
            $accreditation = $accreditation->findOrFail($id);
        }
        $accreditation->loadResult();

        return new AccreditationResource($accreditation);
    }

    protected function notifyToVerify($accreditation)
    {
        $users = User::admins()
                     ->where('region_id', $accreditation->institution->region_id)
                     ->get();
        Notification::send($users, new VerifyNewAccreditation($accreditation));
    }

    public function verify(Request $request, $id)
    {
        $accreditation = Accreditation::submitted()->findOrFail($id);

        $request->validate([
            'is_approved' => 'required|boolean',
            'notes' => 'nullable|string|max:65535',
        ]);

        $accreditation->notes = $request->notes;

        if ($request->is_approved) {
            $accreditation->setVerified()->save();
            $accreditation->user->notify(
                new AccreditationVerified($accreditation)
            );

            return $this->storeAssignments($request, $id);
        } else {
            $accreditation->setIncomplete()->save();

            // Notify to make the user resubmit
            $accreditation->user->notify(
                new ResubmitIncompleteAccreditation($accreditation)
            );
        }

        return new AccreditationResource($accreditation);
    }

    public function browseInstruments(Request $request, $accreditationId)
    {
        $user = $request->user();

        $accreditation = Accreditation::with('institution');
        if ($user->isAssessee()) {
            $accreditation->where('user_id', $user->id);
        }
        $accreditation = $accreditation->findOrFail($accreditationId);

        $page = (int) ($request->page ?? 1);

        if (!$request->has('type')) {
            // Get all main component data without loading relations
            $choice = InstrumentComponent::where('category', $accreditation->institution->category)
                                         ->where('type', 'main')
                                         ->select('*')
                                         ->addSelect(\DB::raw("'choice' as action_type"))
                                         ->get()
                                         ->collect();
            $proof = InstrumentComponent::where('category', $accreditation->institution->category)
                                        ->where('type', 'main')
                                        ->select('*')
                                        ->addSelect(\DB::raw("'proof' as action_type"))
                                        ->get()
                                        ->collect();
            $video = InstrumentComponent::where('category', $accreditation->institution->category)
                                        ->where('type', 'main')
                                        ->select('*')
                                        ->addSelect(\DB::raw("'video' as action_type"))
                                        ->orderBy('id', 'asc')
                                        ->take(1)
                                        ->get()
                                        ->collect();
            // Merge both type
            $merged = $choice->merge($proof)->merge($video);
        } else {
            $merged = InstrumentComponent::where('category', $accreditation->institution->category)
                                        ->where('type', 'main')
                                        ->select('*')
                                        ->addSelect(\DB::raw("'{$request->type}' as action_type"));

            if ($page <= -1) {
                $merged = $merged->assesseeForms($request->get('type'), $accreditation->institution->category, $accreditation->id)
                                 ->get();
            } else {
                $merged = $merged->get()->collect();
            }
        }

        if ($page <= -1) {
            return new InstrumentComponentCollection($merged);
        }

        // Now we replace only the current page's data with component that 
        // includes all relevant relationships to avoid loading unneeded
        // other pages' data and also decrease our database's workload
        $currentComponent = InstrumentComponent::where('id', $merged[$page - 1]->id)
             ->where('category', $accreditation->institution->category)
             ->assesseeForms($merged[$page - 1]->action_type, $accreditation->institution->category, $accreditation->id)
             ->select('*')
             ->addSelect(\DB::raw("'{$merged[$page - 1]->action_type}' as action_type"))
             ->first();
        $currentComponent->accreditation = $accreditation;

        $merged->put($page - 1, $currentComponent);

        // Finally we simple paginate the data
        $perPage = 1;
        $paginator = $this->simplePaginate($merged, $perPage)->withQueryString();

        return new InstrumentComponentCollection($paginator);
    }

    public function getAssignments($accreditationId)
    {
        $assignments = EvaluationAssignment::with('assessors')
            ->where('accreditation_id', $accreditationId)
            ->get();

        return new EvaluationAssignmentCollection($assignments);
    }

    public function storeAssignments(Request $request, $accreditationId)
    {
        $request->validate([
            'assessors' => 'required|array',
            'assessors.*.user_id' => [
                'required',
                Rule::exists('users', 'id', function ($query) {
                    $role = Role::assessor()->first();
                    return $query->where('role_id', $role->id);
                }),
            ],
            'method' => ['required', Rule::in(EvaluationAssignment::methodList())],
            'scheduled_date' => 'required|date|date_format:Y-m-d',
        ]);
        $data = $request->all();
        $data['accreditation_id'] = $accreditationId;
        $assignment = EvaluationAssignment::create($data);
        $assignment->assessors()->sync(Arr::pluck($data['assessors'], 'user_id'));

        $this->notifyAssessorsToEvaluate($assignment);

        $assignment->load('assessors', 'accreditation');
        return new EvaluationAssignmentResource($assignment, 201);
    }

    public function browseContents(Request $request, $accreditationId)
    {
        $user = $request->user();
        if ($user->isAssessee()) {
            $accreditation = Accreditation::where('user_id', $user->id)->findOrFail($accreditationId);
        } else {
            $accreditation = Accreditation::findOrFail($accreditationId);
        }

        $contents = $accreditation->contents();
        if ($request->has('type')) {
            $contents->where('type', $request->type);
        }
        $contents = $request->has('per_page') && $request->per_page <= -1
            ? $contents->get()
            : $contents->paginate($request->per_page ?? 20)->withQueryString();

        return new AccreditationContentCollection($contents);
    }

    protected function notifyAssessorsToEvaluate($assignment)
    {
        $users = User::whereIn('id', $assignment->assessors->pluck('id')->toArray())
                     ->get();
        Notification::send($users, new EvaluateAccreditation($assignment));
    }

    public function processMeetingResult(Request $request, $accreditationId)
    {
        $accreditation = Accreditation::reviewed()->find($accreditationId);
        if (!$accreditation) {
            return new ErrorResource("Akreditasi belum ditinjau atau asesor belum upload berita acara", 404);
        }

        $request->validate([
            'predicate' => ['required', Rule::in(Accreditation::predicateList())],
            'meeting_date' => 'required|date|date_format:Y-m-d',
        ]);

        $accreditation->predicate = $request->get('predicate');
        $accreditation->meeting_date = $request->get('meeting_date');
        $accreditation->setEvaluated();
        $accreditation->setCertificateExpiration();
        $accreditation->save();

        Notification::send($accreditation->user, new AcceptAccreditationEvaluation($accreditation));

        return new AccreditationResource($accreditation);
    }

    public function accept(Request $request, $accreditationId)
    {
        $accreditation = Accreditation::acceptable()->findOrFail($accreditationId);

        $request->validate([
            'is_accepted' => 'required|boolean',
        ]);

        $users = User::admins()
                     ->where('region_id', $accreditation->institution->region_id)
                     ->get();

        if ($request->get('is_accepted') === true) {
            $accreditation->setAccredited();
            $accreditation->save();

            event(new AccreditationCompleted($accreditation));
            Notification::send($users, new AccreditationAccepted($accreditation));
        } else {
            $accreditation->setAppealed();
            $accreditation->save();
            Notification::send($users, new AccreditationAppealed($accreditation));

            $accreditation = $accreditation->newAppeal();
        }

        return new AccreditationResource($accreditation);
    }

    public function updateStatusViaNotification(Request $request, $id)
    {
        $update = Accreditation::where('id', $id)->update([
            'status' => $request->status
        ]);

        $accreditation = Accreditation::find($id);

        return new AccreditationResource($accreditation);
    }

    public function createActions(Request $request)
    {
        $user = $request->user();
        $availableTypes = [];

        if ($user->isAssessee()) {
            $accreditations = $user->accreditations()->orderBy('created_at', 'desc')->get();
            if ($accreditations->isEmpty()) {
                $availableTypes = [
                    Accreditation::TYPE_NEW,
                    Accreditation::TYPE_REACCREDITATION,
                ];
            } else {
                $accreditation = $accreditations->first();
                if ($accreditation->appealed_at) {
                    $availableTypes = [
                        Accreditation::TYPE_APPEAL,
                    ];
                } elseif ($accreditation->isAccredited()) {
                    if ($accreditation->isCertificateExpired()) {
                        $availableTypes = [
                            Accreditation::TYPE_NEW,
                        ];
                    } elseif ($accreditation->isReaccreditationEligible()) {
                        $availableTypes = [
                            Accreditation::TYPE_REACCREDITATION,
                        ];
                    }
                }
            }
        }

        return new Resource([
            'available_types' => $availableTypes,
        ]);
    }
}
