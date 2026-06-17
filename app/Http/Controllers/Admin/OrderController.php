<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Products;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Carbon\Carbon;
use Barryvdh\DomPDF\Facade\Pdf;
use App\Exports\OrdersExport;
use App\Models\Setting;
use Maatwebsite\Excel\Facades\Excel;

class OrderController extends Controller
{
    /**
     * Display a listing of orders
     */
    public function index(Request $request)
    {
        $query = Order::with(['user', 'items.product'])
            ->orderBy('created_at', 'desc');

        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter by payment status
        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        // Filter by date range
        if ($request->filled('date_from')) {
            $query->whereDate('created_at', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('created_at', '<=', $request->date_to);
        }

        // Search by order number or customer
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                    ->orWhere('customer_name', 'like', "%{$search}%")
                    ->orWhere('customer_email', 'like', "%{$search}%")
                    ->orWhere('customer_phone', 'like', "%{$search}%");
            });
        }

        $orders = $query->paginate(2);

        $settings = \App\Models\Setting::getSettings();

        return view('admin.orders.index', compact('orders', 'settings'));
    }

    /**
     * Show the form for creating a new order
     */
    public function create()
    {
        $users = User::select('id', 'name', 'email')->get();
        $products = Products::select('id', 'name', 'price', 'stock')->where('status', 'active')->get();
        $settings = Setting::getSettings();

        return view('admin.orders.create', compact('users', 'products', 'settings'));
    }

    /**
     * Store a newly created order
     */
    public function store(Request $request)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'payment_method' => 'required|in:COD,ONLINE',
            'items' => 'required|array|min:1',
            // 'items.*.product_id' => 'required|exists:products,id',
            // 'items.*.quantity' => 'required|integer|min:1',
            // 'items.*.price' => 'required|numeric|min:0',
        ]);

        try {

            $settingsDiscount = Setting::getSettings()->discount_global;
            DB::beginTransaction();

            $order = Order::create([
                'order_number' => $this->generateOrderNumber(),
                'user_id' => $request->user_id,
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'shipping_address' => $request->shipping_address ?? $request->customer_address,
                'payment_method' => $request->payment_method,
                'payment_status' => 'pending',
                'status' => 'pending',
                'shipping_fee' => $request->shipping_fee ?? 0,
                'notes' => $request->notes,
                'total' => 0,
            ]);

            $total = 0;
            $subtotal = 0;
            $productIds = [];
            $quantities = [];
            $prices = [];

            foreach ($request->items as $item) {
                if (isset($item['product_id'])) {
                    $productIds[] = $item['product_id'];
                } elseif (isset($item['quantity'])) {
                    $quantities[] = $item['quantity'];
                } elseif (isset($item['price'])) {
                    $prices[] = $item['price'];
                }
            }

            // Combine arrays into proper item structure
            for ($i = 0; $i < count($productIds); $i++) {
                $product = Products::find($productIds[$i]);
                $quantity = $quantities[$i] ?? 1;
                $price = $product->flash_sale_price ?? $product->price;
                $subtotalData = $quantity * $price;
                $total += $subtotalData;
                $subtotal += $product->price * $quantity;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $productIds[$i],
                    'product_name' => $product->name,
                    'quantity' => $quantity,
                    'price' => $price,
                    'total' => $subtotalData,
                    'pv_sales' => $product->price
                ]);
                $product->decrement('stock', $quantity);
                if ($product->has_flash_sale && !is_null($product?->flash_sale?->first()?->max_quantity)) {
                    $product->flash_sale()->increment('used_quantity', 1);

                    if ($product->flash_sale?->first()?->used_quantity >= $product->flash_sale?->first()?->max_quantity) {
                        $product->flash_sale()->update(['status' => 'paused']);
                    }
                }
            }
            $discountSystem = $request->payment_method == 'ONLINE' && $settingsDiscount ? ($total * $settingsDiscount / 100) : null;
            $discount = $subtotal != $total ? ($subtotal - $total) : null;
            if ($discountSystem) {
                $total -= $discountSystem;
            }
            $order->update([
                'total' => $total,
                'discount' => $discount,
                'subtotal' => $subtotal,
                'system_discount' => $discountSystem,
                'system_discount_percentage' => $settingsDiscount,
            ]);

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được tạo thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error creating order: ' . $e->getMessage());

            return back()->withInput()
                ->with('error',  $e->getMessage());
        }
    }

    /**
     * Show the form for editing the order
     */
    public function edit(Order $order)
    {
        $order->load(['items.product']);
        $users = User::select('id', 'name', 'email')->get();
        $products = Products::select('id', 'name', 'price', 'stock')->where('status', 'active')->get();

        return view('admin.orders.edit', compact('order', 'users', 'products'));
    }

    /**
     * Update the specified order
     */
    public function update(Request $request, Order $order)
    {
        $request->validate([
            'customer_name' => 'required|string|max:255',
            'customer_email' => 'required|email|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_address' => 'required|string',
            'status' => 'required|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'required|in:pending,paid,failed,refunded',
            'payment_method' => 'required|in:COD,ONLINE',
            'delivery_date' => 'nullable|date|after_or_equal:today',
        ]);

        try {
            $order->update([
                'customer_name' => $request->customer_name,
                'customer_email' => $request->customer_email,
                'customer_phone' => $request->customer_phone,
                'customer_address' => $request->customer_address,
                'shipping_address' => $request->shipping_address,
                'status' => $request->status,
                'payment_status' => $request->payment_status,
                'payment_method' => $request->payment_method,
                'shipping_fee' => $request->shipping_fee ?? 0,
                'notes' => $request->notes,
                'delivery_date' => $request->delivery_date,
            ]);

            // Mark as paid if payment status changed to paid
            if ($request->payment_status === 'paid' && $order->payment_status !== 'paid') {
                $order->update(['paid_at' => Carbon::now(),'payment_status' => 'paid', 'status' => 'confirmed']);
            }

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được cập nhật thành công!');
        } catch (\Exception $e) {
            Log::error('Error updating order: ' . $e->getMessage());

            return back()->withInput()
                ->with('error', 'Có lỗi xảy ra khi cập nhật đơn hàng!');
        }
    }

    /**
     * Remove the specified order
     */
    public function destroy(Order $order)
    {
        try {
            // Only allow deletion of pending/cancelled orders
            if (!in_array($order->status, ['pending', 'cancelled'])) {
                return back()->with('error', 'Chỉ có thể xóa đơn hàng ở trạng thái chờ xử lý hoặc đã hủy!');
            }

            DB::beginTransaction();

            // Restore product stock
            if ($order->status !== 'cancelled') {
                foreach ($order->items as $item) {
                    $product = Products::find($item->product_id);
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                    $product?->flash_sale?->first()?->decrement('used_quantity', 1);
                }
            }
            // Delete order items
            $order->items()->delete();

            // Delete order
            $order->delete();

            DB::commit();

            return redirect()->route('admin.orders.index')
                ->with('success', 'Đơn hàng đã được xóa thành công!');
        } catch (\Exception $e) {
            DB::rollback();
            Log::error('Error deleting order: ' . $e->getMessage());

            return back()->with('error', 'Có lỗi xảy ra khi xóa đơn hàng!');
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|in:pending,confirmed,processing,delivered,completed,cancelled',
        ]);

        $order->update(['status' => $request->status]);
        if ($request->status == 'cancelled') {
            foreach ($order->items as $item) {
                $product = Products::find($item->product_id);
                if ($product) {
                    $product->increment('stock', $item->quantity);
                }
                $product?->flash_sale?->first()?->decrement('used_quantity', 1);
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Trạng thái đơn hàng đã được cập nhật!',
            'status' => $order->status
        ]);
    }

    /**
     * Generate unique order number
     */
    private function generateOrderNumber(): string
    {
        $prefix = 'ORD';
        $date = Carbon::now()->format('Ymd');
        $lastOrder = Order::whereDate('created_at', today())
            ->orderBy('id', 'desc')
            ->first();

        $sequence = $lastOrder ? (int)substr($lastOrder->order_number, -4) + 1 : 1;

        return $prefix . $date . str_pad($sequence, 4, '0', STR_PAD_LEFT);
    }

    public function downloadPDF(Order $order)
    {
        try {
            $order->load(['items.product', 'user']);
            $settings = \App\Models\Setting::getSettings();
            $data = [
                'order' => $order,
                'settings' => $settings,
                'generated_at' => now()->format('d/m/Y H:i:s')
            ];
            $pdf = Pdf::loadView('admin.orders.invoice-pdf', $data);
            $pdf->setPaper('A5', 'portrait');
            $pdf->setOptions([
                'isHtml5ParserEnabled' => true,
                'isPhpEnabled' => true,
                'defaultFont' => 'DejaVu Sans',
                'enable_remote' => true
            ]);
            $filename = 'hoa-don-' . $order->order_number . '.pdf';
            return $pdf->download($filename);
        } catch (\Exception $e) {
            \Log::error('PDF Generation Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi tạo file PDF: ' . $e->getMessage());
        }
    }

    public function exportExcel(Request $request)
    {
        try {
            // Get filters from request
            $filters = $request->only([
                'search',
                'status',
                'payment_status',
                'date_from',
                'date_to'
            ]);

            // Remove empty filters
            $filters = array_filter($filters, function ($value) {
                return !is_null($value) && $value !== '';
            });

            // Generate filename with current date and filters
            $filename = 'don-hang-' . now()->format('Y-m-d-H-i-s');

            // Add filter info to filename if any
            if (!empty($filters['status'])) {
                $filename .= '-' . $filters['status'];
            }
            if (!empty($filters['payment_status'])) {
                $filename .= '-' . $filters['payment_status'];
            }
            if (!empty($filters['date_from']) && !empty($filters['date_to'])) {
                $filename .= '-' . $filters['date_from'] . '-to-' . $filters['date_to'];
            }

            $filename .= '.xlsx';

            // Export Excel file
            return Excel::download(new OrdersExport($filters), $filename);
        } catch (\Exception $e) {
            \Log::error('Excel Export Error: ' . $e->getMessage());

            return redirect()->back()->with('error', 'Có lỗi xảy ra khi xuất file Excel: ' . $e->getMessage());
        }
    }
}
