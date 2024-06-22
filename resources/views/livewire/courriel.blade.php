<div>
    <x-mary-header title="{{ __('messages.courriel_header_title') }}" separator>
        <x-slot:actions>
            @if(auth()->user()->role->name == 'admin' && auth()->user()->non_deletable === 1)
                <x-mary-button icon="o-pencil" label="{{ __('messages.modify_smtp_info_btn') }}" class="btn-primary" @click="$wire.updateSMTP_info_modal = true"/>
            @endif
        </x-slot:actions>
    </x-mary-header>

    <div class="main">
        {{-- <x-mary-signature wire:model="signature1" hint="Please, sign it." /> --}}
    </div>

    <!-- Update SMTP info modal -->
    <x-mary-modal wire:model="updateSMTP_info_modal" title="{{ __('messages.modify_smtp_info_btn') }}">
       
        <x-mary-form wire:submit.prevent="save">

                <x-mary-input label="{{ __('Mailer') }}" wire:model="state.mail_mailer" id="mail_mailer" inline/>

                <x-mary-input label="{{ __('Host') }}" wire:model="state.mail_host" id="mail_host" inline/>

                <x-mary-input label="{{ __('Port') }}" wire:model="state.mail_port" id="mail_port" type="number" inline/>

                <x-mary-input label="{{ __('Username') }}" wire:model="state.mail_username" id="mail_username" inline/>

                <x-mary-input label="{{ __('Password') }}" wire:model="state.mail_password" id="mail_password" type="password" inline/>

                <x-mary-input label="{{ __('Encryption') }}" wire:model="state.mail_encryption" id="mail_encryption" inline/>

                <x-mary-input label="{{ __('From Address') }}" wire:model="state.mail_from_address" id="mail_from_address" type="email" inline/>

                <x-mary-input label="{{ __('From Name') }}" wire:model="state.mail_from_name" id="mail_from_name" inline/>

            

            <x-slot:actions>
                <x-mary-button type="submit" label="{{ __('messages.edit_btn_text') }}" class="btn-primary" />
                <x-mary-button label="{{ __('messages.cancel_btn_text') }}" @click="$wire.updateSMTP_info_modal = false" />
            </x-slot:actions>
        </x-mary-form>
    </x-mary-modal>

    {{-- <script src="https://cdn.jsdelivr.net/npm/signature_pad@4.2.0/dist/signature_pad.umd.min.js"></script> --}}
</div>
