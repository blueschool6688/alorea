<?php
// filepath: c:\laragon\www\perfume-client\app\Http\Controllers\Admin\CKEditorController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CKEditorController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'upload' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            $file = $request->file('upload');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Upload to public/uploads/ckeditor
            $path = $file->move(public_path('uploads/ckeditor'), $filename);
            $url = asset('uploads/ckeditor/' . $filename);

            return response()->json([
                'uploaded' => true,
                'url' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'uploaded' => false,
                'error' => [
                    'message' => 'Upload failed: ' . $e->getMessage()
                ]
            ], 500);
        }
    }
}
