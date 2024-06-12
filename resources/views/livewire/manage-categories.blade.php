<div>
    <div>
        <div class="relative flex items-center justify-center">
            <x-mary-loading
                class="text-primary loading-lg absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 h-screen"
                wire:loading
                wire:target="categoryModal"
            />
        </div>

        <x-mary-header title="{{ __('messages.categories_header_title') }}" subtitle="{{ __('messages.categories_header_subtitle') }}">
            <x-slot:middle class="!justify-end">
                <x-mary-input icon="o-bolt" wire:model.live="search" placeholder="{{ __('messages.search_placeholder') }}" />
            </x-slot:middle>
            <x-slot:actions>
                <x-mary-button icon="o-plus" class="btn-primary" spinner @click="$wire.showModal()" />
            </x-slot:actions>
        </x-mary-header>

        <div class="bg-white dark:bg-gray-800 overflow-hidden sm:rounded-lg p-4">
            @if(count($categories) > 0)

                <x-mary-table :headers="$headers" :rows="$categories" striped with-pagination>
                    @scope('cell_category_name', $category)
                        <p>{{ $category->category_name }}</p>
                    @endscope
                    @scope('actions', $category)
                        <div class="flex gap-1">
                            <x-mary-button icon="o-pencil" wire:click="edit({{ $category->id }})" spinner class="btn-sm btn-ghost" />
                            <x-mary-button icon="o-trash" wire:click="delete({{ $category->id }})" spinner class="btn-sm btn-ghost text-red-600" />
                        </div>
                    @endscope
                </x-mary-table>
            @else
                @livewire('partials/no-categories-to-show');
            @endif
        </div>
    </div>

    <x-mary-modal wire:model="categoryModal" title="{{ $editMode ? __('messages.edit_category_modal_title') : __('messages.add_category_modal_title') }}">
        <x-mary-form wire:submit="save">
            <div class="mb-4">
                <x-mary-input label="{{ __('messages.category_name_input_label') }}" icon="o-document" wire:model="category_name" inline />
            </div>

            <x-slot:actions>
                <x-mary-button label="Annuler" @click="$wire.categoryModal = false" />
                <x-mary-button label="{{ $editMode ? 'Modifier' : 'Créer' }}" class="btn-primary" type="submit" spinner="save" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    <x-mary-toast />
</div>
