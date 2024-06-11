<div class="container mx-auto my-p-4">
    <div class="my-card">
        <div class="my-card-header">
            <h2>Verification OTP</h2>
        </div>
        <div class="my-card-body">
            @if(session('error'))
                <div class="my-alert my-alert-danger">{{ session('error') }}</div>
            @endif
            <form method="POST" action="{{ route('otp.verify.post', ['id' => $documentId]) }}">
                @csrf
                <div class="my-mb-4">
                    <label for="otp_code" class="my-form-label">OTP Code</label>
                    <input type="text" name="otp_code" id="otp_code" class="my-form-control" required>
                </div>
                <button type="submit" class="my-btn my-btn-primary">Verifier OTP</button>
            </form>
        </div>
    </div>
</div>
