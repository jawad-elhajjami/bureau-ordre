<div>
    <x-mary-header title="Créer un document" subtitle="Spécifiez les informations nécessaires" separator />

    <div class="">
        <x-mary-form wire:submit.prevent="save" class="bg-white border border-gray-200 rounded-lg p-4 sm:p-8 lg:p-12">
            <div wire:loading wire:target="file">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-errors title="Oops!" description="Please, fix them." icon="o-face-frown" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-input label="Numéro d'ordre" class="col-span-1" wire:model.live="n_ordre"/>
                <x-mary-input label="Sujet" class="col-span-1" wire:model="sujet" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
                    wire:model.live="service"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-select
                    label="Destinataire (Optionnel)"
                    icon="o-user"
                    :options="$users"
                    option-value="id"
                    option-label="name"
                    placeholder="Selectionnez un destinataire (Optionnel)"
                    wire:model="recipient"
                    :disabled="!$service"
                />
                <x-mary-file wire:model.live="file" label="Document" accept="application/pdf" />
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-textarea
                    label="Description"
                    wire:model="description"
                    placeholder="Your story ..."
                    hint="Max 1000 chars"
                    rows="5"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-checkbox label="Sécurisez ce fichier par code OTP" wire:model='otpcode' />
            </div>

            <x-slot:actions>
                <x-mary-button label="Créer" class="btn-primary" type="submit" spinner="save" :disabled="$file ? null : 'disabled'" />
            </x-slot:actions>
        </x-mary-form>
    </div>
</div>
