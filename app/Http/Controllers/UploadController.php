<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class UploadController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'file' => 'required|file',
        ]);

        $file = $request->file('file');
        $uuid = Str::uuid()->toString();
        $filename = $file->getClientOriginalName();
        $extension = $file->getClientOriginalExtension();

        $path = $file->storeAs('photos', $uuid . '.' . $extension, 's3');

        // Get the full URL
        $url = Storage::disk('s3')->url($path);

        return response()->json([
            'uuid' => $uuid,
            'name' => $filename,
            'original_url' => $url,
            'cdn_url' => $url,
            'mime_type' => $file->getMimeType(),
            'size' => $file->getSize(),
        ]);
    }
}
