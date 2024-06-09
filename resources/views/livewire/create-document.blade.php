<div>
    <x-mary-header title="Créer un document" subtitle="Spécifiez les informations nécessaires" separator />

    <div class="">
        <x-mary-form wire:submit="save" class="bg-white border border-gray-200 rounded-lg p-12">

            <div wire:loading wire:target="file">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-errors title="Oops!" description="Please, fix them." icon="o-face-frown" />


            <div class="grid grid-cols-2 gap-4">
                <x-mary-input label="Numéro d'ordre" class="grid-colspan-1" wire:model.live="n_ordre"/>
                <x-mary-input label="Sujet" class="grid-colspan-1" wire:model="sujet" />
            </div>


            <div class="grid grid-cols-2 gap-4">
                <x-mary-select
                    label="Catégorie de document"
                    icon="o-folder"
                    :options="$categories"
                    option-value="id"
                    option-label="category_name"
                    placeholder="Selectionnez une catégorie"
                    wire:model="category"
                />
                <x-mary-select
                    label="Service conçu"
                    icon="o-building-office"
                    :options="$services"
                    option-value="id"
                    option-label="name"
                    placeholder="Selectionnez un service"
                    wire:model="service"
                    hint="Tous les membres de ce services seront notifiés par ce document"
                />

            </div>
            <div class="grid grid-cols-2 gap-4">
                <x-mary-file wire:model.live="file" label="Document" hint="Only PDF" accept="application/pdf" />
                <x-mary-textarea
                        label="Description"
                        wire:model="description"
                        placeholder="Your story ..."
                        hint="Max 1000 chars"
                        rows="5"
                    />
            </div>
            <div class="grid grid-cols- gap-4">

            </div>

            <x-slot:actions>
                <x-mary-button label="Créer" class="btn-primary" type="submit" spinner="save" :disabled="$file ? null : 'disabled'" />
            </x-slot:actions>

        </x-mary-form>



    </div>



</div>
