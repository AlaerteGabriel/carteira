<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Transacoes;
use App\Policies\UserPolicy;
use Illuminate\Auth\Access\Response;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class PermissoesServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {

        Gate::define('eDono', function(User $user, int $id) {
            if($user->us_id === $id){
                return Response::allow();
            }else{
                return Response::deny('Registro não pertencente ao usuário');
            }
        });

        Gate::define('hasPermissionResource', function (Transacoes $operador, $resource) {
            // Se o usuário for admin, permita o acesso imediatamente
            if ($operador->isRootOrAdmin()) {
                return true;
            }

            return false;
        });

        Gate::policy(User::class, UserPolicy::class);

    }

}
