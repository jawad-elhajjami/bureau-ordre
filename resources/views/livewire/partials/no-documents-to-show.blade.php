<div class="flex items-center flex-col justify-center p-12">
    <img src="{{ asset('illustrations/Search-amico.svg') }}" width="400px">
    <h3 class="text-3xl text-center font-bold text-gray-900 dark:text-neutral-200 mb-4">{{ __("Aucun document trouvé") }}</h3>
    <p class="text-gray-400 text-center dark:text-neutral-600 text-md mb-4">{{ __("Vous pouvez créer un nouveau document en cliquant sur le bouton çi dessous.") }}</p>
    <x-mary-button class="btn-primary" label="Ajouter un document" icon="o-plus" link="{{ route('create-document') }}" />
</div>
