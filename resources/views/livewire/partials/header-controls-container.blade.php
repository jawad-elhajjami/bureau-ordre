<div class="header-controls-container">
    <x-mary-header title="{!! __('messages.menu_scan_documents_title') !!}" subtitle="{!! __('messages.scan_documents_via_app_directly_subtitle') !!}" separator />

    <div class="mb-4 flex flex-col sm:flex-row gap-4 items-center justify-between">
        <!-- Scanner Selection -->
        <div class="flex flex-col gap-2 sm:gap-4">
            <label for="source" class="font-bold text-sm">{!! __('messages.choose_scanner_select_text') !!}</label>
            <select id="source" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 block w-full sm:w-[340px] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500" wire:ignore></select>
        </div>

        <!-- Action Buttons -->
        <div class="flex gap-2 flex-wrap">
            <x-mary-button label="{{ __('messages.upload_document_button_text') }}" icon="o-arrow-up-tray" class="btn-primary btn-sm" id="loadImageBtn" />
            <x-mary-button label="{{ __('messages.start_scan_button_text') }}" icon="o-printer" class="btn-secondary btn-sm" id="scanBtn" />
        </div>
    </div>

    <div class="controls-container bg-white p-4 sm:p-8 rounded-lg border border-gray-300 mb-4 flex flex-col sm:flex-row items-center justify-between">
        <!-- Viewer and Editor Controls -->
        <div class="flex flex-wrap items-center gap-1">
            <x-mary-button icon="o-chevron-double-left" id="btnFirstImage" class="btn-sm btn-primary" />
            <x-mary-button icon="o-chevron-left" id="btnPreImage" class="btn-sm btn-primary" />
            <input type="text" size="2" id="DW_CurrentImage" readonly="readonly" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-[3rem] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
            /
            <input type="text" size="2" id="DW_TotalImage" readonly="readonly" value="0" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg focus:ring-blue-500 focus:border-blue-500 w-[3rem] p-2.5 dark:bg-gray-700 dark:border-gray-600 dark:placeholder-gray-400 dark:text-white dark:focus:ring-blue-500 dark:focus:border-blue-500"/>
            <x-mary-button icon="o-chevron-right" id="btnNextImage" class="btn-sm btn-primary" />
            <x-mary-button icon="o-chevron-double-right" id="btnLastImage" class="btn-sm btn-primary" />

            <label for="DW_PreviewMode" class="text-sm">Preview Mode:</label>
            <select size="1" id="DW_PreviewMode" onchange="setlPreviewMode();" class="bg-gray-50 border border-gray-300 text-gray-900 text-sm rounded-lg">
                <option value="0">-1X-1</option>
                <option value="1">1X1</option>
                <option value="2">2X2</option>
                <option value="3">3X3</option>
                <option value="4">4X4</option>
                <option value="5">5X5</option>
            </select>
        </div>

        <!-- Image Editing Buttons -->
        <div class="flex gap-2 mt-2 sm:mt-0 flex-wrap">
            <x-mary-button label="Crop" id="btnCrop" class="btn-sm" />
            <x-mary-button icon="o-arrow-path" id="btnRotateLeft" class="btn-sm" />
            <x-mary-button label="{{ __('messages.delete_selected_page_str') }}" icon="o-x-mark" id="btnRemoveSelectedImages" class="btn-sm" />
            <x-mary-button label="{{ __('messages.delete_all_pages_str') }}" icon="o-x-circle" id="btnRemoveAllImages" class="btn-sm" />
        </div>

        <!-- Zoom and Fit Buttons -->
        <div class="flex gap-2 mt-2 sm:mt-0 flex-wrap">
            <x-mary-button icon="o-magnifying-glass-plus" id="btnZoomIn" class="btn-sm" />
            <x-mary-button icon="o-magnifying-glass-minus" id="btnZoomOut" class="btn-sm" />
            <x-mary-button label="{{ __('messages.adjust_window_str') }}" id="btnFitWindow" class="btn-sm" />
        </div>
    </div>
</div>
