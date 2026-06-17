<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogCategoryController extends Controller
{
    public function index(Request $request)
    {
        $query = BlogCategory::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $isActive = $request->get('status') === 'active';
            $query->where('is_active', $isActive);
        }

        // Sort
        $sortBy = $request->get('sort_by', 'sort_order');
        $sortOrder = $request->get('sort_order', 'asc');
        $query->orderBy($sortBy, $sortOrder);

        $categories = $query->paginate(15)->appends($request->all());

        return view('admin.blog-categories.index', compact('categories'));
    }

    public function create()
    {
        return view('admin.blog-categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['image']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
            $originalSlug = $data['slug'];
            $counter = 1;
            while (BlogCategory::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        $data['is_active'] = $request->has('is_active') ? true : false;
        $category = BlogCategory::create($data);

        // Handle image upload
        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category created successfully');
    }

    public function show(BlogCategory $blogCategory)
    {
        $blogCategory->load('blogs');
        return view('admin.blog-categories.show', compact('blogCategory'));
    }

    public function edit(BlogCategory $blogCategory)
    {
        return view('admin.blog-categories.edit', compact('blogCategory'));
    }

    public function update(Request $request, BlogCategory $blogCategory)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blog_categories,slug,' . $blogCategory->id,
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
        ]);

        $data = $request->except(['image']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        if ($request->hasFile('image')) {
            $blogCategory->clearMediaCollection('image');
            $blogCategory->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }
        $data['is_active'] = $request->has('is_active') ? true : false;
        $blogCategory->update($data);

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category updated successfully');
    }

    public function destroy(BlogCategory $blogCategory)
    {
        // Check if category has blogs
        if ($blogCategory->blogs()->count() > 0) {
            return redirect()->back()
                ->with('error', 'Cannot delete category that has blog posts');
        }

        // Delete image file
        if ($blogCategory->image && Storage::disk('public')->exists('blog_categories/' . $blogCategory->image)) {
            Storage::disk('public')->delete('blog_categories/' . $blogCategory->image);
        }

        $blogCategory->delete();

        return redirect()->route('admin.blog-categories.index')
            ->with('success', 'Blog category deleted successfully');
    }

    public function toggleStatus(BlogCategory $blogCategory)
    {
        $blogCategory->update(['is_active' => !$blogCategory->is_active]);

        return redirect()->back()
            ->with('success', 'Category status updated successfully');
    }
}
