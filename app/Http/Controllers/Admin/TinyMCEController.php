<?php
// filepath: c:\laragon\www\perfume-client\app\Http\Controllers\Admin\TinyMCEController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TinyMCEController extends Controller
{
    public function upload(Request $request)
    {
        $request->validate([
            'file' => 'required|image|mimes:jpeg,png,jpg,gif,webp|max:2048'
        ]);

        try {
            $file = $request->file('file');
            $filename = time() . '_' . Str::random(10) . '.' . $file->getClientOriginalExtension();

            // Tạo thư mục nếu chưa có
            $uploadPath = public_path('uploads/tinymce');
            if (!file_exists($uploadPath)) {
                mkdir($uploadPath, 0755, true);
            }

            // Upload file
            $file->move($uploadPath, $filename);
            $url = asset('uploads/tinymce/' . $filename);

            return response()->json([
                'location' => $url
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Upload failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
