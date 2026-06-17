<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FlashSale;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;

class FlashSaleController extends Controller
{
    public function index(Request $request)
    {
        $query = FlashSale::with('products');
        if ($request->filled('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }
        if ($request->filled('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->filled('time_status')) {
            $timeStatus = $request->get('time_status');
            $now = Carbon::now();

            switch ($timeStatus) {
                case 'upcoming':
                    $query->where('start_time', '>', $now);
                    break;
                case 'running':
                    $query->where('start_time', '<=', $now)->where('end_time', '>=', $now);
                    break;
                case 'expired':
                    $query->where('end_time', '<', $now);
                    break;
            }
        }
        $sortBy = $request->get('sort_by', 'created_at');
        $sortOrder = $request->get('sort_order', 'desc');
        $query->orderBy($sortBy, $sortOrder);

        $flashSales = $query->paginate(15)->appends($request->all());

        return view('admin.flash-sales.index', compact('flashSales'));
    }

    public function create()
    {
        $products = Products::orderBy('name')->get();
        return view('admin.flash-sales.create', compact('products'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:flash_sales,slug',
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'start_time' => 'required|date|after:now',
            'end_time' => 'nullable|date|after:start_time',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_quantity' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,paused',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'type_all' => 'nullable'
        ]);

        if ($this->hasTimeConflict($request->start_time, $request->end_time)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'Thời gian Flash Sale bị trùng với một Flash Sale khác đang hoạt động. Vui lòng chọn thời gian khác.']);
        }

        if (!$request->has('products') && !$request->has('type_all')) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['products' => 'Vui lòng chọn ít nhất một sản phẩm hoặc chọn "Áp dụng cho tất cả sản phẩm".']);
        }

        $data = $request->except(['products']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['type_all'] = $request->has('type_all') ? true : false;

        $flashSale = FlashSale::create($data);
        if ($request->hasFile('banner_image')) {
            $flashSale->addMediaFromRequest('banner_image')
                ->toMediaCollection('image');
        }
        if ($request->has('products')) {
            $flashSale->products()->attach(array_values($request->products));
        } elseif ($flashSale->type_all) {
            $flashSale->products()->attach(Products::pluck('id')->toArray());
        }

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash Sale created successfully');
    }
    public function edit(FlashSale $flashSale)
    {
        $products = Products::orderBy('name')->get();
        $flashSale->load('products');
        return view('admin.flash-sales.edit', compact('flashSale', 'products'));
    }

    public function update(Request $request, FlashSale $flashSale)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:flash_sales,slug,' . $flashSale->id,
            'description' => 'nullable|string',
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:5120',
            'start_time' => 'required|date',
            'end_time' => 'nullable|date|after:start_time',
            'discount_type' => 'required|in:percent,fixed',
            'discount_value' => 'required|numeric|min:0',
            'max_discount_amount' => 'nullable|numeric|min:0',
            'max_quantity' => 'nullable|integer|min:1',
            'status' => 'required|in:draft,active,paused,ended',
            'products' => 'nullable|array',
            'products.*' => 'exists:products,id',
            'type_all' => 'nullable'
        ]);
        // Check for time conflicts (excluding current flash sale)
        if ($this->hasTimeConflict($request->start_time, $request->end_time, $flashSale->id)) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['start_time' => 'Thời gian Flash Sale bị trùng với một Flash Sale khác đang hoạt động. Vui lòng chọn thời gian khác.']);
        }
        $data = $request->except(['products', 'banner_image', 'remove_banner']);
        if (empty($data['slug'])) {
            $data['slug'] = Str::slug($data['name']);
        }
        $data['type_all'] = $request->has('type_all') ? true : false;
        if ($request->has('remove_banner') && $request->remove_banner == '1' && !$request->hasFile('banner_image')) {
            // Remove existing banner
            $flashSale->clearMediaCollection('image');
        } elseif ($request->hasFile('banner_image')) {
            // Replace with new banner
            $flashSale->clearMediaCollection('image');
            $flashSale->addMediaFromRequest('banner_image')
                ->toMediaCollection('image');
        }


        $flashSale->update($data);
        if ($request->has('products') && !$request->type_all) {
            $flashSale->products()->detach();
            $flashSale->products()->attach(array_values($request->products));
        } elseif ($request->type_all) {
            $flashSale->products()->detach();
            $flashSale->products()->attach(Products::pluck('id')->toArray());
        }

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash Sale đã được cập nhật thành công!');
    }

    public function destroy(FlashSale $flashSale)
    {
        // Delete banner file
        if ($flashSale->banner_image && Storage::disk('public')->exists('flash_sales/' . $flashSale->banner_image)) {
            Storage::disk('public')->delete('flash_sales/' . $flashSale->banner_image);
        }

        // Detach all products
        $flashSale->products()->detach();

        $flashSale->delete();

        return redirect()->route('admin.flash-sales.index')
            ->with('success', 'Flash Sale deleted successfully');
    }

    // Toggle status
    public function toggleStatus(FlashSale $flashSale)
    {
        $newStatus = $flashSale->status === 'active' ? 'paused' : 'active';
        $flashSale->update(['status' => $newStatus]);

        return redirect()->back()
            ->with('success', "Flash Sale status changed to {$newStatus}");
    }

    // Toggle featured
    public function toggleFeatured(FlashSale $flashSale)
    {
        $flashSale->update(['is_featured' => !$flashSale->is_featured]);

        return redirect()->back()
            ->with('success', 'Flash Sale featured status updated');
    }

    // Remove banner
    public function removeBanner(FlashSale $flashSale)
    {
        if ($flashSale->banner_image && Storage::disk('public')->exists('flash_sales/' . $flashSale->banner_image)) {
            Storage::disk('public')->delete('flash_sales/' . $flashSale->banner_image);
        }

        $flashSale->update(['banner_image' => null]);

        return redirect()->back()
            ->with('success', 'Banner removed successfully');
    }

    /**
     * Kiểm tra xem có Flash Sale nào trùng thời gian không
     */
    private function hasTimeConflict($startTime, $endTime, $excludeId = null)
    {
        return FlashSale::where('status', '!=', 'ended')
            ->when($excludeId, function ($query, $excludeId) {
                return $query->where('id', '!=', $excludeId);
            })
            ->where(function ($query) use ($startTime, $endTime) {
                if ($endTime) {
                    $query->where(function ($q) use ($startTime, $endTime) {
                        $q->where(function ($subQ) use ($startTime, $endTime) {
                            $subQ->where('start_time', '<=', $endTime)
                                ->where(function ($endQ) use ($startTime) {
                                    $endQ->whereNull('end_time')
                                        ->orWhere('end_time', '>=', $startTime);
                                });
                        });
                    });
                } else {
                    $query->where(function ($q) use ($startTime) {
                        $q->where('start_time', '>=', $startTime)
                            ->orWhere(function ($subQ) use ($startTime) {
                                $subQ->where('start_time', '<=', $startTime)
                                    ->where(function ($endQ) use ($startTime) {
                                        $endQ->whereNull('end_time')
                                            ->orWhere('end_time', '>=', $startTime);
                                    });
                            });
                    });
                }
            })
            ->exists();
    }
}
