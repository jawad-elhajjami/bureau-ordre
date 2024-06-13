<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Document;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DocumentController extends Controller
{
    // public function showOtpForm($id)
    // {
    //     return view('otp-verify', ['documentId' => $id]);
    // }
    // public function verifyOtp(Request $request, $id)
    // {
    //     $request->validate([
    //         'otp_code' => 'required|numeric',
    //     ]);

    //     $document = Document::findOrFail($id);

    //     if ($request->otp_code == $document->otp_code) {
    //         $otpVerifiedDocuments = Session::get('otp_verified_documents', []);
    //         $otpVerifiedDocuments[] = $id;
    //         Session::put('otp_verified_documents', $otpVerifiedDocuments);

    //         return redirect()->route('documents.view', ['id' => $id])->with('success', 'OTP vérifié avec succès.');
    //     }

    //     return redirect()->route('otp.verify', ['id' => $id])->with('error', 'Code OTP non valide.');
    // }
    // public function view($id)
    // {
    //     $document = Document::findOrFail($id);
    //     return view('documents.view', compact('document'));
    // }
}
