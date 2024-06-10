
<div class="container">
    <div>
        <!-- Loading animation -->
        <div class="relative flex items-center justify-center">
            <x-mary-loading 
                class="text-primary loading-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-screen" 
                wire:loading 
                wire:target="userModal" 
            />
        </div>

        <x-mary-header title="Utilisateurs" subtitle="Gérez les utilisateurs">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="Rechercher..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button icon="o-plus" class="btn-primary" spinner @click="$wire.showModal()" />
            </x-slot:actions>
        </x-mary-header>
        <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
            <!-- Users table -->
 
            {{-- You can use any `$wire.METHOD` on `@row-click` --}}
            @if(count($users) > 0)
            <x-mary-table 
                :headers="$headers" 
                :rows="$users" 
                @row-click="$event.detail.id === {{ auth()->user()->id }} ? window.location.href = '{{ route('profile.show') }}' : $wire.edit($event.detail.id)" 
                striped 
                with-pagination>
                {{-- Special `actions` slot --}}

                    @scope('cell_name', $user)
                        <div class="flex gap-1">
                            @if(auth()->user()->id == $user->id)
                                <p>{{ $user->name }}</p><x-mary-badge value="Vous" class="badge-warning" />
                                @else
                                <p>{{ $user->name }}</p>
                            @endif
                        </div>
                    @endscope

                    @scope('cell_role.name', $user)
                        <x-mary-badge :value="$user->role->name" class="{{ $user->role->name === 'admin' ? 'bg-red-400 text-white' : 'badge-primary'  }}" />
                    @endscope
                    @scope('cell_service', $user)
                        {{ $user->service ? $user->service->name : "N/A" }}
                    @endscope

                    @scope('actions', $user) 
                    <div class="flex gap-1">
                        @if($user->id !== auth()->user()->id && $user->non_deletable == 0)
                            <x-mary-button icon="o-trash" wire:click="delete({{ $user->id }})" wire:confirm="Vous-êtes sûr de supprimer cet utilisateur ?" spinner class="btn-sm btn-ghost text-red-600"  />
                        @endif
                    </div>
                    @endscope
            </x-mary-table>
            @else
                @livewire('partials/no-users-to-show')
            @endif
            
        
        </div>
    </div>

    <!-- Create user modal -->

    <x-mary-modal wire:model="userModal" title="{{ $editMode ? 'Modifier cet utilisateur' : 'Ajouter un utilisateur' }}">
        <x-mary-form wire:submit="save">
            <div class="mb-4">
                <x-mary-input label="Nom complet" icon="o-user"  wire:model="form.fullName" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="E-mail" type="email" icon="o-inbox"  wire:model="form.email" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="Mot de passe" type="password" icon="o-lock-closed"  wire:model="form.password" inline />
            </div>
            <div class="mb-4">
                <x-mary-input label="Confirmer le mot de passe" icon="o-lock-closed" type="password"  wire:model="form.confirm_password" inline />
            </div>
            <div class="mb-4">
                
                @php
                    $roles = App\Models\Role::all();
                    $services = App\Models\Service::all();
                @endphp
                
                <x-mary-select
                    label="Choisir un role"
                    placeholder="Choisir un role"
                    :options="$roles"
                    wire:model="form.role_id"
                    inline
                />

            </div>
            <div class="mb-4">
                <x-mary-select
                    label="Service"
                    :options="$services"
                    placeholder="Choisir un service/département"
                    placeholder-value="0"
                    wire:model="form.service_id"
                    inline
                />
            </div>
        
            <x-slot:actions>
                <x-mary-button label="Annuler" @click="$wire.userModal = false" />
                <x-mary-button label="{{ $editMode ? 'Modifier' : 'Créer' }}" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <x-mary-toast />  

</div>