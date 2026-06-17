<?php
// filepath: app/Http/Controllers/Admin/ProductController.php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use App\Models\Products;
use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Products::with(['category']);

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where('name', 'like', '%' . $request->search . '%');
        }

        if ($request->category_id) {
            $query->where('category_id', $request->category_id);
        }

        $products = $query->latest()->paginate(15);
        $categories = Categories::all();
        return view('admin.products.index', compact('products', 'categories'));
    }

    public function create()
    {
        $categories = Categories::where('status', 'active')->get();
        $scents = Scent::where('is_active', true)->get();
        return view('admin.products.create', compact('categories', 'scents'));
    }

    public function removeGalleryImage(Request $request, Products $product)
    {
        $request->validate([
            'media_id' => 'required|integer'
        ]);

        try {
            $media = $product->media()
                ->where('id', $request->media_id)
                ->where('collection_name', 'gallery')
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
                'remaining_count' => $product->getMedia('gallery')->count()
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error removing image: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'barcode' => 'nullable|string|exists:products,barcode',
            'price' => 'required|numeric|min:0|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'scrent_id' => 'required|exists:scents,id',
            'stock' => 'required|integer|min:0',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:men,women,unisex',
            'status' => 'required|in:active,inactive',
            // Technical specifications validation
            'concentration' => 'nullable|string|max:50',
            'volume_ml' => 'nullable|integer|min:1|max:1000',
            'origin' => 'nullable|string|max:100',
            'longevity' => 'nullable|string|max:50',
            'sillage' => 'nullable|string|max:50',
            'main_ingredients' => 'nullable|string|max:1000',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name) . '-' . uniqid();
        $product = Products::create($data);
        if ($request->hasFile('image')) {
            $product->addMediaFromRequest('image')
                ->toMediaCollection('main_image');
        }

        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $galleryFile) {
                $product->addMedia($galleryFile)
                    ->toMediaCollection('gallery');
            }
        }


        return redirect()->route('admin.products.index')->with('success', 'Product created successfully');
    }

    public function show(Products $product)
    {
        return view('admin.products.show', compact('product'));
    }

    public function edit(Products $product)
    {
        $scents = Scent::where('is_active', true)->get();
        $categories = Categories::where('status', 'active')->get();
        return view('admin.products.edit', compact('product', 'categories', 'scents'));
    }

    public function update(Request $request, Products $product)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'barcode' => 'nullable|string|unique:products,barcode,' . $product->id,
            'price' => 'required|numeric|min:0|max:99999999.99',
            'category_id' => 'required|exists:categories,id',
            'stock' => 'required|integer|min:0',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'type' => 'required|in:men,women,unisex',
            'status' => 'required|in:active,inactive',
            'concentration' => 'nullable|string|max:50',
            'volume_ml' => 'nullable|integer|min:1|max:1000',
            'origin' => 'nullable|string|max:100',
            'longevity' => 'nullable|string|max:50',
            'sillage' => 'nullable|string|max:50',
            'main_ingredients' => 'nullable|string|max:1000',
            'existing_gallery' => 'nullable|array',
            'existing_gallery.*' => 'integer|exists:media,id',
            'gallery' => 'nullable|array',
            'gallery.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $data = $request->except(['image', 'gallery', 'existing_gallery', '_token', '_method']);

        // Cập nhật slug nếu tên thay đổi
        if ($product->name !== $request->name) {
            $data['slug'] = Str::slug($request->name) . '-' . uniqid();
        }

        // Cập nhật main image nếu có
        if ($request->hasFile('image')) {
            $product->clearMediaCollection('main_image');
            $product->addMediaFromRequest('image')
                ->toMediaCollection('main_image');
        }

        // Xử lý gallery images
        $existingGalleryIds = $request->input('existing_gallery', []);
        $currentGalleryIds = $product->getMedia('gallery')->pluck('id')->toArray();

        // Xóa những ảnh không còn được chọn
        $imagesToDelete = array_diff($currentGalleryIds, $existingGalleryIds);
        if (!empty($imagesToDelete)) {
            $product->getMedia('gallery')
                ->whereIn('id', $imagesToDelete)
                ->each(function ($media) {
                    $media->delete();
                });
        }

        // Thêm ảnh gallery mới nếu có
        if ($request->hasFile('gallery')) {
            foreach ($request->file('gallery') as $galleryFile) {
                $product->addMedia($galleryFile)
                    ->toMediaCollection('gallery');
            }
        }

        // Cập nhật thông tin sản phẩm
        $product->update($data);

        return redirect()->route('admin.products.index')->with('success', 'Product updated successfully');
    }

    public function destroy(Products $product)
    {
        $product->delete();
        return redirect()->route('admin.products.index')->with('success', 'Product deleted successfully');
    }
}
