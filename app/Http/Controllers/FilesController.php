<?php

namespace App\Http\Controllers;

use App\Models\Document;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
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

        // Find the document based on the file path
        $document = Document::where('file_path', $path)->firstOrFail();

        // Authorize the view-document action
        if (Gate::denies('view-document', $document)) {
            abort(403, "You are not authorized to view this file.");
        }

       // Get the file content
       $fileContent = $disk->get($path);

       // Get the file mime type
       $mimeType = $disk->mimeType($path);

       return response($fileContent, 200)
           ->header('Content-Type', $mimeType)
           ->header('Content-Disposition', 'inline; filename="'.basename($path).'"');
    }
}
