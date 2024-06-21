<div>
    <x-mary-header title="{{ __('messages.upload_document_title_str') }}" subtitle="{{ __('messages.specify_necessary_info_str') }}" separator />

    <div class="">
        <x-mary-form wire:submit.prevent="save" class="bg-white border border-gray-200 rounded-lg p-4 sm:p-8 lg:p-12">
            <div wire:loading wire:target="file">
                <x-mary-loading class="text-primary loading-lg" />
            </div>

            <x-mary-errors title="Oops!" description="Please, fix them." icon="o-face-frown" />

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-input label="{{ __('messages.order_number_field_label') }}" class="col-span-1" wire:model.live="n_ordre"/>
                <x-mary-input label="{{ __('messages.subject_field_label') }}" class="col-span-1" wire:model="sujet" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-select
                    label="{{ __('messages.category_field_label') }}"
                    icon="o-folder"
                    :options="$categories"
                    option-value="id"
                    option-label="category_name"
                    placeholder="Selectionnez une catégorie"
                    wire:model="category"
                />
                <x-mary-select
                    label="{{ __('messages.service_field_label') }}"
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
                    label="{{ __('messages.recipient_field_label_optional') }}"
                    icon="o-user"
                    :options="$users"
                    option-value="id"
                    option-label="name"
                    placeholder="Selectionnez un destinataire (Optionnel)"
                    wire:model="recipient"
                    :disabled="!$service"
                />
                <div>
                    <x-mary-file wire:model.live="file" label="{{ __('messages.upload_document_title_str') }}" accept="application/pdf" class="mb-4" />
                    
                    <!-- Scan document instead -->
                    <x-mary-button label="{{ __('messages.menu_scan_documents_title') }}" icon-right="o-printer" link="{{ route('documents.scan') }}" class="btn-sm" no-wire-navigate />
                </div>


            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-textarea
                    label="{{ __('messages.description_field_label') }}"
                    wire:model="description"
                    placeholder="Your story ..."
                    hint="Max 1000 chars"
                    rows="5"
                />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <x-mary-checkbox label="{{ __('messages.secure_by_otp_code') }}" wire:model='otpcode' />
            </div>

            <x-slot:actions>
                <x-mary-button label="{{ __('messages.create_btn_text') }}" class="btn-primary" type="submit" spinner="save" :disabled="$file ? null : 'disabled'" />
            </x-slot:actions>
        </x-mary-form>
    </div>
</div>
