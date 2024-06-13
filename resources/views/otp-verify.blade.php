<x-app-layout>
    <x-mary-card title="Vérification du code OTP" subtitle="Entrez votre code OTP" separator>
        <div class="card">
            @if(session('error'))
                <div class="alert alert-danger">{{ session('error') }}</div>
            @endif
            <form method="POST" action="{{ route('otp.verify.post', ['id' => $documentId]) }}">
                @csrf
                <div class="mb-4 rounded-md">
                    <input type="text" name="otp_code" id="otp_code" class="rounded-md" required>
                </div>
                <button type="submit" class="btn btn-primary">Vérifier</button>
            </form>
        </div>
    </x-mary-card>
</x-app-layout>

{{--  --}}
