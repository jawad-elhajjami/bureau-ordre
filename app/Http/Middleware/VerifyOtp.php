<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class VerifyOtp
{
    public function handle(Request $request, Closure $next)
    {
        $user = Auth::user();

        // Skip OTP verification for admins
        if ($user && $user->is_admin) {
            return $next($request);
        }

        $otpVerifiedDocuments = Session::get('otp_verified_documents', []);

        if (!in_array($request->route('id'), $otpVerifiedDocuments)) {
            return redirect()->route('otp.verify', ['id' => $request->route('id')]);
        }

        return $next($request);
    }
}
