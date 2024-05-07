<?php

namespace App\Http\Controllers\Admin;

use App\Models\Accreditation;
use App\Http\Controllers\Controller;
use App\Http\Resources\AccreditationCollection;
use App\Http\Resources\AccreditationResource;
use App\Notifications\CertificationSent;
use App\Notifications\CertificationSigned;
use App\Notifications\CertificationPrinted;
use Illuminate\Http\Request;

class CertificationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $user = $request->user();
        if ($user->isAssessee()){
            $accreditations = Accreditation::with(['institution', 'evaluation'])->where('user_id', $user->id)->accredited()->get();
        }
        else if($user->isSuperAdmin()){
            $accreditations = Accreditation::with(['institution', 'evaluation'])->accredited()->get();
        }
        else if($user->isProvince()){
            $accreditations = Accreditation::with(['institution', 'evaluation'])->whereHas('institution', function($q) use ($user){
                $q->where('province_id', '=', $user["province_id"]);
            })->accredited()->get();        
        }
        else {
            $accreditations = Accreditation::with(['institution', 'evaluation'])->whereHas('institution', function($q) use ($user){
                $q->where('region_id', '=', $user["region_id"]);
            })->accredited()->get();
        }

        return new AccreditationCollection($accreditations);
    }

    public function show($accreditationId)
    {
        $accreditation = Accreditation::with(['institution', 'evaluation'])->accredited()->findOrFail($accreditationId);

        return new AccreditationResource($accreditation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $accreditationId)
    {
        $accreditation = Accreditation::accredited()->findOrFail($accreditationId);
        $request->validate([
            'certificate_status' => 'required|in:'.implode(',', Accreditation::certificateStatusList()),
            'certificate_sent_at' => 'required_if:certificate_status,dikirim|date|date_format:Y-m-d',
        ]);
        $accreditation->certificate_status = $request->get('certificate_status');
        $accreditation->certificate_sent_at = $request->get('certificate_sent_at');
        $accreditation->save();

        if ($accreditation->certificate_status == Accreditation::CERT_STATUS_SENT && $request->has('certificate_sent_at')) {
            $accreditation->user->notify(new CertificationSent($accreditation));
        } elseif ($accreditation->certificate_status == Accreditation::CERT_STATUS_SIGNED) {
            $accreditation->user->notify(new CertificationSigned($accreditation));
        } elseif ($accreditation->certificate_status == Accreditation::CERT_STATUS_PRINTED) {
            $accreditation->user->notify(new CertificationPrinted($accreditation));
        }

        return new AccreditationResource($accreditation);
    }
}
