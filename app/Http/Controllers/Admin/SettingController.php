<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class SettingController extends Controller
{
    public function show()
    {
        $setting = Setting::first();
        return view('admin.settings.index', compact('setting'));
    }


    public function store(Request $request)
    {

        $request->validate([
            'logo_name' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'address' => 'nullable|string|max:500',
            'hotline' => 'nullable|string|max:20',
            'email_contact' => 'nullable|email|max:255',
            'link_social_facebook' => 'nullable|url|max:255',
            'link_social_tiktok' => 'nullable|url|max:255',
            'link_social_youtube' => 'nullable|url|max:255',
            'link_social_instagram' => 'nullable|url|max:255',
            'banner_is_image' => 'boolean',
            'banner_image' => 'nullable',
            'title_banner' => 'nullable|string|max:255',
            'sub_title_banner' => 'nullable|string|max:255',
            'introduce_video_manufacture' => 'nullable|mimes:mp4,avi,mov,wmv',
            'introduce_video_design' => 'nullable|mimes:mp4,avi,mov,wmv',
            'video_tiktok_review' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
            'discount_global'   => 'nullable|numeric',
            'color_title_banner' => 'nullable|string|max:7',
            'color_subtitle_banner' => 'nullable|string|max:7',
        ]);

        $data = $request->all();

        // Set banner type
        $data['banner_is_image'] = $request->has('banner_is_image') ? 1 : 0;

        $setting = Setting::firstOrCreate();
        $setting->update($data);

        if ($request->hasFile('logo_name')) {
            $setting->clearMediaCollection('logo');
            $setting->addMediaFromRequest('logo_name')
                ->toMediaCollection('logo');
        }
        if ($request->hasFile('banner_image')) {
            foreach ($request->file('banner_image') as $galleryFile) {
                $setting->addMedia($galleryFile)
                    ->toMediaCollection('banner_image');
            }
        }
        // if ($request->hasFile('banner_image')) {
        //     $setting->clearMediaCollection('banner_image');
        //     $setting->addMediaFromRequest('banner_image')
        //         ->toMediaCollection('banner_image');
        // }

        if ($request->hasFile('introduce_video_manufacture')) {
            $setting->clearMediaCollection('intro_videos_manufacture');
            $setting->addMediaFromRequest('introduce_video_manufacture')
                ->toMediaCollection('intro_videos_manufacture');
        }

        if ($request->hasFile('introduce_video_design')) {
            $setting->clearMediaCollection('intro_videos_design');
            $setting->addMediaFromRequest('introduce_video_design')
                ->toMediaCollection('intro_videos_design');
        }


        return redirect()->route('admin.settings.show')
            ->with('success', 'Settings created successfully');
    }


    public function deleteBanner(Request $request)
    {
        $request->validate([
            'media_id' => 'required|integer'
        ]);

        try {
            $setting = Setting::first();
            $media = $setting->media()
                ->where('id', $request->media_id)
                ->where('collection_name', 'banner_image')
                ->first();

            if (!$media) {
                return response()->json([
                    'success' => false,
                    'message' => 'Image not found in gallery'
                ], 404);
            }
            $media->delete();

            return response()->json([
                'success' => true,
                'message' => 'Image removed successfully',
                'remaining_count' => $setting->getMedia('banner_image')->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function clearCache()
    {
        \Cache::forget('site_settings');
        return redirect()->back()->with('success', 'Settings cache cleared successfully');
    }
}
