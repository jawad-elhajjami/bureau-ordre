<div class="container mx-auto px-4">
    <script src="{{ asset('Resources/dynamsoft.webtwain.initiate.js') }}"></script>
    <script src="{{ asset('Resources/dynamsoft.webtwain.config.js') }}"></script>
    <meta name="_token" content="{{ csrf_token() }}" />

    <!-- Header + Controls Container -->
    @livewire('partials.header-controls-container')

    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-4">

        <!-- Document Viewer Container -->
        <div class="sm:col-span-2 md:col-span-3 lg:col-span-3">
            <div id="dwtcontrolContainer" class="w-full h-screen" wire:ignore></div>
        </div>

        <!-- Details Form -->
        <div class="sm:col-span-1 md:col-span-2 lg:col-span-2 bg-white p-4 rounded-lg border border-gray-300">
            <x-mary-form>
                <h3 class="font-bold text-2xl mb-4">{{ __('messages.document_details_form_titlte') }}</h3>
                <x-mary-input wire:model="n_ordre" label="{!! __('messages.order_number_field_label') !!}"/>
                <x-mary-input wire:model="sujet" label="{{ __('messages.subject_field_label') }}"/>
                <x-mary-select
                    label="{{ __('messages.category_field_label') }}"
                    icon="o-folder"
                    :options="$categories"
                    option-value="id"
                    option-label="category_name"
                    placeholder="{{ __('messages.select_category_dropdown_text') }}"
                    wire:model="category"
                />                    
                <x-mary-select
                    label="{{ __('messages.service_field_label') }}"
                    icon="o-building-office"
                    :options="$services"
                    option-value="id"
                    option-label="name"
                    placeholder="{{ __('messages.select_service_dropdown_text') }}"
                    wire:model.live="service"
                />

                <x-mary-select
                    label="{{ __('messages.recipient_field_label') }}"
                    icon="o-user"
                    :options="$users"
                    option-value="id"
                    option-label="name"
                    placeholder="{{ __('messages.select_recipient_dropdown_text') }}"
                    wire:model="recipient"
                    :disabled="!$service"
                />

                <x-mary-textarea
                    label="{{ __('messages.description_field_label') }}"
                    wire:model="description"
                    placeholder="Your story ..."
                    hint="Max 1000 chars"
                    rows="5"
                />

                <x-mary-checkbox label="{{ __('messages.secure_by_otp_code') }}" wire:model='otpcode' />

                <x-mary-input wire:model="message" hidden/>
                <x-mary-input wire:model="messageType" hidden/>

                <x-mary-button label="{{ __('messages.create_btn_text') }}" icon="o-cloud-arrow-up" class="btn-warning btn-primary" id="uploadBtn" />
            </x-mary-form>
        </div>
    </div>
</div>

<script>
    // Initialization
    window.onload = function () {
        if (Dynamsoft) {
            Dynamsoft.DWT.AutoLoad = false;
            Dynamsoft.DWT.UseLocalService = true;
            Dynamsoft.DWT.Containers = [{
                ContainerId: 'dwtcontrolContainer',
                Width: '100%',
                Height: '100%' 
            }];
            Dynamsoft.DWT.RegisterEvent('OnWebTwainReady', Dynamsoft_OnReady);
            Dynamsoft.DWT.ResourcesPath = '{{ asset("Resources/") }}';

            Dynamsoft.DWT.Load();
        }
    };

    function upload() {
        var indices = [];
        for (var i = 0; i < DWObject.HowManyImagesInBuffer; i++) {
            indices.push(i);
        }

        // Set HTTP form fields
        DWObject.SetHTTPFormField('n_ordre', @this.get('n_ordre'));
        DWObject.SetHTTPFormField('sujet', @this.get('sujet'));
        DWObject.SetHTTPFormField('category', @this.get('category'));
        DWObject.SetHTTPFormField('service', @this.get('service'));
        DWObject.SetHTTPFormField('description', @this.get('description'));
        
        // Only set recipient if it has a value
        var recipient = @this.get('recipient');
        if (recipient) {
            DWObject.SetHTTPFormField('recipient', recipient);
        }

        DWObject.SetHTTPFormField('otpcode', @this.get('otpcode'));
        DWObject.SetHTTPHeader("X-CSRF-TOKEN", document.querySelector('meta[name="_token"]').content);

        var OnSuccess = function (httpResponse) {
            var response;
            try {
                response = JSON.parse(httpResponse);
            } catch (e) {
                response = { message: "Unexpected response format." };
            }

            if (response.message) {
                @this.set('message', response.message);
                @this.set('messageType', response.type);
            } else {
                @this.set('message', response.message);
                @this.set('messageType', response.type);
            }
            setTimeout(function() {
                location.reload();
            }, 1500);
        };

        var OnFailure = function (errorCode, errorString, httpResponse) {
            var response;
            try {
                response = JSON.parse(httpResponse);
            } catch (e) {
                response = { message: "Upload failed with unexpected response format." };
            }

            if (response.message) {
                @this.set('message', response.message);
                @this.set('messageType', response.type);

                // alert("Error: " + response.message);
            } else {
                @this.set('message', response.message);
                @this.set('messageType', response.type);
                // alert("Upload failed: " + errorString);
            }
            setTimeout(function() {
                location.reload();
            }, 1500);
        };

        DWObject.HTTPUpload(
            "/dwt_upload/upload",
            indices,
            Dynamsoft.DWT.EnumDWT_ImageType.IT_PDF,
            Dynamsoft.DWT.EnumDWT_UploadDataFormat.Binary,
            @this.get('n_ordre') + '.pdf',
            OnSuccess,
            OnFailure
        );
    }
</script>
<script src="{{ asset('js/scanner.js') }}"></script>
