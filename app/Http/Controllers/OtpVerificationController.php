<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Session;

class OtpVerificationController extends Controller
{
    public function show($id)
    {
        return view('otp-verify', ['documentId' => $id]);
    }

    public function verify(Request $request, $id)
    {
        $request->validate([
            'otp_code' => 'required|numeric',
        ]);

        $document = Document::findOrFail($id);

        if ($request->otp_code == $document->otp_code) {
            $otpVerifiedDocuments = Session::get('otp_verified_documents', []);
            $otpVerifiedDocuments[] = $id;
            Session::put('otp_verified_documents', $otpVerifiedDocuments);

            return redirect()->route('documents.view', ['id' => $id])->with('success', 'OTP verified successfully.');
        }

        return redirect()->route('otp.verify', ['id' => $id])->with('error', 'Invalid OTP code.');
    }
}
