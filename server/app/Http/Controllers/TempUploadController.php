<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TempUploadController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|file|max:5120', // Max 5MB
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $folder = 'temp_uploads';
            $filename = Str::random(20) . '.' . $file->getClientOriginalExtension();
            $path = $file->storeAs($folder, $filename, 'public');

            return response()->json([
                'file_name' => $file->getClientOriginalName(),
                'file_path' => $path,
                'file_url' => Storage::url($path),
            ]);
        }

        return response()->json(['message' => 'No file uploaded'], 400);
    }
}
