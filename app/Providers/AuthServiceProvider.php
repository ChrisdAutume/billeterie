<?php

namespace App\Providers;

use App\Models\Guichet;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        Auth::viaRequest('api-token', function ($request) {
            $inputKey = 'api_key';
            $token = $request->query($inputKey);

            if (empty($token)) {
                $token = $request->input($inputKey);
            }

            if (empty($token)) {
                $token = $request->bearerToken();
            }

            return Guichet::where('uuid', $token)
                ->where('start_at','<=',Carbon::now('Europe/Paris'))
                ->where('end_at','>=',Carbon::now('Europe/Paris'))->first();
        });
        //
    }
}
