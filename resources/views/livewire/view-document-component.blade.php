<div class="container">
    <div class="flex items-center w-full justify-between mb-4 p-4">
        <h1 class="text-2xl font-bold">{{ $document->subject }}</h1>
        <div class="controls flex gap-4 items-center">
            <x-mary-button id="prev-page" icon="o-arrow-left"  class="btn-sm btn-primary"/>
            <input id="page-input" type="number" min="1" value="1" class="border border-gray-200 rounded-lg px-4 py-2">
            <span>/ <span id="page-count"></span></span>
            <x-mary-button id="next-page" icon="o-arrow-right" class="btn-sm btn-primary"/>
        </div>

        <div class="controls"> 
            <x-mary-button id="zoom-in" icon="o-magnifying-glass-plus" class="btn-secondary btn-sm"/>
            <x-mary-button id="zoom-out" icon="o-magnifying-glass-minus" class="btn-secondary btn-sm"/>
            <x-mary-button label="Imprimer" icon="o-printer" class="btn-sm btn-primary" onclick="printPdf()" />
        </div>

    </div>
    <div class="grid grid-cols-5 gap-8">
        
        <div class="col-span-2 bg-white p-8 rounded-lg border border-gray-200 h-fit sticky top-20">
            <x-mary-tabs wire:model="selectedTab">
                <x-mary-tab name="details" label="Détails">   
                    @if ($document)
                        @foreach ($columns as $column => $label)
                            <x-mary-list-item :item="$document" separator hover>
                                <x-slot:value>
                                    {{ $label }}
                                </x-slot:value>
                                <x-slot:sub-value>
                                    @if ($column == 'service_id' && $document->service)
                                        <x-mary-badge value="{{ $document->service->name }}" class="badge-warning" />
                                    @elseif ($column == 'category_id' && $document->category)
                                        <x-mary-badge value="{{ $document->category->category_name }}" class="badge-error" />
                                    @elseif ($column == 'recipient_id' && $document->recipient)
                                        <x-mary-badge value="{{ $document->recipient->name }}" class="badge-primary" />
                                    @else
                                        {{ $document->$column }}
                                    @endif
                                </x-slot:sub-value>
                            </x-mary-list-item>
                        @endforeach
                    @endif
                </x-mary-tab>

                <x-mary-tab name="metadata" label="Métadonnés">
                    @if ($metadata)
                    @php $counter = 0; @endphp
                    <ul>
                    @foreach ($metadata as $key => $value)
                            @if (is_string($value))
                                <x-mary-list-item :item="$metadata" separator hover>
                                    <x-slot:value>
                                        {{ $key }}
                                    </x-slot:value>
                                    <x-slot:sub-value>
                                        {{ $value }}
                                    </x-slot:sub-value>
                                </x-mary-list-item>
                                @php $counter++; @endphp
                            @endif
                            @if ($counter >= 10)
                                @break
                            @endif
                        @endforeach
                    </ul>
                     @endif
                    </x-mary-tab>

                <x-mary-tab name="content" label="Contenu">
                    <x-mary-textarea
                        label="Contenu"
                        wire:model="content"
                        placeholder="Contenu de document"
                        rows="15"
                        inline 
                    />
                </x-mary-tab>
                <x-mary-tab name="notes" label="Notes">
                    <x-mary-form wire:submit.prevent="addNote">
                        <x-mary-textarea
                            label="Ajouter une note"
                            wire:model.defer="note"
                            hint="Max 1000 chars"
                            rows="5"
                            inline 
                        />
                        
                        <x-slot:actions>
                            <x-mary-button label="Ajouter une note" class="btn-primary" type="submit" spinner="addNote" />
                        </x-slot:actions>
                    </x-mary-form>
        
                    <!-- Notes container -->
                    @if ($notes->count() > 0)
                    <ul class="mt-4 overflow-y-scroll h-[300px]" id="notes-container">
                        @foreach ($notes as $note)
                            @if($note->writer !== NULL)
                            <x-mary-list-item :key="$note->id" :item="$note" separator hover>
                                <x-slot:avatar>
                                    <x-mary-popover>
                                        <x-slot:trigger>
                                            @if($note->writer != NULL)
                                                <div class="w-9 h-9 text-sm font-bold text-white flex items-center justify-center rounded-full border border-gray-300" style="color:#fff;background-color: {{ $note->writer->color ?? 'rgb(168,85,247)' }};">{{ $note->writer->initials }}</div>
                                            @else
                                                <x-mary-icon name="o-user"  class="w-8 h-8 m-8"/>
                                            @endif
                                        </x-slot:trigger>
                                        <x-slot:content>
                                            @if($note->writer != NULL)
                                                Nom: {{ $note->writer->name }} <br>
                                                E-mail: {{ $note->writer->email }}
                                            @else
                                                {{ __("Utilisateur supprimé") }}
                                            @endif
                                        </x-slot:content>
                                    </x-mary-popover>
                                </x-slot:avatar>
                                <x-slot:value>
                                    @if(auth()->user()->id == $note->writer->id)
                                        {{ $note->writer !== NULL ? $note->writer->name : "Utilsateur supprimé" }}
                                        <x-mary-badge value="Vous" class="badge-primary" />
                                    @else
                                        {{ $note->writer !== NULL ? $note->writer->name : "Utilsateur supprimé" }}
                                    @endif
                                </x-slot:value>
                                <x-slot:sub-value>
                                    {{ $note->content }}
                                </x-slot:sub-value>
                                @can('delete-note', $note)
                                <x-slot:actions>
                                    <x-mary-button icon="o-trash" class="btn-sm" wire:click="deleteNote({{ $note->id }})" spinner/>
                                </x-slot:actions>
                                @endcan
                            </x-mary-list-item>
                            @endif
                        @endforeach
                    </ul>
                    @else
                    <p>{{ __("Rien a afficher") }}</p>
                    @endif
                </x-mary-tab>
                <x-mary-tab name="sender" label="Emetteur">
                    <x-mary-card title="Autres documents envoyés par le même utilisateur" class="border border-gray-200">
                            <!-- Iterate through other documents by the same user -->
                            @if(count($otherDocuments) > 0)
                                @foreach ($otherDocuments as $otherDocument)
                                    <!-- Display each document as a list item -->
                                    @can('view-document', $otherDocument)
                                    <x-mary-list-item separator hover :item="$otherDocument">
                                        <x-slot:value>
                                            {{ $otherDocument->subject }}
                                        </x-slot:value>
                                        <x-slot:sub-value>
                                            Créé le {{ $otherDocument->created_at->format('d/m/Y') }}
                                            <x-mary-button  class="btn-sm btn-ghost" icon="o-link" no-wire-navigate external link="{{ $otherDocument->id }}"/>
                                        </x-slot:sub-value>
                                    </x-mary-list-item>
                                    @endcan
                                @endforeach
                                @else
                                    <p class="text-sm text-gray-600">{{ "Rien a afficher" }}</p>
                            @endif
                        <x-slot:figure>
                            <x-mary-popover>
                                <x-slot:trigger>
                                    @if($document->owner != null)
                                        <div class="m-8 w-20 h-20 text-lg font-bold text-white flex items-center justify-center rounded-full border border-gray-300" style="color:#fff;background-color: {{ $document->owner->color ?? 'rgb(168,85,247)' }};">{{ $document->owner->initials }}</div>
                                    @else
                                            <x-mary-icon name="o-user"  class="w-8 h-8 m-8"/>
                                    @endif
                                </x-slot:trigger>
                                <x-slot:content>
                                    @if($document->owner != null)
                                        Nom: {{ $document->owner->name }} <br>
                                        E-mail: {{ $document->owner->email }}
                                    @else
                                        {{ __("Utilisateur supprimé") }}
                                    @endif
                                </x-slot:content>
                            </x-mary-popover>
                        </x-slot:figure>
                    </x-mary-card>
                </x-mary-tab>
            </x-mary-tabs>
        </div>  

            <embed id="pdf-embed" src="{{ $this->getDocumentUrl() }}" type="application/pdf" class="h-screen col-span-3 w-full hidden" />
            <div id="pdf-viewer-container" class="h-fit flex items-center justify-center overflow-auto col-span-3 "></div>

                <script>
                    function handlePdfJsError() {
                        document.querySelectorAll('.controls').forEach(element => {
                            element.style.display = 'none'
                        });
                        document.getElementById('pdf-viewer-container').style.display = 'none';
                        document.getElementById('pdf-embed').style.display = 'block';
                    }
                    function printPdf() {
                        const url = '{{ $this->getDocumentUrl() }}';
                        const printWindow = window.open(url, '_blank');
                        printWindow.onload = function() {
                            printWindow.print();
                        };
                    }
                    function initializePdfViewer() {
                        const url = '{{ $this->getDocumentUrl() }}';
                        const container = document.getElementById('pdf-viewer-container');
                        const prevPageBtn = document.getElementById('prev-page');
                        const nextPageBtn = document.getElementById('next-page');
                        const zoomInBtn = document.getElementById('zoom-in');
                        const zoomOutBtn = document.getElementById('zoom-out');
                        const pageInput = document.getElementById('page-input');
                        const pageCountSpan = document.getElementById('page-count');
                
                        let currentPage = 1;
                        let scale = 1;
                        let pdfDocument = null;
                        const minScale = 0.5; // Minimum scale value
                        const maxScale = 2.5 // Maximum scale value

                        const loadingTask = pdfjsLib.getDocument(url);
                        loadingTask.promise.then(pdf => {
                            pdfDocument = pdf;
                            pageCountSpan.textContent = pdf.numPages;
                            renderPage(currentPage);

                            prevPageBtn.addEventListener('click', () => {
                                if (currentPage > 1) {
                                    currentPage--;
                                    renderPage(currentPage);
                                }
                            });
                
                            nextPageBtn.addEventListener('click', () => {
                                if (currentPage < pdf.numPages) {
                                    currentPage++;
                                    renderPage(currentPage);
                                }
                            });
                
                            zoomInBtn.addEventListener('click', () => {
                                if(scale < maxScale){
                                    scale += 0.1;
                                    renderPage(currentPage);
                                }
                            });
                
                            zoomOutBtn.addEventListener('click', () => {
                                if (scale > minScale) {
                                    scale -= 0.1;
                                    renderPage(currentPage);
                                }
                            });
                
                            pageInput.addEventListener('change', () => {
                                let pageNumber = parseInt(pageInput.value);
                                if (pageNumber >= 1 && pageNumber <= pdf.numPages) {
                                    currentPage = pageNumber;
                                    renderPage(currentPage);
                                } else {
                                    pageInput.value = currentPage;
                                }
                            });
                        }, function (reason) {
                            console.error(reason);
                        });
                
                        function renderPage(pageNum) {
                            pdfDocument.getPage(pageNum).then(page => {
                                const viewport = page.getViewport({ scale });
                
                                // Prepare canvas using PDF page dimensions
                                const canvas = document.createElement('canvas');
                                const context = canvas.getContext('2d');
                                canvas.height = viewport.height;
                                canvas.width = viewport.width;
                
                                // Clear previous canvas content
                                container.innerHTML = '';
                
                                // Append canvas to the container
                                container.appendChild(canvas);
                
                                // Render PDF page into canvas context
                                const renderContext = {
                                    canvasContext: context,
                                    viewport: viewport
                                };
                                page.render(renderContext);
                
                                // Update the input field value
                                pageInput.value = pageNum;
                            });
                        }
                    }

                    document.addEventListener('DOMContentLoaded', initializePdfViewer);
                    
                </script>
                 <script src="https://cdnjs.cloudflare.com/ajax/libs/pdf.js/2.10.377/pdf.min.js" onerror="handlePdfJsError()"></script>
                @script
                <script>
                    $wire.on('refresh-view', () => {
                        initializePdfViewer();
                    });
                </script>
                @endscript
    </div>    
</div>    
    
