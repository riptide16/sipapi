<?php

namespace App\Http\Controllers\Admin;

use App\Http\Resources\UserResource;
use App\Http\Resources\InstitutionResource;
use App\Http\Resources\InstrumentResource;
use App\Http\Resources\InstrumentComponentCollection;
use App\Http\Resources\PermissionCollection;
use App\Http\Resources\MenuCollection;
use App\Http\Resources\ErrorResource;
use App\Http\Resources\Resource;
use App\Http\Resources\ResourceCollection;
use App\Http\Resources\AccreditationResource;
use App\Http\Resources\NotificationCollection;
use App\Http\Resources\NotificationResource;
use App\Http\Resources\EvaluationAssignmentCollection;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SubmitInstitutionRequest;
use App\Models\Accreditation;
use App\Models\Institution;
use App\Models\InstitutionRequest;
use App\Models\Instrument;
use App\Models\InstrumentAspect;
use App\Models\InstrumentComponent;
use App\Models\Menu;
use App\Models\Notification;
use App\Models\User;
use App\Models\EvaluationAssignment;
use App\Models\Page;
use App\Notifications\ValidateAssesseeInstitutionSubmission;
use App\Rules\Alphaspace;
use App\Support\CanPaginate;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification as NotificationDispatcher;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Storage;

class SelfController extends Controller
{
    use CanPaginate;

    public function showUser(Request $request)
    {
        return new UserResource($request->user()->load('role', 'region', 'province'));
    }

    public function updateProfile(Request $request)
    {
        $request->validate([
            'name' => ['sometimes', new Alphaspace(), 'max:191'],
            'email' => [
                'sometimes',
                'email',
                'max:191',
                Rule::unique('users')->ignore($request->user()),
            ],
            'phone_number' => 'nullable|numeric|digits_between:5,20',
            'photo_upload' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'institution_name' => 'nullable|max:191',
            'province_id' => 'sometimes|exists:provinces,id',
            'region_id' => 'sometimes|exists:regions,id',
            'password' => 'sometimes|min:8|confirmed',
        ]);

        $user = $request->user();
        $data = $request->all();

        if ($request->hasFile('photo_upload')) {
            $data['profile_picture'] = $request->file('photo_upload')
                                               ->storePublicly(
                                                  "users/{$user->id}",
                                                  'public'
                                               );

            // Delete old picture
            if ($user->profile_picture) {
                Storage::disk('public')->delete($user->profile_picture);
            }
        }

        $user->name = $data['name'] ?? $user->name;
        $user->phone_number = $data['phone_number'] ?? $user->phone_number;
        $user->profile_picture = $data['profile_picture'] ?? $user->profile_picture;
        $user->institution_name = $data['institution_name'] ?? $user->institution_name;
        $user->province_id = $data['province_id'] ?? $user->province_id;
        $user->region_id = $data['region_id'] ?? $user->region_id;
        $user->email = $data['email'] ?? $user->email;
        if (!empty($data['password'])) {
            $user->password = $data['password'];
        }
        $user->save();

        $user->load('role', 'region', 'province');

        return new UserResource($user);
    }

    public function indexPermissions(Request $request)
    {
        return new PermissionCollection($request->user()->role->permissions);
    }

    public function indexMenus(Request $request)
    {
        $user = $request->user();
	
        $menus = Menu::whereHas('permissions', function ($query) use ($user) {
            $query->whereIn('id', $user->role->permissions->pluck('id')->toArray());
        })->sort()->get();

        return new MenuCollection($menus);
    }

    public function showInstitution(Request $request)
    {
        $user = $request->user();
        $user->canOrFail('edit_institutions');

        if ($user->isActive() && is_null($user->institution)) {
            $institutionReq = $user->institutionRequests()->orderBy('id', 'desc')->first();
            $institution = $institutionReq ?: null;
        } else {
            $institution = $user->institution;
        }
        if ($institution) {
            return new InstitutionResource($institution->load('region', 'province', 'city', 'subdistrict', 'village', 'user'));
        } else {
            return (new ModelNotFoundException())->setModel(Institution::class);
        }
    }

    public function submitInstitution(SubmitInstitutionRequest $request)
    {
        $user = $request->user();
        $user->canOrFail('edit_institutions');
        if ($user->institutionRequests()->unvalidated()->exists()) {
            return new ErrorResource(__('errors.institution_request_exists'), 400);
        }

        $data = $request->all();

        if ($request->hasFile('registration_form')) {
            $formPath = $request->file('registration_form')
                                ->store('institutions/forms');
            $data = array_merge($data, ['registration_form_file' => $formPath]);
        }
        $data['user_id'] = $user->id;
        $data['type'] = $user->institution ? InstitutionRequest::TYPE_UPDATE : InstitutionRequest::TYPE_CREATE;
        $institution = InstitutionRequest::create($data);

        $users = User::admins()->where('region_id', $institution->region_id)->get();
        NotificationDispatcher::send($users, new ValidateAssesseeInstitutionSubmission($institution));

        return new InstitutionResource($institution);
    }

