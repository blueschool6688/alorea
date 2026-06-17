<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Categories;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Exception;

class CategoriesController extends Controller
{
    /**
     * Display a listing of categories
     */
    public function index(Request $request)
    {
        $query = Categories::with(['parent', 'children'])->withCount(['products']);
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'LIKE', '%' . $request->search . '%')
                    ->orWhere('description', 'LIKE', '%' . $request->search . '%');
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('parent_id')) {
            if ($request->parent_id === 'null') {
                $query->whereNull('parent_id');
            } else {
                $query->where('parent_id', $request->parent_id);
            }
        }
        $categories = $query->paginate(15);
        $parentCategories = Categories::whereNull('parent_id')
            ->where('status', 'active')
            ->get();
        return view('admin.categories.index', compact('categories', 'parentCategories'));
    }

    /**
     * Show the form for creating a new category
     */
    public function create()
    {
        $parentCategories = Categories::whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return view('admin.categories.create', compact('parentCategories'));
    }

    /**
     * Store a newly created category in storage
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name',
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);

        $data = $request->all();
        $data['slug'] = Str::slug($request->name);
        $originalSlug = $data['slug'];
        $count = 1;
        while (Categories::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count;
            $count++;
        }

        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $category = Categories::create($data);
        if ($request->hasFile('image')) {
            $category->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        $message = 'Category "' . $category->name . '" created successfully.';
        return redirect()->route('admin.categories.index')->with('success', $message);
    }


    /**
     * Show the form for editing the specified category
     */
    public function edit(Categories $category)
    {
        $parentCategories = Categories::whereNull('parent_id')
            ->where('status', 'active')
            ->where('id', '!=', $category->id) // Exclude current category
            ->orderBy('name')
            ->get();

        // Exclude descendants to prevent circular reference
        $descendants = $this->getDescendantIds($category);
        $parentCategories = $parentCategories->whereNotIn('id', $descendants);

        return view('admin.categories.edit', compact('category', 'parentCategories'));
    }

    public function update(Request $request, Categories $category)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:categories,name,' . $category->id,
            'description' => 'nullable|string',
            'parent_id' => 'nullable|exists:categories,id',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
            'status' => 'required|in:active,inactive',
            'is_featured' => 'boolean',
            'meta_title' => 'nullable|string|max:255',
            'meta_description' => 'nullable|string|max:500',
            'meta_keywords' => 'nullable|string|max:255',
        ]);
        if ($request->parent_id) {
            $descendants = $this->getDescendantIds($category);
            if (in_array($request->parent_id, $descendants)) {
                return back()->withErrors(['parent_id' => 'Cannot set a descendant category as parent.']);
            }

            if ($request->parent_id == $category->id) {
                return back()->withErrors(['parent_id' => 'Category cannot be its own parent.']);
            }
        }
        $data = $request->all();
        if ($request->name !== $category->name) {
            $data['slug'] = Str::slug($request->name);
            $originalSlug = $data['slug'];
            $count = 1;
            while (Categories::where('slug', $data['slug'])->where('id', '!=', $category->id)->exists()) {
                $data['slug'] = $originalSlug . '-' . $count;
                $count++;
            }
        }

        if ($request->hasFile('image')) {
            $category->clearMediaCollection('image');
            $category->addMediaFromRequest('image')
                ->toMediaCollection('image');
        }

        $data['is_featured'] = $request->has('is_featured') ? true : false;
        $category->update($data);
        return redirect()->route('admin.categories.index')->with('success', 'Category "' . $category->name . '" updated successfully.');
    }

    /**
     * Remove the specified category from storage
     */
    public function destroy(Categories $category)
    {
        try {
            if ($category->children()->count() > 0) {
                return back()->withErrors(['error' => 'Cannot delete category with subcategories. Please delete or move subcategories first.']);
            }
            $productsCount = $category->products()->count();
            if ($productsCount > 0) {
                return back()->withErrors(['error' => "Cannot delete category with {$productsCount} products. Please move or delete products first."]);
            }
            $categoryName = $category->name;
            $category->delete();

            return redirect()->route('admin.categories.index')->with('success', "Category \"{$categoryName}\" deleted successfully.");
        } catch (Exception $e) {
            return back()->withErrors(['error' => 'Error deleting category: ' . $e->getMessage()]);
        }
    }

    /**
     * Get category tree for API
     */
    public function tree()
    {
        $categories = Categories::with('allChildren')
            ->whereNull('parent_id')
            ->where('status', 'active')
            ->orderBy('name')
            ->get();

        return response()->json($categories);
    }
    /**
     * Get all descendant IDs of a category
     */
    private function getDescendantIds(Categories $category, $ids = [])
    {
        $children = $category->children;

        foreach ($children as $child) {
            $ids[] = $child->id;
            $ids = $this->getDescendantIds($child, $ids);
        }

        return $ids;
    }

    /**
     * Toggle category status
     */
    public function toggleStatus(Categories $category)
    {
        $category->update([
            'status' => $category->status === 'active' ? 'inactive' : 'active'
        ]);

        return response()->json([
            'success' => true,
            'status' => $category->status,
            'message' => 'Category status updated successfully.'
        ]);
    }

    /**
     * Toggle featured status
     */
    public function toggleFeatured(Categories $category)
    {
        $category->update([
            'is_featured' => !$category->is_featured
        ]);

        return response()->json([
            'success' => true,
            'is_featured' => $category->is_featured,
            'message' => 'Category featured status updated successfully.'
        ]);
    }
}
