<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\BlogCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BlogController extends Controller
{
    public function index(Request $request)
    {
        $query = Blog::with(['category']);

        // Search
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('author_name', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        // Filter by category
        if ($request->filled('category')) {
            $query->where('category_id', $request->get('category'));
        }

        // Filter by featured
        if ($request->filled('featured')) {
            $query->where('is_featured', $request->get('featured') === '1');
        }

        // Sort
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $blogs = $query->paginate(15)->appends($request->all());

        // Additional data
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.blogs.index', compact('blogs', 'categories'));
    }

    public function create()
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();

        return view('admin.blogs.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug',
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'source_url' => 'nullable|url',
            'social_shares' => 'nullable|array'
        ]);

        $data = $request->except(['featured_image', 'gallery', 'meta_keywords', 'social_shares']);


        // Auto generate slug if not provided
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);

            $originalSlug = $data['slug'];
            $counter = 1;
            while (Blog::where('slug', $data['slug'])->exists()) {
                $data['slug'] = $originalSlug . '-' . $counter;
                $counter++;
            }
        }
        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $data['meta_keywords'] = $request->meta_keywords ?? [];
        $data['social_shares'] = $request->social_shares ?? [];
        if ($data['status'] === 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        $wordCount = str_word_count(strip_tags($data['content']));
        $data['reading_time'] = max(1, ceil($wordCount / 200));

        $blog = Blog::create($data);
        if ($request->hasFile('featured_image')) {
            $blog->addMediaFromRequest('featured_image')
                ->toMediaCollection('image');
        }

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post created successfully');
    }
    public function edit(Blog $blog)
    {
        $categories = BlogCategory::where('is_active', true)->orderBy('name')->get();
        return view('admin.blogs.edit', compact('blog', 'categories'));
    }

    public function update(Request $request, Blog $blog)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:blogs,slug,' . $blog->id,
            'excerpt' => 'nullable|string|max:500',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'category_id' => 'nullable|exists:blog_categories,id',
            'author_name' => 'required|string|max:255',
            'status' => 'required|in:draft,published,archived',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|array',
            'source_url' => 'nullable|url',
            'remove_featured_image' => 'nullable|numeric',
            'social_shares' => 'nullable|array'
        ]);

        $data = $request->except(['featured_image', 'gallery', 'meta_keywords', 'social_shares']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['title']);
        }
        $data['is_featured'] = $request->has('is_featured');
        $data['meta_keywords'] = $request->meta_keywords ?? [];
        $data['social_shares'] = $request->social_shares ?? [];

        // Set published_at if status changed to published
        if ($data['status'] === 'published' && $blog->status !== 'published' && empty($data['published_at'])) {
            $data['published_at'] = now();
        }
        if($request->has('remove_featured_image') && (int)$request->get('remove_featured_image') == 1) {
            $blog->clearMediaCollection('image');
        }
         // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $blog->clearMediaCollection('image');
            $blog->addMediaFromRequest('featured_image')
                ->toMediaCollection('image');
        }
        if ($data['content'] !== $blog->content) {
            $wordCount = str_word_count(strip_tags($data['content']));
            $data['reading_time'] = max(1, ceil($wordCount / 200));
        }

        $blog->update($data);

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post updated successfully');
    }

    public function destroy(Blog $blog)
    {
        // Delete featured image
        if ($blog->featured_image && Storage::disk('public')->exists('blogs/' . $blog->featured_image)) {
            Storage::disk('public')->delete('blogs/' . $blog->featured_image);
        }

        // Delete gallery images
        if ($blog->gallery) {
            foreach ($blog->gallery as $image) {
                if (Storage::disk('public')->exists('blogs/gallery/' . $image)) {
                    Storage::disk('public')->delete('blogs/gallery/' . $image);
                }
            }
        }

        $blog->delete();

        return redirect()->route('admin.blogs.index')
            ->with('success', 'Blog post deleted successfully');
    }

    public function toggleFeatured(Blog $blog)
    {
        $blog->update(['is_featured' => !$blog->is_featured]);

        return redirect()->back()
            ->with('success', 'Blog featured status updated');
    }
}
