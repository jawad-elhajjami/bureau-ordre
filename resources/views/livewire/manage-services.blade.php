<div class="container">
    <x-mary-header title="Services" subtitle="Gérez les services">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="Rechercher..." />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button icon="o-plus" class="btn-primary" @click="$wire.showModal()" spinner />
            </x-slot:actions>
    </x-mary-header>

    

    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
        <!-- Services table -->

        <x-mary-table :headers="$headers" :rows="$services" with-pagination
            @row-click="$wire.edit($event.detail.id)" 
        >   
            @scope('cell_members', $service)
            
            <div class="flex items-center gap-1"> 
                @php
                    $members = $service->members;
                @endphp
                @if(count($members) !== 0)
                   
                    @foreach ($members as $member)

                    <x-mary-popover>
                        <x-slot:trigger>
                            <div class="w-9 h-9 bg-purple-400 text-sm font-bold text-white flex items-center justify-center rounded-full border border-gray-400">{{ $member->initials }}</div>
                        </x-slot:trigger>
                        <x-slot:content>
                            Nom: {{ $member->name }} <br>
                            E-mail: {{ $member->email }}
                        </x-slot:content>
                    </x-mary-popover>
                    
                    @endforeach
                
                @else
                <x-mary-badge value="Pas de members" class="badge-warning" />
                @endif


            </div>
            
            @endscope
            @scope('actions', $service) 
                <div class="flex gap-1">
                    <x-mary-button icon="o-trash" wire:click="delete({{ $service->id }})" spinner class="btn-sm btn-ghost text-red-600"  />
                </div>
            @endscope
        </x-mary-table>
    
    </div>

    <x-mary-modal wire:model="serviceModal" >
        
        <x-mary-form wire:submit="save">
            <h3 class="text-2xl text-gray-900 dark:text-neutral-200">{{ $editMode ? 'Modifier le service' : 'Ajouter un service' }}</h3>
            <x-mary-input label="Titre de service" wire:model="form.name"/>
            <h4 class="mt-4 mb-2 text-lg">Sélectionnez les membres</h4>
                @if(count($availableUsers) !== 0)
                
                    @foreach($availableUsers as $user)
                    <div class="grid grid-cols-2">
                        
                        @if ($user->service_id && $user->service_id !== $clickedServiceId)
                            <x-mary-checkbox
                                label="{{ $user->name }}"
                                wire:model="users_multi_ids"
                                value="{{ $user->id }}"
                                class="checkbox-primary"
                                disabled
                            />
                            <x-mary-badge value="{{ $user->service->name }}" class="badge-warning ml-2" />
                        @else
                            <x-mary-checkbox
                                label="{{ $user->name }}"
                                wire:model="users_multi_ids"
                                value="{{ $user->id }}"
                                class="checkbox-primary"
                            />
                        @endif
                    </div>
                    @endforeach
                @else
                <p>No members available ! </p>
                @endif
            <x-slot:actions>
                <x-mary-button label="Annuler" @click="$wire.serviceModal = false" />
                <x-mary-button label="{{ $editMode ? 'Modifier' : 'Créer' }}" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

</div>
