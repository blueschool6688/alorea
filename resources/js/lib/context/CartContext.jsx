import { cartService } from '@/lib/services/CartService';
import { message } from 'antd';
import { createContext, useCallback, useContext, useEffect, useState } from 'react';

export const CartContext = createContext(null);

export const CartProvider = ({ children }) => {
    const [cart, setCart] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    useEffect(() => {
        setLoading(true);
        cartService.getCart()
            .then((res) => {
                console.log('📥 Initial cart load:', res);
                setCart(res);
            })
            .catch((err) => {
                const errorMessage = err.message || 'Lỗi tải giỏ hàng';
                setError(errorMessage);
                message.error(errorMessage);
            })
            .finally(() => setLoading(false));
    }, []);

    const addToCart = useCallback(async (product, quantity = 1) => {
        setLoading(true);
        setError(null);
        try {
            let res;
            res = await cartService.addToCartLocal(product, quantity);
            setCart(res);
            message.success(`Đã thêm ${product.name} vào giỏ hàng`);
            return res;
        } catch (err) {
            console.error('❌ AddToCart error:', err);
            const errorMessage = err.message || 'Lỗi thêm vào giỏ hàng';
            setError(errorMessage);
            message.error(errorMessage);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const updateCartItem = useCallback(async (id, quantity) => {
        setLoading(true);
        setError(null);
        try {
            const res = await cartService.updateCartItem(id, quantity);
            console.log('🔄 UpdateCartItem result:', res);
            setCart(res);
            return res;
        } catch (err) {
            const errorMessage = err.message || 'Lỗi cập nhật giỏ hàng';
            setError(errorMessage);
            message.error(errorMessage);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const removeCartItem = useCallback(async (id) => {
        setLoading(true);
        setError(null);
        try {
            const res = await cartService.removeCartItem(id);
            console.log('🗑️ RemoveCartItem result:', res);
            setCart(res);
            message.success('Đã xóa sản phẩm khỏi giỏ hàng');
            return res;
        } catch (err) {
            const errorMessage = err.message || 'Lỗi xóa sản phẩm';
            setError(errorMessage);
            message.error(errorMessage);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const clearCart = useCallback(async () => {
        setLoading(true);
        setError(null);
        try {
            const res = await cartService.clearCart();
            setCart(res);
            // message.success('Đã xóa toàn bộ giỏ hàng');
            return res;
        } catch (err) {
            const errorMessage = err.message || 'Lỗi xóa giỏ hàng';
            setError(errorMessage);
            message.error(errorMessage);
            throw err;
        } finally {
            setLoading(false);
        }
    }, []);

    const getItemQuantity = useCallback((productId) => {
        const items = cart?.data?.items || cart?.items || [];
        // Đối với local cart: item.id = product_id
        // Đối với server cart: item.product_id = product_id
        const cartItem = items.find((item) => {
            const itemProductId = item.product_id || item.id;
            return itemProductId === productId;
        });
        const quantity = cartItem?.quantity || 0;
        console.log(`🔍 getItemQuantity(${productId}):`, quantity, 'found cartItem:', cartItem);
        return quantity;
    }, [cart]);

    const isItemInCart = useCallback((productId) => {
        const inCart = getItemQuantity(productId) > 0;
        console.log(`🛒 isItemInCart(${productId}):`, inCart);
        return inCart;
    }, [getItemQuantity]);

    const getCartItemId = useCallback((productId) => {
        const items = cart?.data?.items || cart?.items || [];
        const cartItem = items.find((item) => {
            const itemProductId = item.product_id || item.id;
            return itemProductId === productId;
        });
        // Với local cart: cart item id chính là product id (item.id)
        // Với server cart: sử dụng cart_item_id thực tế nếu có
        const cartItemId = cartItem?.cart_item_id || cartItem?.id || null;
        console.log(`🆔 getCartItemId(${productId}):`, cartItemId, 'found cartItem:', cartItem);
        return cartItemId;
    }, [cart]);

    const getProductIds = useCallback(() => {
        const items = cart?.data?.items || cart?.items || [];
        const productIds = items.map(item => item.product_id || item.id);
        return productIds;
    }, [cart]);


    // Calculated values
    const cartCount = cart?.data?.items?.reduce((total, item) => total + item.quantity, 0) ||
        cart?.items?.reduce((total, item) => total + item.quantity, 0) || 0;

    const subtotal = cart?.data?.items?.reduce((total, item) => total + (item.price * item.quantity), 0) ||
        cart?.items?.reduce((total, item) => total + (item.price * item.quantity), 0) || 0;

    const value = {
        cart,
        loading,
        error,
        cartCount,
        subtotal,
        addToCart,
        updateCartItem,
        removeCartItem,
        clearCart,
        setCart,
        getItemQuantity,
        isItemInCart,
        getCartItemId,
        getProductIds
    };

    return (
        <CartContext.Provider value={value}>
            {children}
        </CartContext.Provider>
    );
};

// Custom hook to use cart context
export const useCart = () => {
    const context = useContext(CartContext);
    if (!context) {
        throw new Error('useCart must be used within a CartProvider');
    }
    return context;
};
