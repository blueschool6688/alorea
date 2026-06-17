<?php

use \App\Services\VideoStreamService;

if (!function_exists('storeFileReturnName')) {
    function storeFileReturnName($image, $path = 'products', $disk = 'public')
    {
        $originalName = pathinfo($image->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = $image->getClientOriginalExtension();
        $filename = \Str::slug($originalName) . '_' . time() . '.' . $extension;
        $image->storeAs($path, $filename, $disk);
        return $filename;
    }
}
if (!function_exists('streamMediaVideo')) {
    function streamMediaVideo($media)
    {
        try {
            if (!$media) {
                return response()->json(['error' => 'Media not found'], 404);
            }

            $videoPath = str_replace('file:///', '', $media->getPath());

            if (!file_exists($videoPath)) {
                \Log::error('Video file not found: ' . $videoPath);
                return response()->json(['error' => 'Video file not found'], 404);
            }

            $streamService = new VideoStreamService($videoPath);
            return $streamService->streamVideo();
        } catch (\Exception $e) {
            \Log::error('Video streaming error: ' . $e->getMessage());
            return response()->json(['error' => 'Error streaming video'], 500);
        }
    }
}
