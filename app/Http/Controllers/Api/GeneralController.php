<?php

namespace App\Http\Controllers\Api;

use App\Models\Blog;
use App\Models\Categories;
use App\Models\Collections;
use App\Models\FlashSale;
use App\Models\Order;
use App\Models\Products;
use App\Models\Scent;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class GeneralController extends ApiController
{



    public function getGeneral()
    {
        return $this->sendApiResponse(Setting::getSettings(), 'General application info');
    }

    public function getDataHomepage()
    {
        $productCategory = Categories::where('status', 'active')
            ->where('is_featured', true)->with(['products' => function ($query) {
                $query->where('status', 'active')->where('stock', '>', 0)->take(3);
            }])->get();

        $collections = Collections::get(['id', 'title']);

        $blogs = Blog::where('status', 'published')->latest()->take(4)->get(['id', 'title', 'slug', 'featured_image', 'published_at', 'created_at', 'excerpt', 'reading_time', 'views_count', 'author_name']);

        $flashSales = FlashSale::where('status', 'active')
            ->whereDate('start_time', '<=', now())
            ->where(function ($query) {
                $query->whereDate('end_time', '>=', now())
                    ->orWhereNull('end_time');
            })
            ->where(function ($query) {
                $query->whereNull('max_quantity')
                    ->orWhere(function ($subQuery) {
                        $subQuery->whereNotNull('max_quantity')
                            ->where('used_quantity', '<', \DB::raw('max_quantity'));
                    });
            })
            ->get();


        $data = [
            'productCategory' => $productCategory,
            'collections' => $collections,
            'blogs' => $blogs,
            'flashSales' => $flashSales,
        ];

        return $this->sendApiResponse($data, 'Homepage data');
    }

    public function getCollection()
    {
        $collections = Collections::with(['products' => function ($query) {
            $query
                ->select(['id', 'name', 'price', 'slug', 'short_description', 'stock'])
                ->where('status', 'active')->where('stock', '>', 0)->take(3);
        }])->get();
        return $this->sendApiResponse($collections, 'Product collections');
    }

    public function detailProduct($slug)
    {
        $product = Products::where('slug', $slug)->with(['category', 'reviews'])->first();

        if (!$product) {
            return $this->responseNotFound(message: 'Product not found');
        }

        return $this->sendApiResponse($product, 'Product details');
    }

    public function submitReview(Request $request, Products $product)
    {
        $user = $request->user();

        $validator = Validator::make($request->all(), [
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess($validator->errors()->first());
        }

        $data = [
            'user_id' =>  $user->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
            'name' => $user->name
        ];

        $review = $product->reviews()->create([
            ...$data,
            'status' => 'approved', // case tạm
        ]);
        if ($review) {
            $product->updateRating();
            $review->load('user:id,name');
        }
        return $this->sendApiResponse($review, 'Review submitted successfully and is pending approval.');
    }


    public function getProductsData(Request $request)
    {
        $filterType = Products::getTypeData();
        $dataScrent = Scent::where('is_active', true)->get(['id', 'name']);

        $gender = $request->gender;
        $scrent = $request->scent;
        $sort = $request->query('sort', 'created_at:desc');
        $min_price = $request->query('min_price');
        $max_price = $request->query('max_price');

        $limit = $request->query('limit', 10);

        $filter = [
            'gender' => collect($filterType),
            'scents' => $dataScrent
        ];
        $query = Products::with('category')
            ->where('stock', '>', 0)
            ->where('status', 'active');

        if ($gender) {
            $query->whereIn('type', (array)$gender);
        }
        if ($scrent) {
            $query->whereHas('scrents', function ($q) use ($scrent) {
                $q->whereIn('id', (array)$scrent);
            });
        }
        if ($min_price) {
            $query->where('price', '>=', (float)$min_price);
        }
        if ($max_price) {
            $query->where('price', '<=', (float)$max_price);
        }
        if ($sort) {
            $sortParts = explode(':', $sort);
            $sortField = $sortParts[0] ?? 'created_at';
            $sortDirection = $sortParts[1] ?? 'desc';
            $allowedSortFields = ['created_at', 'price', 'name', 'rating'];
            $allowedDirections = ['asc', 'desc'];

            if (in_array($sortField, $allowedSortFields) && in_array($sortDirection, $allowedDirections)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }
        $products = $query->paginate($limit);
        return $this->sendApiResponse([
            'filters' => $filter,
            'products' => $products
        ], 'Product data retrieved successfully.');
    }

    public function getBlogs(Request $request)
    {
        $search = $request->query('search');
        $sort = $request->query('sort', 'created_at:desc');
        $limit = $request->query('limit', 10);
        $query = Blog::where('status', 'published');
        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%")
                    ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }
        if ($sort) {
            $sortParts = explode(':', $sort);
            $sortField = $sortParts[0] ?? 'created_at';
            $sortDirection = $sortParts[1] ?? 'desc';
            $allowedSortFields = ['created_at', 'views_count', 'likes_count', 'title', 'published_at'];
            $allowedDirections = ['asc', 'desc'];

            if (in_array($sortField, $allowedSortFields) && in_array($sortDirection, $allowedDirections)) {
                $query->orderBy($sortField, $sortDirection);
            } else {
                $query->orderBy('created_at', 'desc');
            }
        } else {
            $query->orderBy('created_at', 'desc');
        }


        $blogs = $query->latest()->paginate($limit, ['id', 'title', 'is_featured', 'slug', 'featured_image', 'published_at', 'created_at', 'excerpt', 'reading_time', 'views_count', 'author_name']);
        return $this->sendApiResponse($blogs, 'Blog posts retrieved successfully.');
    }


    public function getBlogsDetail($slug)
    {
        $blog = Blog::where('slug', $slug)->where('status', 'published')->first();

        if (!$blog) {
            return $this->responseNotFound(message: 'Blog post not found');
        }
        $blog->increment('views_count');
        $relatedBlogs = Blog::where('status', 'published')
            ->where('id', '!=', $blog->id)
            ->latest()
            ->take(3)
            ->get(['id', 'title', 'slug', 'featured_image', 'published_at', 'created_at', 'excerpt', 'reading_time', 'views_count', 'author_name']);
        return $this->sendApiResponse([
            'blog' => $blog,
            'relatedBlogs' => $relatedBlogs
        ], 'Blog post details retrieved successfully.');
    }



    public function sendContact(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:255',
        ]);

        if ($validator->fails()) {
            return $this->responseUnprocess($validator->errors()->first());
        }

        $data = $validator->validated();
        $data['subject'] = 'Đăng ký để nhận ưu đãi 10% cho đơn hàng đầu tiên';
        \App\Models\Contact::create($data);

        return $this->sendApiResponse(null, 'Your message has been sent successfully. We will get back to you soon.');
    }


    public function checkFlashSale(Request $request)
    {
        try {
            $cartItems = json_decode($request->query('cart', '[]'), true)['data'];
            $paymentType = $request->query('type', 'cod'); // Mặc định là COD nếu không được chỉ định
            $productIds = collect($cartItems['items'])->pluck('id')->unique();

            // Lấy discount_global từ settings, nhưng chỉ áp dụng nếu type là "online"
            $settings = Setting::getSettings();
            $globalDiscountPercentage = 0;
            if ($paymentType === 'online' && isset($settings->discount_global)) {
                $globalDiscountPercentage = floatval($settings->discount_global);
            }

            $products = Products::whereIn('id', $productIds)
                ->where('status', 'active')
                ->get()
                ->keyBy('id');
            $currentTime = now();
            $activeFlashSales = FlashSale::where('status', 'active')
                ->where('start_time', '<=', $currentTime)
                ->where('end_time', '>=', $currentTime)
                ->with('products')
                ->get();


            $flashSaleData = [];
            $totalDiscount = 0;
            $validatedCartItems = [];

            foreach ($cartItems['items'] as $cartItem) {
                $productId = $cartItem['id'];
                $quantity = $cartItem['quantity'];
                $product = $products->get($productId);

                if (!$product) {
                    continue;
                }

                $originalPrice = $product->price;
                $finalPrice = $originalPrice;
                $flashSaleInfo = null;
                $discountAmount = 0;
                $globalDiscountAmount = 0;
                $applicableMaxQuantity = null;

                // Check if product is in any active flash sale
                foreach ($activeFlashSales as $flashSale) {
                    $isEligible = false;

                    if ($flashSale->type_all) {
                        $isEligible = true;
                    } elseif ($flashSale->products && $flashSale->products->contains('id', $productId)) {
                        $isEligible = true;
                    }

                    if ($isEligible) {
                        $discountAmount = ((int)$originalPrice * (int)$flashSale->discount_percentage) / 100;

                        if ($flashSale->max_discount_amount && $discountAmount > $flashSale->max_discount_amount) {
                            $discountAmount = (int)$flashSale->max_discount_amount;
                        }
                        $finalPrice = $originalPrice - $discountAmount;
                        $finalPrice = max($finalPrice, 0);
                        $applicableMaxQuantity = $flashSale->max_quantity;

                        $flashSaleInfo = [
                            'flash_sale_id' => $flashSale->id,
                            'flash_sale_name' => $flashSale->name,
                            'sale_price' => $finalPrice,
                            'original_price' => $originalPrice,
                            'discount_amount' => $discountAmount,
                            'discount_percentage' => $flashSale->discount_percentage,
                            'actual_discount_percentage' => $originalPrice > 0 ? round(($discountAmount / $originalPrice) * 100, 2) : 0,
                            'max_discount_amount' => $flashSale->max_discount_amount,
                            'max_quantity' => $flashSale->max_quantity,
                            'start_time' => $flashSale->start_time->toISOString(),
                            'end_time' => $flashSale->end_time->toISOString(),
                        ];
                        $flashSaleData[$productId] = $flashSaleInfo;
                        $applicableQuantity = $quantity;
                        if ($flashSale->max_quantity && $quantity > $flashSale->max_quantity) {
                            $applicableQuantity = $flashSale->max_quantity;
                        }

                        $totalDiscount += $discountAmount * $applicableQuantity;

                        break;
                    }
                }

                // Áp dụng thêm discount_global (%) cho giá sau khi đã tính flash sale (chỉ khi type=online)
                if ($paymentType === 'online' && $globalDiscountPercentage > 0) {
                    $globalDiscountAmount = ($finalPrice * $globalDiscountPercentage) / 100;
                    $finalPrice = $finalPrice - $globalDiscountAmount;
                    $finalPrice = max($finalPrice, 0);
                    $totalDiscount += $globalDiscountAmount * $quantity;
                }

                // Add to validated cart items
                $validatedCartItems[] = [
                    'product_id' => $productId,
                    'quantity' => $quantity,
                    'name' => $product->name,
                    'original_price' => $originalPrice,
                    'final_price' => $finalPrice,
                    'discount_amount_per_item' => $discountAmount + $globalDiscountAmount,
                    'flash_sale_discount' => $discountAmount,
                    'global_discount' => $globalDiscountAmount,
                    'total_discount_for_item' => ($discountAmount * min($quantity, $applicableMaxQuantity ?? $quantity))
                        + ($globalDiscountAmount * $quantity),
                    'has_flash_sale' => !is_null($flashSaleInfo),
                    'flash_sale_info' => $flashSaleInfo,
                    'global_discount_percentage' => $paymentType === 'online' ? $globalDiscountPercentage : 0,
                    'payment_type' => $paymentType
                ];
            }

            // Calculate totals
            $originalSubtotal = collect($validatedCartItems)->sum(function ($item) {
                return $item['original_price'] * $item['quantity'];
            });

            $finalSubtotal = collect($validatedCartItems)->sum(function ($item) {
                return $item['final_price'] * $item['quantity'];
            });


            $totalGlobalDiscount = collect($validatedCartItems)->sum(function ($item) {
                return $item['global_discount'] * $item['quantity'];
            });

            $totalFlashSaleDiscount = collect($validatedCartItems)->sum(function ($item) {
                $maxQuantity = isset($item['flash_sale_info']['max_quantity']) ? $item['flash_sale_info']['max_quantity'] : $item['quantity'];
                return $item['flash_sale_discount'] * min($item['quantity'], $maxQuantity);
            });
            $response = [
                'flash_sales' => $flashSaleData,
                'total_discount' => $totalDiscount,
                'total_global_discount' => $totalGlobalDiscount,
                'total_flash_sale_discount' => $totalFlashSaleDiscount,
                'original_subtotal' => $originalSubtotal,
                'final_subtotal' => $finalSubtotal,
                'global_discount_percentage' => $paymentType === 'online' ? $globalDiscountPercentage : 0,
                'payment_type' => $paymentType,
                'validated_cart' => [
                    'items' => $validatedCartItems,
                    'total_items' => collect($validatedCartItems)->sum('quantity'),
                    'has_flash_sale_items' => !empty($flashSaleData)
                ],
                'active_flash_sales_count' => $activeFlashSales->count(),
                'checked_at' => $currentTime->toISOString()
            ];

            return $this->sendApiResponse($response, 'Flash sale check completed successfully.');
        } catch (\Throwable $th) {
            return $this->responseUnprocess($th->getMessage(), ' ' . $th->getLine());
        }
    }


    public function searchOrder(Request $request)
    {
        $email = $request->query('email');
        $orderNumber = $request->query('order_number') ?
            (str_starts_with($request->query('order_number'), 'ORD-') ? $request->query('order_number') : 'ORD-' . $request->query('order_number'))
            : null;


        if (!$orderNumber && !$email) {
            return $this->responseUnprocess('Please provide at least order_number or email to search.');
        }

        $query = Order::with('items.product')->whereNull('user_id');

        if ($orderNumber) {
            $query->where('order_number', $orderNumber);
        }

        if ($email) {
            $query->where('customer_email', $email);
        }

        $order = $query->get();

        if ($order->isEmpty()) {
            return $this->responseNotFound('Order not found with the provided details.');
        }

        return $this->sendApiResponse($order, 'Order retrieved successfully.');
    }
}
