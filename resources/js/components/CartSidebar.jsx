import { useCart } from '@/lib/context/CartContext';
import {
    CloseOutlined,
    DeleteOutlined,
    MinusOutlined,
    PlusOutlined,
    ShoppingOutlined,
    TagOutlined
} from '@ant-design/icons';
import {
    Button,
    Divider,
    Drawer,
    Empty,
    InputNumber,
    Popconfirm,
    Space,
    Spin,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from "react-router-dom";

const { Title, Text, Paragraph } = Typography;

export const CartSidebar = ({ open, onClose }) => {
    const { cart, loading, updateCartItem, removeCartItem, clearCart } = useCart();
    const navigate = useNavigate();
    const [isMobile, setIsMobile] = useState(window.innerWidth < 768);

    useEffect(() => {
        const handleResize = () => setIsMobile(window.innerWidth < 768);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    const handleChangeQty = (id, qty) => {
        if (qty < 1) return;

        // Tìm item trong cart để kiểm tra stock
        const item = cart.data.items.find(item => item.id === id);
        if (item && item.stock && qty > item.stock) {
            // Có thể thêm notification ở đây
            console.warn(`Số lượng không thể vượt quá ${item.stock}`);
            return;
        }

        updateCartItem(id, qty);
    };

    const handleRemoveItem = (id, name) => {
        removeCartItem(id);
    };

    const handleClearCart = () => {
        clearCart();
    };

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const renderEmptyCart = () => (
        <Empty
            image={<ShoppingOutlined style={{ fontSize: 60, color: '#bfbfbf' }} />}
            imageStyle={{ height: 80 }}
            description={
                <Space direction="vertical" size="small">
                    <Text strong style={{ fontSize: 16 }}>Giỏ hàng trống</Text>
                    <Text type="secondary">Thêm sản phẩm để bắt đầu mua sắm</Text>
                    <Button
                        type="primary"
                        onClick={() => {
                            onClose();
                            navigate('/collections');
                        }}
                        style={{ marginTop: 16 }}
                    >
                        Khám phá sản phẩm
                    </Button>
                </Space>
            }
        />
    );

    // Tính giá thực tế cho từng item (flash sale hoặc giá gốc)
    const getItemPrice = (item) => {
        return item.has_discount && item.discount_price ? item.discount_price : item.price;
    };

    // Tính tổng tiền giỏ hàng
    const calculateSubtotal = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => {
            const itemPrice = getItemPrice(item);
            return sum + (itemPrice * item.quantity);
        }, 0);
    };

    // Tính số tiền tiết kiệm từ flash sale
    const calculateSavings = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => {
            if (item.has_flash_sale && item.flash_sale_price && item.price > item.flash_sale_price) {
                const savings = (item.price - item.flash_sale_price) * item.quantity;
                return sum + savings;
            }
            return sum;
        }, 0);
    };

    // Tính phần trăm giảm giá cho item
    const getDiscountPercentage = (item) => {
        if (item.has_flash_sale && item.flash_sale_price && item.price > item.flash_sale_price) {
            return Math.round(((item.price - item.flash_sale_price) / item.price) * 100);
        }
        return 0;
    };

    const subtotal = calculateSubtotal();
    const savings = calculateSavings();
    const hasFlashSaleItems = cart?.data?.items?.some(item => item.has_flash_sale && item.flash_sale_price);

    return (
        <Drawer
            title={
                <div className="flex items-center justify-between">
                    <Title level={5} style={{ margin: 0, color: 'white' }}>
                        Giỏ hàng ({cart?.data?.items?.length || 0})
                    </Title>
                </div>
            }
            placement="right"
            onClose={onClose}
            open={open}
            width={isMobile ? "100%" : 450}
            closable={true}
            maskStyle={{ backgroundColor: 'rgba(0,0,0,0.45)', backdropFilter: 'blur(2px)' }}
            closeIcon={<CloseOutlined style={{ color: 'white' }} />}
            className="cart-sidebar-drawer"
            bodyStyle={{ padding: 0 }}
            contentWrapperStyle={{ boxShadow: '0 0 15px rgba(0,0,0,0.2)' }}
            footer={
                cart?.data?.items?.length > 0 ? (
                    <div className="px-4 py-4 space-y-3">
                        <div className="space-y-2">
                            <div className="flex justify-between items-center">
                                <Text type="secondary">Tạm tính ({cart.data.items.length} sản phẩm)</Text>
                                <Text>{formatPrice(subtotal + savings)}</Text>
                            </div>

                            {hasFlashSaleItems && savings > 0 && (
                                <div className="flex justify-between items-center">
                                    <Text type="success">
                                        <TagOutlined className="mr-1" />
                                        Flash Sale tiết kiệm
                                    </Text>
                                    <Text type="success" strong>-{formatPrice(savings)}</Text>
                                </div>
                            )}

                            <Divider style={{ margin: '8px 0' }} />

                            <div className="flex justify-between items-center">
                                <Text strong>Tổng cộng</Text>
                                <Title level={4} style={{ margin: 0, color: '#ff4d4f' }}>
                                    {formatPrice(subtotal)}
                                </Title>
                            </div>
                        </div>

                        <Space direction="vertical" style={{ width: '100%' }} size="small">
                            <Button
                                type="primary"
                                size="large"
                                block
                                className='!bg-burgundy-primary !hover:bg-burgundy-primary/80'
                                onClick={() => {
                                    onClose();
                                    navigate('/checkout');
                                }}
                            >
                                Thanh toán ngay
                            </Button>

                            <div className="flex gap-2">
                                <Button
                                    size="middle"
                                    block
                                    onClick={() => {
                                        onClose();
                                    }}
                                >
                                    Tiếp tục mua
                                </Button>
                                <Popconfirm
                                    title="Xóa tất cả sản phẩm?"
                                    description="Bạn có chắc chắn muốn xóa tất cả sản phẩm trong giỏ hàng?"
                                    onConfirm={handleClearCart}
                                    okText="Xóa"
                                    cancelText="Hủy"
                                >
                                    <Button
                                        danger
                                        icon={<DeleteOutlined />}
                                    />
                                </Popconfirm>
                            </div>
                        </Space>
                    </div>
                ) : null
            }
        >
            {/* Cart content */}
            <div className="cart-content">
                {loading ? (
                    <div className="h-64 flex items-center justify-center">
                        <Spin size="large" tip="Đang tải..." />
                    </div>
                ) : !cart?.data?.items?.length ? (
                    <div className="h-96 flex items-center justify-center p-4">
                        {renderEmptyCart()}
                    </div>
                ) : (
                    <div className="cart-items">
                        {cart.data.items.map(item => {
                            const itemPrice = getItemPrice(item);
                            const itemTotal = itemPrice * item.quantity;
                            console.log(item)

                            return (
                                <div key={item.id} className="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                    <div className="flex gap-4">
                                        <div className="w-20 h-20 rounded-md overflow-hidden border border-gray-200">
                                            <img
                                                src={item.image || '/placeholder.jpg'}
                                                alt={item.name}
                                                className="w-full h-full object-cover"
                                            />
                                        </div>

                                        {/* Product details */}
                                        <div className="flex-1 min-w-0">
                                            <Title level={5} ellipsis={{ rows: 2 }} style={{ marginTop: 0, marginBottom: 4 }}>
                                                {item.name}
                                            </Title>

                                            {item.category && (
                                                <Text type="secondary" ellipsis className="text-xs block mb-1">
                                                    {item.category}
                                                </Text>
                                            )}
                                            {/* Flash Sale indicator */}
                                            {item.has_flash_sale && item.flash_sale_price && (
                                                <div className="mb-2">
                                                    <span className="inline-flex items-center px-2 py-1 rounded-full text-xs bg-red-100 text-red-800">
                                                        <TagOutlined className="w-3 h-3 mr-1" />
                                                        Flash Sale
                                                    </span>
                                                </div>
                                            )}

                                            <div className="flex justify-between items-end mt-2">
                                                <div className="flex items-center gap-1">
                                                    <Button
                                                        icon={<MinusOutlined />}
                                                        size="small"
                                                        onClick={() => handleChangeQty(item.id, item.quantity - 1)}
                                                        disabled={item.quantity <= 1}
                                                        className="flex items-center justify-center"
                                                    />
                                                    <InputNumber
                                                        min={1}
                                                        max={item.stock || undefined}
                                                        value={item.quantity}
                                                        onChange={(value) => handleChangeQty(item.id, value)}
                                                        size="small"
                                                        controls={false}
                                                        className="w-12 text-center"
                                                    />
                                                    <Button
                                                        icon={<PlusOutlined />}
                                                        size="small"
                                                        onClick={() => handleChangeQty(item.id, item.quantity + 1)}
                                                        disabled={item.stock && item.quantity >= item.stock}
                                                        className="flex items-center justify-center"
                                                    />
                                                </div>

                                                <Button
                                                    type="text"
                                                    danger
                                                    icon={<DeleteOutlined />}
                                                    size="small"
                                                    onClick={() => handleRemoveItem(item.id, item.name)}
                                                />
                                            </div>
                                        </div>

                                        {/* Price column */}
                                        <div className="text-right ml-2">
                                            {item.has_discount && item.discount_price && item.price > item.discount_price && (
                                                <div>
                                                    <Text delete type="secondary" className="!text-xs !text-red-500">
                                                        {formatPrice(item.price)}
                                                    </Text>
                                                </div>
                                            )}


                                            {/* Total price for quantity */}
                                            <div className="mt-1">
                                                <Text strong className="text-red-500">
                                                    {formatPrice(itemTotal)}
                                                </Text>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            );
                        })}
                    </div>
                )}
            </div>

            <style jsx global>{`
                .cart-sidebar-drawer .ant-drawer-header {
                    background-color: #262212;
                    color: white;
                    border-bottom: none;
                    padding: 16px 24px;
                }
                .cart-sidebar-drawer .ant-drawer-title,
                .cart-sidebar-drawer .ant-drawer-close {
                    color: white;
                }
                .cart-sidebar-drawer .ant-drawer-body {
                    padding: 0;
                }
                .cart-sidebar-drawer .ant-empty {
                    margin: 32px 0;
                }
                .cart-sidebar-drawer .cart-items {
                    max-height: calc(100vh - 240px);
                    overflow-y: auto;
                }
                .cart-sidebar-drawer .cart-items::-webkit-scrollbar {
                    width: 5px;
                }
                .cart-sidebar-drawer .cart-items::-webkit-scrollbar-track {
                    background: #f1f1f1;
                }
                .cart-sidebar-drawer .cart-items::-webkit-scrollbar-thumb {
                    background: #ccc;
                    border-radius: 4px;
                }
                .cart-sidebar-drawer .cart-items::-webkit-scrollbar-thumb:hover {
                    background: #999;
                }
                .cart-sidebar-drawer .ant-drawer-footer {
                    border-top: 1px solid #f0f0f0;
                    padding: 16px;
                    background: #fafafa;
                }
                .cart-sidebar-drawer .ant-input-number-input {
                    text-align: center;
                }
                @media (max-width: 768px) {
                    .cart-sidebar-drawer .cart-items {
                        max-height: calc(100vh - 280px);
                    }
                }
            `}</style>
        </Drawer>
    );
};

export default CartSidebar;
