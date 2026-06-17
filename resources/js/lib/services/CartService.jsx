import axios from 'axios';

const CART_LOCAL_KEY = 'alorea_cart_data';

function getToken() {
    return localStorage.getItem('token');
}

function isLoggedIn() {
    return !!getToken();
}

// Lấy giỏ hàng từ localStorage
function getLocalCart() {
    const data = localStorage.getItem(CART_LOCAL_KEY);
    if (!data) {
        return {
            data: {
                id: null,
                user_id: null,
                items: [],
                total: 0,
            },
        };
    }
    try {
        return JSON.parse(data);
    } catch {
        return {
            data: {
                id: null,
                user_id: null,
                items: [],
                total: 0,
            },
        };
    }
}

// Lưu giỏ hàng vào localStorage
function saveLocalCart(cart) {
    localStorage.setItem(CART_LOCAL_KEY, JSON.stringify(cart));
}

export const cartService = {
    // Lấy giỏ hàng hiện tại
    async getCart() {
        if (isLoggedIn()) {
            try {
                const res = await axios.get('/api/user/cart', {
                    headers: { Authorization: `Bearer ${getToken()}` },
                });
                return res.data;
            } catch (error) {
                console.error('Failed to fetch cart from server:', error);
                // Fallback to local cart if server request fails
                return getLocalCart();
            }
        } else {
            return getLocalCart();
        }
    },

    // Thêm sản phẩm vào giỏ hàng (server hoặc local)
    async addToCart(id, quantity) {
        if (isLoggedIn()) {
            try {
                const res = await axios.post(
                    '/api/user/cart/add',
                    { product_id: id, quantity },
                    { headers: { Authorization: `Bearer ${getToken()}` } }
                );
                return res.data;
            } catch (error) {
                console.error('Failed to add to server cart:', error);
                throw new Error('Không thể thêm sản phẩm vào giỏ hàng');
            }
        } else {
            // local cart
            let cart = getLocalCart();
            let item = cart.data.items.find((i) => i.id === id);
            if (item) {
                item.quantity += quantity;
            } else {
                // Lấy thông tin sản phẩm từ productService (giả định đã load sẵn products ở client)
                // Nên truyền product vào hàm này nếu dùng local
                throw new Error('Product info required for local cart. Use addToCartLocal instead.');
            }
            cart.data.total = cart.data.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            saveLocalCart(cart);
            return cart;
        }
    },

    // Thêm sản phẩm vào giỏ hàng local (cần truyền đủ thông tin product)
    async addToCartLocal(product, quantity) {
        let cart = getLocalCart();
        let item = cart.data.items.find((i) => i.id === product.id);

        // Kiểm tra stock trước khi thêm
        const currentQuantityInCart = item ? item.quantity : 0;
        const newTotalQuantity = currentQuantityInCart + quantity;
        const availableStock = product.stock || 0;

        if (availableStock > 0 && newTotalQuantity > availableStock) {
            throw new Error(`Vượt quá số lượng stock, còn ${availableStock} sản phẩm trong kho.`);
        }

        if (item) {
            // Cập nhật số lượng với kiểm tra stock
            item.quantity = newTotalQuantity;
        } else {
            cart.data.items.push({
                id: product.id,
                name: product.name,
                stock: product.stock || 0,
                subtitle: product.subtitle || '',
                price: product.price,
                discount_price: product?.flash_sale_price,
                has_discount: product?.has_flash_sale,
                flash_sale_discount: product?.flash_sale_discount,
                final_price: product?.has_flash_sale ? product?.flash_sale_price : product.price,
                quantity,
                image: product.image || '',
                size: product.size || '50ml',
                category: product.category || 'Nước hoa',
            });
        }

        // Tính tổng tiền dựa trên final_price thay vì price
        cart.data.total = cart.data.items.reduce((sum, i) => {
            const itemPrice = i.has_discount && i.discount_price ? i.discount_price : i.price;
            return sum + itemPrice * i.quantity;
        }, 0);

        saveLocalCart(cart);
        return cart;
    },

    // Cập nhật số lượng sản phẩm trong giỏ hàng
    async updateCartItem(id, quantity) {
        if (isLoggedIn()) {
            try {
                const res = await axios.put(
                    '/api/user/cart/update',
                    { cart_item_id: id, quantity },
                    { headers: { Authorization: `Bearer ${getToken()}` } }
                );
                return res.data;
            } catch (error) {
                console.error('Failed to update cart item:', error);
                throw new Error('Không thể cập nhật giỏ hàng');
            }
        } else {
            let cart = getLocalCart();
            let item = cart.data.items.find((i) => i.id === id);
            if (item) {
                if (quantity <= 0) {
                    // Remove item if quantity is 0 or negative
                    cart.data.items = cart.data.items.filter((i) => i.id !== id);
                } else {
                    item.quantity = quantity;
                }
            }
            cart.data.items = cart.data.items.filter((i) => i.quantity > 0);
            cart.data.total = cart.data.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            saveLocalCart(cart);
            return cart;
        }
    },

    // Xóa một sản phẩm khỏi giỏ hàng
    async removeCartItem(id) {
        if (isLoggedIn()) {
            try {
                const res = await axios.delete('/api/user/cart/remove', {
                    headers: { Authorization: `Bearer ${getToken()}` },
                    data: { cart_item_id: id },
                });
                return res.data;
            } catch (error) {
                console.error('Failed to remove cart item:', error);
                throw new Error('Không thể xóa sản phẩm khỏi giỏ hàng');
            }
        } else {
            let cart = getLocalCart();
            cart.data.items = cart.data.items.filter((i) => i.id !== id);
            cart.data.total = cart.data.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            saveLocalCart(cart);
            return cart;
        }
    },

    // Xóa toàn bộ giỏ hàng
    async clearCart() {
        if (isLoggedIn()) {
            try {
                const res = await axios.delete('/api/user/cart/clear', {
                    headers: { Authorization: `Bearer ${getToken()}` },
                });
                return res.data;
            } catch (error) {
                console.error('Failed to clear cart:', error);
                throw new Error('Không thể xóa giỏ hàng');
            }
        } else {
            const cart = {
                data: {
                    id: null,
                    user_id: null,
                    items: [],
                    total: 0,
                },
            };
            saveLocalCart(cart);
            return cart;
        }
    },

    // Merge cart khi đăng nhập
    async mergeCart(items) {
        if (isLoggedIn()) {
            try {
                const res = await axios.post(
                    '/api/user/cart/merge',
                    { items },
                    { headers: { Authorization: `Bearer ${getToken()}` } }
                );
                return res.data;
            } catch (error) {
                console.error('Failed to merge cart:', error);
                throw new Error('Không thể đồng bộ giỏ hàng');
            }
        } else {
            // local: merge items vào cart hiện tại
            let cart = getLocalCart();
            items.forEach((item) => {
                let exist = cart.data.items.find((i) => i.id === item.id);
                if (exist) {
                    exist.quantity += item.quantity;
                } else {
                    // Không có thông tin product, bỏ qua
                    console.warn(`Cannot merge item ${item.id} - missing product info`);
                }
            });
            cart.data.total = cart.data.items.reduce((sum, i) => sum + i.price * i.quantity, 0);
            saveLocalCart(cart);
            return cart;
        }
    },

    // Sync local cart to server when user logs in
    async syncLocalCartToServer() {
        const localCart = getLocalCart();
        if (localCart.data.items.length > 0 && isLoggedIn()) {
            try {
                const items = localCart.data.items.map(item => ({
                    id: item.id,
                    quantity: item.quantity
                }));

                const serverCart = await this.mergeCart(items);

                // Clear local cart after successful sync
                localStorage.removeItem(CART_LOCAL_KEY);

                return serverCart;
            } catch (error) {
                console.error('Failed to sync local cart to server:', error);
                return localCart;
            }
        }
        return await this.getCart();
    },

    // Get cart summary (count, total, savings)
    getCartSummary(cart) {
        const items = cart?.data?.items || cart?.items || [];
        const count = items.reduce((total, item) => total + item.quantity, 0);
        const subtotal = items.reduce((total, item) => total + (item.price * item.quantity), 0);
        const savings = items.reduce((total, item) => {
            if (item.originalPrice) {
                return total + ((item.originalPrice - item.price) * item.quantity);
            }
            return total;
        }, 0);

        // Calculate shipping fee (free shipping if total >= 1,000,000 VND or all items have free shipping)
        const hasAllFreeShipping = items.every(item => item.freeShipping);
        const shippingFee = (subtotal >= 1000000 || hasAllFreeShipping) ? 0 : 50000;

        const total = subtotal + shippingFee;

        return {
            count,
            subtotal,
            savings,
            shippingFee,
            total,
            isEmpty: count === 0
        };
    },

    // Check if product is in cart
    isProductInCart(cart, productId) {
        const items = cart?.data?.items || cart?.items || [];
        return items.some(item => item.id === productId);
    },

    // Get product quantity in cart
    getProductQuantity(cart, productId) {
        const items = cart?.data?.items || cart?.items || [];
        const cartItem = items.find(item => item.id === productId);
        return cartItem ? cartItem.quantity : 0;
    },

    // Get cart item by product ID
    getCartItem(cart, productId) {
        const items = cart?.data?.items || cart?.items || [];
        return items.find(item => item.id === productId) || null;
    },

    // Format price to VND currency
    formatPrice(price) {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    },

    // Apply promo code (example implementation)
    applyPromoCode(cart, promoCode) {
        const summary = this.getCartSummary(cart);
        let discount = 0;

        switch (promoCode?.toUpperCase()) {
            case 'ALOREA10':
                discount = Math.min(summary.subtotal * 0.1, 200000); // 10% max 200k
                break;
            case 'WELCOME20':
                discount = Math.min(summary.subtotal * 0.2, 500000); // 20% max 500k
                break;
            case 'FREESHIP':
                discount = summary.shippingFee; // Free shipping
                break;
            default:
                discount = 0;
        }

        return {
            ...summary,
            discount,
            total: summary.total - discount,
            promoCode: discount > 0 ? promoCode : null
        };
    }
};
