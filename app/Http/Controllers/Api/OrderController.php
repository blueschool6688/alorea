<?php

namespace App\Http\Controllers\Api;

use App\Models\Order;
use App\Models\Products;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class OrderController extends ApiController
{

    public function storeOrder(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'province' => 'required|string',
            'ward' => 'required|string',
            'user_id' => 'nullable|exists:users,id',
            'note' => 'nullable|string',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'payment_method' => 'required|in:cod,online',
            'subtotal' => 'required|numeric|min:0',
            'shipping_fee' => 'nullable|numeric|min:0',
            'flash_sale_discount' => 'nullable|numeric|min:0',
            'original_subtotal' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'address' => "required|string|max:500",
            'system_discount' => 'nullable',
            'system_discount_percentage' => 'nullable',
            'items' => 'required',
        ]);

        DB::beginTransaction();
        try {
            $order = Order::create([
                'order_number' => 'ORD-' . strtoupper(uniqid()),
                'province_id' => $validated['province'],
                'user_id' => $validated['user_id'],
                'ward_id' => $validated['ward'],
                'notes' => $validated['note'] ?? null,
                'customer_name' => $request->name,
                'customer_email' => $request->email,
                'customer_address' => $validated['address'],
                'customer_phone' => $validated['phone'],
                'payment_method' => $validated['payment_method'],
                'discount' => isset($validated['flash_sale_discount']) ? $validated['flash_sale_discount'] : null,
                'subtotal' => isset($validated['original_subtotal']) ? $validated['original_subtotal'] : $validated['total'],
                'payment_status' => 'pending',
                'system_discount' => $validated['system_discount'],
                'system_discount_percentage' => $validated['system_discount_percentage'],
                'shipping_address' => $validated['address'],
                'shipping_fee' => $validated['shipping_fee'],
                'total' => $validated['total'],
                'status' => 'pending',
            ]);
            $items = $validated['items'];
            foreach ($items as $item) {
                $product = Products::find($item['id']);
                if ($product->stock != 0 && $product->stock >= $item['quantity']) {
                    $order->items()->create([
                        'product_id' => $item['id'],
                        'product_name' => $item['name'],
                        'quantity' => $item['quantity'],
                        'total' => $item['final_price'] * $item['quantity'],
                        'price' => $item['final_price'],
                        'pv_sales' => $item['price'] ?? null
                    ]);
                    $product->decrement('stock', $item['quantity']);
                    if ($product->has_flash_sale && !is_null($product?->flash_sale?->first()?->max_quantity)) {
                        $product->flash_sale()->increment('used_quantity', 1);

                        if ($product->flash_sale?->first()?->used_quantity >= $product->flash_sale?->first()?->max_quantity) {
                            $product->flash_sale()->update(['status' => 'paused']);
                        }
                    }
                } else {
                    DB::rollBack();
                    return $this->responseUnprocess('Sản phẩm ' . $item['name'] . ' is out of stock or insufficient stock');
                }
            }
            DB::commit();
            return $this->sendApiResponse($order, 'Order created successfully');
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->responseServerError('Order creation failed', $th->getMessage());
        }
    }

    public function getOrder(Request $request, $orderId)
    {
        $user = $request->user();
        $orderNumber = str_starts_with($orderId, 'ORD-') ? $orderId : 'ORD-' . $orderId;
        $order = $user->orders()->with(['items' => function ($query) {
            $query->with('product:id');
        }, 'provinces', 'ward'])->where('order_number', $orderNumber)->first();
        if (!$order) {
            return $this->responseNotFound('Order not found');
        }
        $order->payment_url = 'https://qr.sepay.vn/img?acc=' . env('BANK_ACCOUNT_NUMBER') . '&bank=' . env('BANK_NAME') . '&amount=' . $order->total . '&des=DH+' . $order->id;
        return $this->sendApiResponse($order, 'Order retrieved successfully');
    }

    public function getGuestOrder($orderId)
    {
        $orderNumber = str_starts_with($orderId, 'ORD-') ? $orderId : 'ORD-' . $orderId;
        $order = Order::whereNull('user_id')->where('order_number', $orderNumber)->with(['items' => function ($query) {
            $query->with('product:id');
        }, 'provinces', 'ward'])->first();
        if (!$order) {
            return $this->responseNotFound('Order not found or email does not match');
        }
        $order->payment_url = 'https://qr.sepay.vn/img?acc=' . env('BANK_ACCOUNT_NUMBER') . '&bank=' . env('BANK_NAME') . '&amount=' . $order->total . '&des=DH+' . $order->id;
        return $this->sendApiResponse($order, 'Order retrieved successfully');
    }

    public function getOrders(Request $request)
    {
        $limit = $request->input('per_page', 10);
        $search = $request->search;
        $status = $request->status;
        $startDate = $request->startDate;
        $endDate = $request->endDate;


        $user = $request->user();
        $query = $user->orders()->with(['items', 'provinces', 'ward'])->orderBy('created_at', 'desc');

        if (!blank($search)) {
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', '%' . $search . '%')
                    ->orWhere('customer_name', 'like', '%' . $search . '%')
                    ->orWhere('customer_email', 'like', '%' . $search . '%')
                    ->orWhere('customer_phone', 'like', '%' . $search . '%');
            });
        }

        if ($status) {
            $query->where('status', $status);
        }

        if ($startDate && $endDate) {
            $query->whereDate('created_at', '>=', $startDate)
                ->whereDate('created_at', '<=', $endDate);
        } elseif ($startDate) {
            $query->whereDate('created_at', '>=', $startDate);
        } elseif ($endDate) {
            $query->whereDate('created_at', '<=', $endDate);
        }


        $orders = $query->paginate($limit);

        return $this->sendApiResponse($orders, 'Orders retrieved successfully');
    }


    public function webhookSepay(Request $request)
    {
        \Log::info('Webhook received');
        $webhookData = $request->all();
        $logMessage = 'Sepay ' . "\n" . json_encode($webhookData, JSON_PRETTY_PRINT) . "\n------------------------------\n";
        $filePath = storage_path('logs/sepay.txt');
        File::append($filePath, $logMessage);
        \Log::info($logMessage);
        $transferAmount = $webhookData['transferAmount'] ?? 0;
        $transactionId = $webhookData['id'] ?? null;
        try {
            DB::beginTransaction();
            $order_id = \Str::of($webhookData['description'])
                ->match('/DH(\d+)/');
            \Log::info($order_id);
            $order = Order::find($order_id);
            if (!$order) {
                throw new \Exception('Order not found');
            }
            $order->update([
                'payment_status' => 'paid',
                'status' => 'confirmed',
                'paid_at' => now()
            ]);

            Transaction::addLog([
                'order_id' => $order->id,
                'transaction_no' => $transactionId,
                'amount' => $transferAmount,
                'payment_method' => 'sepay',
                'type' => 'credit',
                'payload' => json_encode($webhookData),
            ]);
            \Log::info('Webhook completed ' . $order->id . '-' . $order->order_number);
            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();
            \Log::error($exception->getMessage());
        }
    }

    public function paymentStatus(Request $request, Order $order)
    {
        // $user = $request->user();
        if (!$order) {
            return $this->responseNotFound('Order not found');
        }
        // if ($order->user_id != $user->id) {
        //     return $this->responseUnprocess('You do not have permission to access this order');
        // }
        if ($order->payment_status == 'paid' && $order->paid_at != null && $order->status == 'confirmed') {
            return $this->sendApiResponse(['payment_status' => $order->payment_status], 'Payment already completed');
        }
        return $this->sendApiResponse(['payment_status' => 'pending'], 'Payment status retrieved');
    }
}
