<?php

namespace App\Providers;

use App\Models\Submissao\Evento;
use App\Models\Users\Administrador;
use App\Models\Submissao\Trabalho;
use App\Policies\AdministradorPolicy;
use App\Policies\EventoPolicy;
use App\Policies\TrabalhoPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
        Evento::class  =>  EventoPolicy::class,
        Administrador::class => AdministradorPolicy::class,
        Trabalho::class => TrabalhoPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
