<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;

use App\Models\Document;
use App\Models\note;
use App\Policies\NotePolicy;
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

        Gate::define('delete-note', function ($user, note $note){
            if ($note->user_id === $user->id) {
                return true;
            }

            if ($user->role->name === 'admin') {
                return true;
            }
        });

        Gate::define('view-document', function ($user, Document $document) {
            // Check if the user is the owner of the document
            if ($document->user_id == $user->id) {
                return true;
            }
        
            // Admin can view all documents
            if ($user->role->name === 'admin') {
                return true;
            }
        
            // Check if the document has a specific recipient
            if ($document->recipient_id) {
                // If the user is the recipient, they can view the document
                if ($document->recipient_id == $user->id) {
                    return true;
                } else {
                    // If there is a specific recipient, other users in the same service cannot view the document
                    return false;
                }
            }
        
            // Check if the document was sent to a service the user belongs to
            if ($document->service_id == $user->service_id) {
                return true;
            }
        
            return false;
        });


        // check if user has authorization to delete a document
        Gate::define('delete-document', function ($user, Document $document) {
            // Check if the user is the owner of the document
            if ($document->user_id == $user->id) {
                return true;
            }

            if ($user->role->name === 'admin') {
                return true; // Admin can delete all documents
            }

            return false;
        });

        // check if user has authorization to delete a document
        Gate::define('update-document', function ($user, Document $document) {
            // Check if the user is the owner of the document
            if ($document->user_id == $user->id) {
                return true;
            }

            if ($user->role->name === 'admin') {
                return true; // Admin can delete all documents
            }

            return false;
        });


        // check if user can mark a document as read

        Gate::define('mark-as-read', function ($user, Document $document) {
            // Check if the user is not the owner of the document
            if ($document->user_id !== $user->id) {
                return true;
            }
            return false;
        });

    }
}
