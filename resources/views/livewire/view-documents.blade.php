<div>
    <x-mary-header title="Documents" subtitle="Gérez vos documents">
        <x-slot:middle class="!justify-end">
            <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="Search..." />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button icon="o-funnel" class="btn-primary" @click="$wire.filtersDrawer = true" />
        </x-slot:actions>
    </x-mary-header>

    </x-mary-header>
    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
        <x-mary-table :headers="$headers" :rows="$documents" striped @row-click="alert($event.detail.subject)" :sort-by="$sortBy">
            @scope('cell_sent_by', $document)

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

            @endscope
            @scope('actions', $document)
            <div class="flex gap-1">
                <x-mary-button icon="o-eye" link="{{ route('documents.view', $document->id) }}" no-wire-navigate class="btn-sm btn-ghost "  />
                <x-mary-button icon="o-arrow-down-tray" link="{{ route('files.show', ['path' => $document->file_path]) }}" external class="btn-sm btn-ghost "/>
                <x-mary-button icon="o-trash" spinner class="btn-sm btn-ghost "/>
            </div>
            @endscope
        </x-mary-table>
        <x-mary-tabs wire:model="selectedTab" class="mb-4">
            <x-mary-tab name="incoming" icon="o-document-arrow-down">
                <x-slot:label>
                    Arrivée
                    @php
                    // Query to get the count of incoming documents
                    $incomingDocumentCount = App\Models\Document::where('service_id', auth()->user()->service_id)->count();
                    @endphp
                    @if($incomingDocumentCount > 0)
                        <x-mary-badge :value="$incomingDocumentCount" class="badge-primary incomingDocumentsCount"/>
                    @endif
                </x-slot:label>
                @livewire('incoming-documents', ['search' => $search])
            </x-mary-tab>
            <x-mary-tab name="outgoing" label="Départ" icon="o-document-arrow-up">
                @livewire('outgoing-documents', ['search' => $search])
            </x-mary-tab>
        </x-mary-tabs>
    </div>
    <x-mary-drawer
        wire:model="filtersDrawer"
        title="Hello"
        subtitle="Livewire"
        separator
        with-close-button
        close-on-escape
        class="w-11/12 lg:w-1/3"
        right
    >
    <div>Hey!</div>

    <x-slot:actions>
        <x-mary-button label="Cancel" @click="$wire.filtersDrawer = false" />
        <x-mary-button label="Confirm" class="btn-primary" icon="o-check" />
    </x-slot:actions>
        <div>Hey!</div>

        <x-slot:actions>
            <x-mary-button label="Cancel" @click="$wire.filtersDrawer = false" />
            <x-mary-button label="Confirm" class="btn-primary" icon="o-check" />
        </x-slot:actions>
    </x-mary-drawer>
</div>
@script
<script>
    $wire.on('incomingDocumentDeleted', () => {
        console.log('deleted');
        // Increment the count of incoming documents
        let incomingCountElement = document.querySelector('.incomingDocumentsCount');
        let incomingCountValue = parseInt(incomingCountElement.textContent);
        incomingCountElement.textContent = incomingCountValue - 1;
    });

    $wire.on('count-changed', (event) => {
        console.log(event.count);
        document.querySelector('.incomingDocumentsCount').textContent = event.count
    })
</script>
@endscript
