<div>
    @if(count($documents) > 0)
    <x-mary-table :headers="$headers" :rows="$documents" striped with-pagination :sort-by="$sortBy">
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
            </div>
        @endscope
    </x-mary-table>
    @else
        @livewire('partials/no-documents-to-show')
    @endif
</div>
