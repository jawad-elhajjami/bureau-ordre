<div>

    <x-mary-header title="{{ __('messages.menu_all_documents_title') }}">
        <x-slot:middle class="!justify-end">
            <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="{{ __('messages.search_placeholder') }}" />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button icon="o-funnel" class="btn-primary" @click="$wire.filtersDrawer = true" />
        </x-slot:actions>
    </x-mary-header>  

    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
    @if(count($documents) > 0)
    <x-mary-table :headers="$headers" :rows="$documents" striped with-pagination :sort-by="$sortBy">
        
        @scope('cell_description', $document)
            <p class="truncate text-ellipsis w-96">{{ $document->description }}</p>
        @endscope
        
        @scope('cell_sent_by', $document)
            @if($document->owner)
                @if(auth()->user()->id == $document->owner->id)
                    <x-mary-badge value="Vous" class="badge-primary" />
                @else 
                    <x-mary-popover>
                        <x-slot:trigger>
                            <div class="w-9 h-9 text-sm font-bold text-white flex items-center justify-center rounded-full border border-gray-400" style="background-color: {{ $document->owner->color ?? 'rgb(168,85,247)' }};">{{ $document->owner->initials }}</div>
                        </x-slot:trigger>
                        <x-slot:content>
                            Nom: {{ $document->owner->name }} <br>
                            E-mail: {{ $document->owner->email }}
                        </x-slot:content>
                    </x-mary-popover>
                @endif
            @else
                <span class="text-gray-500">{{ __("Utilisateur supprimé") }}</span>
            @endif
        @endscope

        
        @scope('cell_department_sent_to', $document)
            @if($document->service != null)
                <x-mary-badge value="{{ $document->service->name }}" class="badge-warning truncate" />
            @else
                <p>Service supprimé</p>
            @endif
        @endscope
        
        @scope('cell_user_sent_to', $document)
            @if($document->recipient)
            @if(auth()->user()->id == $document->recipient->id)
                <x-mary-badge value="Vous" class="badge-primary" />
                @else 
                    <x-mary-popover>
                        <x-slot:trigger>
                            <div class="w-9 h-9 text-sm font-bold text-white flex items-center justify-center rounded-full border border-gray-400" style="background-color: {{ $document->recipient->color ?? 'rgb(168,85,247)' }};">{{ $document->recipient->initials }}</div>
                        </x-slot:trigger>
                        <x-slot:content>
                            Nom: {{ $document->recipient->name }} <br>
                            E-mail: {{ $document->recipient->email }}
                        </x-slot:content>
                    </x-mary-popover>
                @endif
            @else
                <span class="text-gray-500">{{ __("Tous les membres du service") }}</span>
            @endif
        @endscope
        
        
        @scope('actions', $document) 
            <div class="flex gap-1">
                @can('view-document', $document)
                    <x-mary-button icon="o-eye" link="{{ route('documents.view', $document->id) }}" no-wire-navigate class="btn-sm btn-ghost"  />
                @endcan
                @can('update-document', $document)
                    <x-mary-button icon="o-pencil" link="{{ route('documents.update', ['id' => $document->id ]) }}" external class="btn-sm btn-ghost "/>
                @endcan
                @can('delete-document', $document)
                    <x-mary-button icon="o-trash" spinner class="btn-sm btn-ghost" spinner wire:click="deleteDocument({{ $document->id }})" wire:confirm="Vous êtes sûr de supprimer cet document ?"/>
                @endcan
                
                @can('mark-as-read', $document)
                <!-- Mark document as read -->
                @php
                    $isRead = $document->readers->contains(auth()->user());
                @endphp
                <x-mary-button icon="o-check" spinner tooltip="{{ $isRead ? __('messages.mark_as_unread_btn') : __('messages.mark_as_read_btn') }}"  wire:click="toggleReadStatus({{ $document->id }})" class="btn-sm btn-ghost {{ $isRead ? 'text-green-500' : 'text-gray-800' }}" />
                @endcan

            </div>
        @endscope
    </x-mary-table>
    @else
        @livewire('partials/no-documents-to-show')
    @endif
    </div>


    <x-mary-drawer
        wire:model="filtersDrawer"
        title="{{ __('messages.filter_title') }}"
        separator
        with-close-button
        close-on-escape
        class="w-11/12 lg:w-1/3"
        right
>
    <div class="p-4">
        <div class="mb-4">
            <x-mary-select 
            label="Catégorie de document" 
            icon="o-folder" 
            :options="$categories" 
            option-value="id"
            option-label="category_name"
            placeholder="Selectionnez une catégorie"
            wire:model="filterCategory"
        />
        </div>
        <div class="mb-4">
            <x-mary-select 
            label="Service conçu" 
            icon="o-building-office" 
            :options="$services" 
            option-value="id"
            option-label="name"
            placeholder="Selectionnez un service"
            wire:model="filterService"
        />
        </div>
        <div class="mb-4">
            <x-mary-select 
            label="Destinataire" 
            icon="o-user" 
            :options="$users" 
            option-value="id"
            option-label="name"
            placeholder="Selectionnez un destinataire"
            wire:model="filterRecipient"
        />
        </div>

        <div class="mt-4">
            <x-mary-select 
                label="Période" 
                icon="o-clock" 
                :options="$timePeriodOptions" 
                option-value="value"
                option-label="label"
                placeholder="Sélectionnez une période"
                wire:model="timePeriodFilter"
            />
        </div>
        
    </div>
 
    <x-slot:actions>
        <x-mary-button label="Réinitialiser les filtres" @click="$wire.filtersDrawer = false" wire:click="resetFilters"/>
        <x-mary-button label="Appliquer les filtres" wire:click="$refresh" class="btn-primary" icon="o-check" />
    </x-slot:actions>
    </x-mary-drawer>
    

</div>
