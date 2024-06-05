<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Document;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        //
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
        

        // check if user is admin before allowing him to manage users
        Gate::define('can-manage-users', function ($user) {
            return $user->role->name === 'admin';
        });

        // check if user is admin before allowing him to manage services
        Gate::define('can-manage-services', function ($user) {
            return $user->role->name === 'admin';
        });

        // check if user has authorization to view a document
        Gate::define('view-document', function ($user, Document $document) {
            // Check if the user is the owner of the document
            if ($document->user_id == $user->id) {
                return true;
            }

            // Check if the document was sent to a service the user belongs to
            if ($document->service_id == $user->service_id) {
                return true;
            }

            return false;
        });


    }
}
