<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Collections;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionsController extends Controller
{
    public function index(Request $request)
    {
        $query = Collections::with('products');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('sub_title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $collections = $query->paginate(15)->appends($request->all());

        return view('admin.collections.index', compact('collections'));
    }

    public function create()
    {
        $products = Products::orderBy('name')->where('status', 'active')->get();
        return view('admin.collections.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug',
            'sub_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable|mimes:mp4,avi,mov,wmv|max:51200',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        $data = $request->all();
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $collection = Collections::create($data);
        if ($request->hasFile('video')) {
            $collection->addMediaFromRequest('video')
                ->toMediaCollection('video');
        }

        if ($request->has('products')) {
            $collection->products()->attach($request->products);
        }
        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection created successfully');
    }

    public function show(Collections $collection)
    {
        $collection->load('products');
        return view('admin.collections.show', compact('collection'));
    }

    public function edit(Collections $collection)
    {
        $products = Products::orderBy('name')->get();
        $collection->load('products');
        return view('admin.collections.edit', compact('collection', 'products'));
    }

    public function update(Request $request, Collections $collection)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:collections,slug,' . $collection->id,
            'sub_title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'video' => 'nullable|file|max:51200',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id'
        ]);

        $data = $request->except(['products']);

        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if ($request->hasFile('video')) {
            $collection->clearMediaCollection('video');
            $collection->addMediaFromRequest('video')
                ->toMediaCollection('video');
        }

        $collection->update($data);

        if ($request->has('products')) {
            $collection->products()->sync($request->products);
        } else {
            $collection->products()->detach();
        }

        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection updated successfully');
    }

    public function destroy(Collections $collection)
    {
        // Delete video file
        if ($collection->video && Storage::disk('public')->exists('collections/' . $collection->video)) {
            Storage::disk('public')->delete('collections/' . $collection->video);
        }

        // Detach all products
        $collection->products()->detach();

        $collection->delete();

        return redirect()->route('admin.collections.index')
            ->with('success', 'Collection deleted successfully');
    }

    // Remove video from collection
    public function removeVideo(Collections $collection)
    {
        if ($collection->video && Storage::disk('public')->exists('collections/' . $collection->video)) {
            Storage::disk('public')->delete('collections/' . $collection->video);
        }

        $collection->update(['video' => null]);

        return redirect()->back()
            ->with('success', 'Video removed successfully');
    }

    // Add product to collection
    public function addProduct(Request $request, Collections $collection)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id'
        ]);

        if (!$collection->products()->where('product_id', $request->product_id)->exists()) {
            $collection->products()->attach($request->product_id);
            return redirect()->back()->with('success', 'Product added to collection');
        }

        return redirect()->back()->with('error', 'Product already in collection');
    }

    // Remove product from collection
    public function removeProduct(Collections $collection, Products $product)
    {
        $collection->products()->detach($product->id);
        return redirect()->back()->with('success', 'Product removed from collection');
    }
}
