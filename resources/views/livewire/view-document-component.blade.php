<div class="grid grid-cols-2">
    <iframe src="{{ $this->getDocumentUrl() }}" class="w-full h-screen"></iframe>
    <div>
        <h2 class="text-2xl">{{ $document->order_number }}</h2>
        <p>{{ $document->description }}</p>
    </div>
    

</div>