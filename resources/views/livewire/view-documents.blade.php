<div>

    <x-mary-header title="{{ __('messages.documents_header_title') }}" subtitle="{{ __('messages.documents_header_subtitle') }}">
        <x-slot:middle class="!justify-end">
            <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="Search..." />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button icon="o-plus" class="btn-primary" link="{{ route('create-document') }}" wire:navigate />
        </x-slot:actions>
    </x-mary-header>
    <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
        <x-mary-tabs wire:model="selectedTab" class="mb-4">
            <x-mary-loading class="text-primary" wire:loading wire:target="search" class="m-8"/>
            <x-mary-tab name="incoming" icon="o-document-arrow-down">
                <x-slot:label>
                    Arrivée

                        <x-mary-badge :value="$incomingDocumentCount" class="badge-error incomingDocumentsCount"/>

                </x-slot:label>
                @livewire('incoming-documents', ['search' => $search])
            </x-mary-tab>
            <x-mary-tab name="outgoing" label="Départ" icon="o-document-arrow-up">
                @livewire('outgoing-documents', ['search' => $search])
            </x-mary-tab>
        </x-mary-tabs>
    </div>
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
