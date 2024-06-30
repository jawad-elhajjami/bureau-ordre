<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\Document;

class VerifyOtp
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();
        $documentId = $request->route('id');
        $document = Document::find($documentId);

        // Skip OTP verification for admins
        if ($user && $user->is_admin) {
            return $next($request);
        }

        // Skip verification if user is owner of the document
        if($user->id == $document->user_id){
            return $next($request);
        }

        // Skip OTP verification if OTP code is null
        if ($document && is_null($document->otp_code)) {
            return $next($request);
        }

        $otpVerifiedDocuments = Session::get('otp_verified_documents', []);

        if (!in_array($documentId, $otpVerifiedDocuments)) {
            return redirect()->route('otp.verify', ['id' => $documentId]);
        }

        return $next($request);
    }
}
