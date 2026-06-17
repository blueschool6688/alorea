<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Scent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class ScentController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = Scent::query();

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('notes', 'like', "%{$search}%");
            });
        }

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status === 'active') {
                $query->where('is_active', true);
            } elseif ($request->status === 'inactive') {
                $query->where('is_active', false);
            }
        }
        $scents = $query->orderBy('name', 'asc')->paginate(15);

        // Get filter options
        $types = Scent::getTypes();
        $categories = Scent::getCategories();

        return view('admin.scents.index', compact('scents', 'types', 'categories'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $types = Scent::getTypes();
        $categories = Scent::getCategories();

        return view('admin.scents.create', compact('types', 'categories'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:scents,name',
            'slug' => 'nullable|string|max:255|unique:scents,slug',
            'description' => 'nullable|string',
            'type' => 'required|in:top,middle,base',
            'notes' => 'nullable|string',
            'is_active' => 'nullable'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->has('is_active') ? true : false;
        $scent = Scent::create($validated);

        return redirect()
            ->route('admin.scents.index')
            ->with('success', "Mùi hương '{$scent->name}' đã được tạo thành công!");
    }
    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Scent $scent)
    {
        $types = Scent::getTypes();
        $categories = Scent::getCategories();

        return view('admin.scents.edit', compact('scent', 'types', 'categories'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Scent $scent)
    {
        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('scents', 'name')->ignore($scent->id)
            ],
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('scents', 'slug')->ignore($scent->id)
            ],
            'description' => 'nullable|string',
            'type' => 'required|in:top,middle,base',
            'notes' => 'nullable|string',
            'is_active' => 'nullable'
        ]);

        // Auto-generate slug if not provided
        if (empty($validated['slug'])) {
            $validated['slug'] = Str::slug($validated['name']);
        }
        $validated['is_active'] = $request->has('is_active');

        $scent->update($validated);

        return redirect()
            ->route('admin.scents.index')
            ->with('success', "Mùi hương '{$scent->name}' đã được cập nhật thành công!");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Scent $scent)
    {
        $name = $scent->name;
        $scent->delete();

        return redirect()
            ->route('admin.scents.index')
            ->with('success', "Mùi hương '{$name}' đã được xóa thành công!");
    }

    /**
     * Toggle active status
     */
    public function toggleStatus(Scent $scent)
    {
        $scent->update(['is_active' => !$scent->is_active]);

        $status = $scent->is_active ? 'kích hoạt' : 'vô hiệu hóa';

        return redirect()
            ->back()
            ->with('success', "Mùi hương '{$scent->name}' đã được {$status}!");
    }
}
