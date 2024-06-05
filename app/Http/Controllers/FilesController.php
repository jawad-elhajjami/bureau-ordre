<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FilesController extends Controller
{
    public function show(Request $request, $path)
    {
        $disk = Storage::disk('files');

        abort_if(
            ! $disk->exists($path),
            404,
            "The file doesn't exist. Check the path."
        );

        // You can add additional checks here to ensure the user is authorized to download the file
        // For example, check if the user owns the file or has permission to access it

        return $disk->download($path);
    }
}
