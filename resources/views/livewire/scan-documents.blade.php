<div class="container">
    <script src="{{ asset('Resources/dynamsoft.webtwain.initiate.js') }}"></script>
    <script src="{{ asset('Resources/dynamsoft.webtwain.config.js') }}"></script>
    <meta name="_token" content="{{ csrf_token() }}" />

    <x-mary-header title="Numériser les documents" subtitle="Scannez les documents directement vers l'application" separator />

    <div class="mb-4 flex gap-4 items-center justify-between">
        <div class="flex flex-col gap-4">
            <label for="source" class="font-bold text-sm">Choisissez un scanner</label>
            <select id="source" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-[340px] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:ignore></select>
        </div>
        <div>
            <x-mary-button label="Choisir un document" icon="o-arrow-up-tray" class="btn-primary btn-sm" id="loadImageBtn" />
            <x-mary-button label="Lancer le scan" icon="o-printer" class="btn-secondary btn-sm" id="scanBtn" />
        </div>
    </div>
    <div class="controls-container bg-white p-8 rounded-lg border border-gray-300 mb-4 flex items-center justify-between">
        <!-- Viewer and Editor Controls -->
        <div>
            <x-mary-button icon="o-chevron-double-left" id="btnFirstImage" class="btn-sm btn-primary" />
            <x-mary-button icon="o-chevron-left" id="btnPreImage" class="btn-sm btn-primary" />
            <input type="text" size="2" id="DW_CurrentImage" readonly="readonly" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/> /
            <input type="text" size="2" id="DW_TotalImage" readonly="readonly" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-fit p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
            <x-mary-button icon="o-chevron-right" id="btnNextImage" class="btn-sm btn-primary" />
            <x-mary-button icon="o-chevron-double-right" id="btnLastImage" class="btn-sm btn-primary" />
            Preview Mode:
            <select size="1" id="DW_PreviewMode" onchange="setlPreviewMode();" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg">
                <option value="0">-1X-1</option>
                <option value="1">1X1</option>
                <option value="2">2X2</option>
                <option value="3">3X3</option>
                <option value="4">4X4</option>
                <option value="5">5X5</option>
            </select>
        </div>
        <div>
            <x-mary-button label="Crop" id="btnCrop" class="btn-sm" />
            <x-mary-button icon="o-arrow-path" id="btnRotateLeft" class="btn-sm" />
            <x-mary-button label="Supprimer la page selectionné" icon="o-x-mark" id="btnRemoveSelectedImages" class="btn-sm" />
            <x-mary-button label="Supprimer tous les pages" icon="o-x-circle" id="btnRemoveAllImages" class="btn-sm" />
        </div>
        <div>
            <x-mary-button icon="o-magnifying-glass-plus" id="btnZoomIn" class="btn-sm" />
            <x-mary-button icon="o-magnifying-glass-minus" id="btnZoomOut" class="btn-sm" />
            <x-mary-button label="Ajuster la fenêtre" id="btnFitWindow" class="btn-sm" />
        </div>
    </div>
    
    <div class="grid grid-cols-5">
        <div class="col-span-3">
            <div id="dwtcontrolContainer" style="h-fit flex items-center justify-center m-auto w-fit" wire:ignore></div>
        </div>
        <div class="form-container col-span-2 bg-white p-4 rounded-lg border border-gray-300">
            <x-mary-form>
                <h3 class="font-bold text-2xl mb-4">Détails du document</h3>
                <x-mary-input wire:model="n_ordre" label="Numéro d'ordre"/>
                <x-mary-input wire:model="sujet" label="Sujet"/>
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

                <x-mary-textarea
                    label="Description"
                    wire:model="description"
                    placeholder="Your story ..."
                    hint="Max 1000 chars"
                    rows="5"
                />

                <x-mary-checkbox label="Sécurisez ce fichier par code OTP" wire:model='otpcode' />

                <x-mary-input  wire:model="message" hidden/>
                <x-mary-input  wire:model="messageType" hidden/>

                <x-mary-button label="Importez le document" icon="o-cloud-arrow-up" class="btn-warning btn-primary" id="uploadBtn" />
            </x-mary-form>
        </div>
    </div>
    <script>
        // initializiation
        window.onload = function () {
            if (Dynamsoft) {
                Dynamsoft.DWT.AutoLoad = false;
                Dynamsoft.DWT.UseLocalService = true;
                Dynamsoft.DWT.Containers = [{
                    ContainerId: 'dwtcontrolContainer',
                    Width: '600px',
                    Height: '750px'
                }];
                Dynamsoft.DWT.RegisterEvent('OnWebTwainReady', Dynamsoft_OnReady);
                Dynamsoft.DWT.ProductKey =
                    't0114QAEAACyPbb490giZ+NGuSVEWGBZdTIpTaJ0SmP78yHHGI1p5M12K7lzLU7DmG4i9ni2Cs2gpoasPyRIQEbyZN0/1HPIv45tpzPhaYPOEQUQucmDilNlbTb2R855bYTBlaox3fJiend3MIAjK7QK8CkFQ';
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
</div>
