<?php

namespace App\Providers;

use App\Events\AccountActivated;
use App\Events\AccreditationEvaluated;
use App\Events\AccreditationCompleted;
use App\Events\PageUpdated;
use App\Listeners\SendAccountApprovalNotification;
use App\Listeners\SendAccountActivatedEmail;
use App\Listeners\SendAwaitingAccountApprovalEmail;
use App\Listeners\SetNewlyVerifiedAccountActive;
use App\Listeners\CreateInitialAssesseeInstitution;
use App\Listeners\ProcessAccreditationPostEvaluation;
use App\Listeners\SyncPublicMenuUrl;
use App\Listeners\MakeInstitutionAccredited;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        Verified::class => [
            SetNewlyVerifiedAccountActive::class,
        ],
        AccountActivated::class => [
            SendAccountActivatedEmail::class,
        ],
        AccreditationEvaluated::class => [
            ProcessAccreditationPostEvaluation::class,
        ],
        PageUpdated::class => [
            SyncPublicMenuUrl::class,
        ],
        AccreditationCompleted::class => [
            MakeInstitutionAccredited::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