    public function showInstrument(Request $request)
    {
        $user = $request->user();
        if (!optional($user->institution)->isValid()) {
            return new ErrorResource(__('errors.invalid_user'), 403);
        }

        if ($request->get('simulation')) {
            $accreditation = $user->accreditationSimulations()->incomplete()->orderBy('created_at', 'desc')->first();
        } else {
            $accreditation = $user->accreditations()->incomplete()->orderBy('created_at', 'desc')->first();
        }

        if (!$request->has('type')) {
            // Get all main component data without loading relations
            $choice = InstrumentComponent::where('category', $user->institution->category)
                                         ->where('type', 'main')
                                         ->select('*')
                                         ->addSelect(\DB::raw("'choice' as action_type"))
                                         ->get()
                                         ->collect();
            $proof = InstrumentComponent::where('category', $user->institution->category)
                                        ->where('type', 'main')
                                        ->select('*')
                                        ->addSelect(\DB::raw("'proof' as action_type"))
                                        ->get()
                                        ->collect();
            $video = InstrumentComponent::where('category', $user->institution->category)
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
            $merged = InstrumentComponent::where('category', $user->institution->category)
                                        ->where('type', 'main')
                                        ->select('*')
                                        ->addSelect(\DB::raw("'{$request->type}' as action_type"))
                                        ->get()
                                        ->collect();
        }

        if ($merged->isEmpty()) {
            return new ResourceCollection();
        }

        // Now we replace only the current page's data with component that 
        // includes all relevant relationships to avoid loading unneeded
        // other pages' data and also decrease our database's workload
        $page = (int) ($request->page ?? 1);

        if ($request->get('simulation')) {
            $currentComponent = InstrumentComponent::where('id', $merged[$page - 1]->id)
                 ->where('category', $user->institution->category)
                 ->assesseeFormSimulations($merged[$page - 1]->action_type, $user->institution->category, optional($accreditation)->id)
                 ->select('*')
                 ->addSelect(\DB::raw("'{$merged[$page - 1]->action_type}' as action_type"))
                 ->first();
        } else {
            $currentComponent = InstrumentComponent::where('id', $merged[$page - 1]->id)
                 ->where('category', $user->institution->category)
                 ->assesseeForms($merged[$page - 1]->action_type, $user->institution->category, optional($accreditation)->id)
                 ->select('*')
                 ->addSelect(\DB::raw("'{$merged[$page - 1]->action_type}' as action_type"))
                 ->first();
        }
        $merged->put($page - 1, $currentComponent);

        // Finally we simple paginate the data
        $perPage = 1;
        $paginator = $this->simplePaginate($merged, $perPage);

        return new InstrumentComponentCollection($paginator);
    }

    public function indexNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::byUserId($user->id)->sort()->take(3);

        if (!$request->has('show_all') || !$request->show_all) {
            $notifications->unread();
        }

        $notifications = $notifications->get();

        return new NotificationCollection($notifications);
    }

    public function allNotifications(Request $request)
    {
        $user = $request->user();
        $notifications = Notification::byUserId($user->id)->sort();

        if (!$request->has('show_all') || !$request->show_all) {
            $notifications->unread();
        }

        $notifications = $notifications->get();

        return new NotificationCollection($notifications);
    }

    public function readNotification(Request $request, $id)
    {
        $user = $request->user();
        $notification = Notification::byUserId($user->id)->findOrFail($id);

        if (!$notification->read_at) {
            $notification->read_at = now();
            $notification->save();
        }

        return new NotificationResource($notification);
    }

    public function indexEvaluationAssignments(Request $request)
    {
        $user = $request->user();
        $assignments = EvaluationAssignment::whereHas('assessors', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })->with(['accreditation'])->get();

        return new EvaluationAssignmentCollection($assignments);
    }

    public function pageSlugAvailability($slug)
    {
        $data = [
            'is_available' => true,
            'alternative' => $slug,
        ];

        $page = Page::where('slug', $slug)->first();
        if ($page) {
            $data['is_available'] = false;
            $data['alternative'] = $page->alternativeSlug();
        }

        return new Resource($data);
    }

    public function incompleteAccreditation(Request $request)
    {
        $user = $request->user();
        $accreditation = $user->accreditations()->incomplete()->orderBy('id', 'desc')->first();

        return new AccreditationResource($accreditation);
    }
}
