<div>
    <x-mary-header title="Documents" subtitle="Gérez vos documents">
        <x-slot:middle class="!justify-end">
            <x-mary-input icon="o-bolt" placeholder="Search..." />
        </x-slot:middle>
        <x-slot:actions>
            <x-mary-button icon="o-funnel" class="btn-primary" @click="$wire.filtersDrawer = true"/>
        </x-slot:actions>
    </x-mary-header>


    <div class="grid grid-cols-4 gap-4">
        @foreach ($documents as $document)
            <x-mary-card title="{{ $document->order_number }}">
                {{ $document->subject }}
            
                <x-slot:figure>
                    <img src="https://placehold.co/600x400" />
                </x-slot:figure>
                <x-slot:menu>
                    <x-mary-button icon="o-share" class="btn-circle btn-sm" />
                    <x-mary-icon name="o-heart" class="cursor-pointer" />
                </x-slot:menu>
                <x-slot:actions>
                    <x-mary-button label="Download" link="{{ route('files.show', ['path' => $document->file_path]) }}" external  class="btn-primary"/>
                </x-slot:actions>
            </x-mary-card>
        @endforeach
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
    </x-mary-drawer>
    
</div>
