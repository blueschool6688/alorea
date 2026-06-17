import useAuth from '@/hooks/useAuth';
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
    Badge,
    Button,
    Divider,
    Drawer,
    Empty,
    InputNumber,
    Space,
    Spin,
    Tag,
    Typography,
    message
} from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from "react-router-dom";

const { Title, Text } = Typography;

export const CartSidebar = ({ open, onClose }) => {
    const { cart, loading, updateCartItem, removeCartItem, clearCart } = useCart();
    const { isAuthenticated } = useAuth();
    const [coupon, setCoupon] = useState('');
    const navigate = useNavigate();
    const [isMobile, setIsMobile] = useState(window.innerWidth < 768);

    // Theo dõi kích thước màn hình
    useEffect(() => {
        const handleResize = () => setIsMobile(window.innerWidth < 768);
        window.addEventListener('resize', handleResize);
        return () => window.removeEventListener('resize', handleResize);
    }, []);

    // Prevent body scroll when open
    useEffect(() => {
        if (open) {
            document.body.style.overflow = 'hidden';
        } else {
            document.body.style.overflow = 'unset';
        }
        return () => {
            document.body.style.overflow = 'unset';
        };
    }, [open]);

    const handleChangeQty = (id, qty) => {
        if (qty < 1) return;
        updateCartItem(id, qty);
    };

    // Format price to VND
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const handleCheckout = async () => {
        if (!isAuthenticated) {
            message.info('Vui lòng đăng nhập để tiếp tục thanh toán');
        }
        else {
            onClose();
            navigate('/checkout');
        }
    }

    const handleRemoveItem = (itemId, itemName) => {
        removeCartItem(itemId);
        message.success(`Đã xóa sản phẩm khỏi giỏ hàng`);
    };

    const handleClearCart = () => {
        if (cart?.data?.items?.length) {
            clearCart();
            message.success('Đã xóa tất cả sản phẩm khỏi giỏ hàng');
        }
    };

    const renderEmptyCart = () => (
        <Empty
            image={<ShoppingOutlined style={{ fontSize: 60, color: '#bfbfbf' }} />}
            imageStyle={{ height: 80 }}
            description={
                <Space direction="vertical" size="small">
                    <Text strong className='!font-sans' style={{ fontSize: 16 }}>Giỏ hàng trống</Text>
                    <Text type="secondary" className='!font-sans'>Thêm sản phẩm để bắt đầu mua sắm</Text>
                </Space>
            }
        />
    );

    const calculateSubtotal = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    };

    const calculateSavings = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => {
            if (item.originalPrice) {
                return sum + ((item.originalPrice - item.price) * item.quantity);
            }
            return sum;
        }, 0);
    };

    const subtotal = calculateSubtotal();
    const savings = calculateSavings();
    const hasDiscounts = cart?.data?.items?.some(item => item.originalPrice);

    return (
        <Drawer
            title={
                <div className="flex items-center justify-between ">
                    <Title className='!text-white !font-sans' level={5} style={{ margin: 0 }}>
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
            closeIcon={<CloseOutlined />}
            className="cart-sidebar-drawer"
            bodyStyle={{ padding: 0 }}
            contentWrapperStyle={{ boxShadow: '0 0 15px rgba(0,0,0,0.2)' }}
            footer={
                cart?.data?.items?.length > 0 ? (
                    <div className="px-4 py-4 space-y-3">
                        <div className="space-y-2">
                            <div className="flex justify-between items-center text-sm">
                                <Text type="secondary">Tạm tính ({cart.data.items.length} sản phẩm)</Text>
                                <Text>{formatPrice(subtotal)}</Text>
                            </div>

                            {hasDiscounts && savings > 0 && (
                                <div className="flex justify-between items-center">
                                    <Text type="success">Tiết kiệm</Text>
                                    <Text type="success">-{formatPrice(savings)}</Text>
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
                                onClick={handleCheckout}
                                style={{ height: 48 }}
                            >
                                Thanh toán ngay
                            </Button>

                            <div className="flex gap-2">
                                <Button
                                    size="middle"
                                    block
                                    onClick={onClose}
                                >
                                    Tiếp tục mua sắm
                                </Button>
                                <Button
                                    danger
                                    icon={<DeleteOutlined />}
                                    onClick={handleClearCart}
                                    disabled={!cart?.data?.items?.length}
                                />
                            </div>
                        </Space>

                        <div className="text-center mt-2">
                            <Text type="secondary" className="text-xs">
                                🚚 Miễn phí vận chuyển cho đơn hàng từ 1.000.000₫
                            </Text>
                        </div>
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
                        {cart.data.items.map(item => (
                            <div key={item.id} className="p-4 border-b border-gray-100 hover:bg-gray-50 transition-colors">
                                <div className="flex gap-4">
                                    {/* Image with discount badge */}
                                    <div className="relative">
                                        <Badge
                                            count={item.originalPrice ? `-${Math.round((1 - item.price / item.originalPrice) * 100)}%` : 0}
                                            style={{ backgroundColor: '#ff4d4f' }}
                                            offset={[0, 0]}
                                            showZero={false}
                                        >
                                            <div className="w-20 h-20 rounded-md overflow-hidden border border-gray-200">
                                                <img
                                                    src={item.image || item.images?.[0] || '/placeholder.jpg'}
                                                    alt={item.name}
                                                    className="w-full h-full object-cover"
                                                />
                                            </div>
                                        </Badge>
                                    </div>

                                    {/* Product details */}
                                    <div className="flex-1 min-w-0">
                                        <Title level={5} ellipsis={{ rows: 2 }} style={{ marginTop: 0, marginBottom: 4 }}>
                                            {item.name}
                                        </Title>

                                        {item.subtitle && (
                                            <Text type="secondary" ellipsis className="text-xs block mb-1">
                                                {item.subtitle}
                                            </Text>
                                        )}

                                        <Space size={4} className="mb-2">
                                            <Tag size="small" color="default">
                                                <TagOutlined /> {item.category}
                                            </Tag>
                                            {item.size && (
                                                <Tag size="small">{item.size}</Tag>
                                            )}
                                        </Space>

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
                                        {item.originalPrice && (
                                            <Text delete type="secondary" className="text-xs">
                                                {formatPrice(item.originalPrice)}
                                            </Text>
                                        )}
                                        <div>
                                            <Text>{formatPrice(item.price)}</Text>
                                        </div>
                                        <div>
                                            <Text strong className="text-red-500">
                                                {formatPrice(item.price * item.quantity)}
                                            </Text>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        ))}
                    </div>
                )}
            </div>

            <style jsx global>{`
                .cart-sidebar-drawer .ant-drawer-header {
                    background-color: #000;
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
