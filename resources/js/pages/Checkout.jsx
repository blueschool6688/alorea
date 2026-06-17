import useAuth from '@/hooks/useAuth';
import { useCart } from '@/lib/context/CartContext';
import { useSettings } from '@/lib/context/SettingContext';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import {
    CreditCardOutlined,
    DollarOutlined,
    FireOutlined,
    HomeOutlined,
    LoadingOutlined,
    PhoneOutlined,
    ShoppingOutlined,
    UserOutlined
} from '@ant-design/icons';
import {
    Alert,
    Button,
    Card,
    Col,
    Collapse,
    Divider,
    Form,
    Input,
    List,
    message,
    Radio,
    Row,
    Select,
    Space,
    Spin,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';

const { Title, Text, Paragraph } = Typography;
const { Option } = Select;

const Checkout = () => {
    const navigate = useNavigate();
    const { settings } = useSettings();
    const { cart, clearCart } = useCart();
    const { user, isAuthenticated, loading, initialized } = useAuth();
    const [loadingData, setLoading] = useState(false);
    const [provinces, setProvinces] = useState([]);
    const [wards, setWards] = useState([]);
    const [paymentMethod, setPaymentMethod] = useState('cod');
    const [processing, setProcessing] = useState(false);
    const [loadingWards, setLoadingWards] = useState(false);
    const [isSubmitting, setIsSubmitting] = useState(false);
    const [form] = Form.useForm();
    const [orderSuccess, setOrderSuccess] = useState(false);

    const fetchProvinces = async () => {
        try {
            setLoading(true);
            const response = await api.get('/get-provinces');
            if (response && response.data) {
                setProvinces(Array.isArray(response.data) ? response.data : []);
            } else {
                setProvinces([]);
            }
        } catch (error) {
            console.error('Error fetching provinces:', error);
            setProvinces([]);
            message.error('Không thể tải danh sách tỉnh/thành phố');
        } finally {
            setLoading(false);
        }
    };

    useEffect(() => {
        form.setFieldsValue({
            name: user?.name,
            email: user?.email,
            phone: user?.phone,
            address: user?.address,
        });
        fetchProvinces();
    }, [form, user]);

    // Handle province change
    const handleProvinceChange = async (provinceCode) => {
        try {
            form.setFieldsValue({ ward: undefined });
            setWards([]);
            setLoadingWards(true);

            const response = await api.get(`/get-wards/${provinceCode}`);
            if (response && response.data) {
                setWards(Array.isArray(response.data) ? response.data : []);
            } else {
                setWards([]);
            }
        } catch (error) {
            setWards([]);
            message.error('Không thể tải danh sách phường/xã');
        } finally {
            setLoadingWards(false);
        }
    };

    // Calculate pricing directly from cart data
    const getItemPrice = (item) => {
        return item.has_discount && item.discount_price ? item.discount_price : item.price;
    };

    const calculateSubtotal = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => {
            const itemPrice = getItemPrice(item);
            return sum + (itemPrice * item.quantity);
        }, 0);
    };

    const calculateOriginalSubtotal = () => {
        if (!cart?.data?.items?.length) return 0;
        return cart.data.items.reduce((sum, item) => sum + (item.price * item.quantity), 0);
    };

    const calculateTotalDiscount = () => {
        const originalSubtotal = calculateOriginalSubtotal();
        const currentSubtotal = calculateSubtotal();
        return originalSubtotal - currentSubtotal;
    };

    // Thêm function tính giảm giá hệ thống
    const calculateSystemDiscount = () => {
        if (paymentMethod === 'online' && settings?.discount_global) {
            const subtotal = calculateSubtotal();
            return (subtotal * settings.discount_global) / 100;
        }
        return 0;
    };

    // Cập nhật function calculateTotal để bao gồm system discount
    const calculateTotal = () => {
        const subtotal = calculateSubtotal();
        const systemDiscount = calculateSystemDiscount();
        const shipping = settings?.shipping_fee || 0;
        return subtotal - systemDiscount + shipping;
    };

    const totalDiscount = calculateTotalDiscount();

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const handleSubmit = async (values) => {
        if (!cart?.data?.items?.length) {
            message.error('Giỏ hàng của bạn đang trống');
            return;
        }
        setIsSubmitting(true);
        setProcessing(true);
        try {
            const selectedProvince = provinces.find(p => p.code === values.province);
            const selectedWard = wards.find(w => w.code === values.ward);

            const orderData = {
                ...values,
                province_name: selectedProvince?.name || '',
                ward_name: selectedWard?.name || '',
                user_id: isAuthenticated && user ? user.id : null,
                items: cart.data.items,
                payment_method: paymentMethod,
                subtotal: calculateSubtotal(),
                original_subtotal: calculateOriginalSubtotal(),
                flash_sale_discount: totalDiscount,
                system_discount: calculateSystemDiscount(),
                system_discount_percentage: paymentMethod === 'online' && settings?.discount_global ? settings.discount_global : 0,
                shipping_fee: settings?.shipping_fee || 0,
                total: calculateTotal()
            };

            const response = await api.post(GeneralPath.STORE_ORDER_ENDPOINT, orderData);
            console.log(response)
            if (response && response?.data) {
                setOrderSuccess(true);
                await clearCart();
                const code = response.data.order_number.split("ORD-")[1];
                navigate(`/order/${code}`, { replace: true });
            }
        } catch (error) {
            message.error(error || 'Đặt hàng thất bại. Vui lòng thử lại sau.');
            setIsSubmitting(false);
        } finally {
            setProcessing(false);
            setIsSubmitting(false);
        }
    };

    // Redirect if cart is empty
    useEffect(() => {
        if (cart?.data?.items?.length === 0 && !loadingData && !isSubmitting && !orderSuccess) {
            navigate('/', { replace: true });
        }
    }, [cart, loadingData, navigate, isSubmitting, orderSuccess]);

    if (loading && !initialized) {
        return (
            <div className="h-screen flex items-center justify-center">
                <Spin size="large" indicator={<LoadingOutlined style={{ fontSize: 36 }} spin />} />
            </div>
        );
    }

    return (
        <div className="bg-gray-50 min-h-screen py-12">
            <div className="relative bg-black text-white py-20">
                <div className="absolute inset-0 bg-gradient-to-r from-black/80 to-transparent z-10"></div>
                <div
                    className="absolute inset-0 bg-cover bg-center opacity-30"
                    style={{
                        backgroundImage: 'url(https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=1920&h=1080&fit=crop)'
                    }}
                ></div>

                <div className="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center space-y-6">
                        <Title level={1} className="!text-white !text-5xl md:!text-6xl !font-serif !mb-4">
                            THANH TOÁN
                        </Title>
                        <div className="w-32 h-1 bg-white mx-auto mb-8"></div>
                        <Paragraph className="!text-white/90 !font-sans !text-xl !max-w-3xl !mx-auto !leading-relaxed">
                            Hoàn tất đơn hàng của bạn
                        </Paragraph>
                    </div>
                </div>
            </div>

            <div className="max-w-7xl mx-auto px-4 mt-4">
                <Form form={form} layout="vertical" onFinish={handleSubmit}>
                    <Row gutter={24}>
                        {/* Customer Information */}
                        <Col xs={24} lg={16}>
                            <Card
                                title={
                                    <div className="flex items-center">
                                        <UserOutlined style={{ marginRight: 8 }} />
                                        <span>Thông tin khách hàng</span>
                                    </div>
                                }
                                className="mb-6"
                                headStyle={{ borderBottom: '1px solid #f0f0f0', paddingTop: 16, paddingBottom: 16 }}
                            >
                                <Row gutter={16}>
                                    <Col xs={24} md={12}>
                                        <Form.Item
                                            name="name"
                                            label="Họ và tên"
                                            rules={[{ required: true, message: 'Vui lòng nhập họ tên' }]}
                                        >
                                            <Input placeholder="Nhập họ tên người nhận" prefix={<UserOutlined className="text-gray-400" />} />
                                        </Form.Item>
                                    </Col>
                                    <Col xs={24} md={12}>
                                        <Form.Item
                                            name="phone"
                                            label="Số điện thoại"
                                            rules={[
                                                { required: true, message: 'Vui lòng nhập số điện thoại' },
                                                { pattern: /^[0-9]{10,11}$/, message: 'Số điện thoại không hợp lệ' }
                                            ]}
                                        >
                                            <Input placeholder="Nhập số điện thoại" prefix={<PhoneOutlined className="text-gray-400" />} />
                                        </Form.Item>
                                    </Col>
                                </Row>
                                <Form.Item
                                    name="email"
                                    label="Email"
                                    rules={[
                                        { required: true, message: 'Vui lòng nhập email' },
                                        { type: 'email', message: 'Email không hợp lệ' }
                                    ]}
                                >
                                    <Input placeholder="Nhập email" />
                                </Form.Item>
                            </Card>

                            {/* Shipping Address */}
                            <Card
                                title={
                                    <div className="flex items-center">
                                        <HomeOutlined style={{ marginRight: 8 }} />
                                        <span>Địa chỉ giao hàng</span>
                                    </div>
                                }
                                className="mb-6"
                                headStyle={{ borderBottom: '1px solid #f0f0f0', paddingTop: 16, paddingBottom: 16 }}
                            >
                                <Row gutter={16}>
                                    <Col xs={24} md={12}>
                                        <Form.Item
                                            name="province"
                                            label="Tỉnh/Thành phố"
                                            rules={[{ required: true, message: 'Vui lòng chọn Tỉnh/Thành phố' }]}
                                        >
                                            <Select
                                                placeholder="Chọn Tỉnh/Thành phố"
                                                onChange={handleProvinceChange}
                                                showSearch
                                                filterOption={(input, option) =>
                                                    option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                                                }
                                            >
                                                {Array.isArray(provinces) && provinces.map(province => (
                                                    <Option key={province.province_code} value={province.province_code}>
                                                        {province.name}
                                                    </Option>
                                                ))}
                                            </Select>
                                        </Form.Item>
                                    </Col>
                                    <Col xs={24} md={12}>
                                        <Form.Item
                                            name="ward"
                                            label="Phường/Xã"
                                            rules={[{ required: true, message: 'Vui lòng chọn Phường/Xã' }]}
                                        >
                                            <Select
                                                placeholder="Chọn Phường/Xã"
                                                disabled={!form.getFieldValue('province')}
                                                loading={loadingWards}
                                                showSearch
                                                filterOption={(input, option) =>
                                                    option.children.toLowerCase().indexOf(input.toLowerCase()) >= 0
                                                }
                                                notFoundContent={loadingWards ? <Spin size="small" /> : 'Không có dữ liệu'}
                                            >
                                                {Array.isArray(wards) && wards.map(ward => (
                                                    <Option key={ward.ward_code} value={ward.ward_code}>
                                                        {ward.name}
                                                    </Option>
                                                ))}
                                            </Select>
                                        </Form.Item>
                                    </Col>
                                </Row>
                                <Form.Item
                                    name="address"
                                    label="Địa chỉ cụ thể"
                                    rules={[{ required: true, message: 'Vui lòng nhập địa chỉ cụ thể' }]}
                                >
                                    <Input.TextArea placeholder="Nhập số nhà, tên đường..." rows={3} />
                                </Form.Item>
                                <Form.Item name="note" label="Ghi chú (tùy chọn)">
                                    <Input.TextArea
                                        placeholder="Ghi chú về đơn hàng, ví dụ: thời gian hay chỉ dẫn địa điểm giao hàng chi tiết hơn."
                                        rows={3}
                                    />
                                </Form.Item>
                            </Card>

                            {/* Payment Method */}
                            <Card
                                title={
                                    <div className="flex items-center">
                                        <CreditCardOutlined style={{ marginRight: 8 }} />
                                        <span>Phương thức thanh toán</span>
                                    </div>
                                }
                                className="mb-6"
                                headStyle={{ borderBottom: '1px solid #f0f0f0', paddingTop: 16, paddingBottom: 16 }}
                            >
                                <Radio.Group
                                    onChange={(e) => setPaymentMethod(e.target.value)}
                                    value={paymentMethod}
                                    className="w-full"
                                >
                                    <Space direction="vertical" className="w-full">
                                        <Radio value="cod" className="payment-option">
                                            <Card
                                                className="w-full cursor-pointer hover:bg-gray-50"
                                                size="small"
                                                bordered={paymentMethod === 'cod'}
                                                style={{ borderColor: paymentMethod === 'cod' ? '#000' : '#f0f0f0' }}
                                            >
                                                <div className="flex items-center">
                                                    <DollarOutlined style={{ fontSize: 20, marginRight: 8 }} />
                                                    <div>
                                                        <div className="font-medium">Thanh toán khi nhận hàng (COD)</div>
                                                        <div className="text-gray-500 text-sm">Thanh toán bằng tiền mặt khi nhận được hàng</div>
                                                    </div>
                                                </div>
                                            </Card>
                                        </Radio>
                                        <Radio value="online" className="payment-option">
                                            <Card
                                                className="w-full cursor-pointer hover:bg-gray-50"
                                                size="small"
                                                bordered={paymentMethod === 'online'}
                                                style={{ borderColor: paymentMethod === 'online' ? '#000' : '#f0f0f0' }}
                                            >
                                                <div className="flex items-center">
                                                    <CreditCardOutlined style={{ fontSize: 20, marginRight: 8 }} />
                                                    <div>
                                                        <div className="font-medium">Thanh toán online</div>
                                                        <div className="text-gray-500 text-sm">Thanh toán bằng thẻ ATM, Visa, MasterCard hoặc ví điện tử</div>
                                                    </div>
                                                </div>
                                            </Card>
                                        </Radio>
                                    </Space>
                                </Radio.Group>
                                {paymentMethod === 'online' && (
                                    <div className="mt-4 bg-gray-50 p-4 rounded">
                                        <Alert
                                            message="Chuyển hướng thanh toán"
                                            description="Bạn sẽ được chuyển đến cổng thanh toán an toàn sau khi xác nhận đơn hàng."
                                            type="info"
                                            showIcon
                                        />
                                    </div>
                                )}
                            </Card>
                        </Col>

                        {/* Order Summary */}
                        <Col xs={24} md={24} lg={8}>
                            <div className="lg:sticky lg:top-18">
                                <Card
                                    title={
                                        <div className="flex items-center">
                                            <ShoppingOutlined style={{ marginRight: 8 }} />
                                            <span>Đơn hàng của bạn</span>
                                        </div>
                                    }
                                    className="mb-6 shadow-md hover:shadow-lg transition-shadow duration-300"
                                    headStyle={{ borderBottom: '1px solid #f0f0f0', paddingTop: 16, paddingBottom: 16 }}
                                    bodyStyle={{ padding: '16px', paddingBottom: '20px' }}
                                >
                                    {/* Mobile Order Summary Toggle */}
                                    <div className="block lg:hidden mb-3">
                                        <Collapse ghost>
                                            <Collapse.Panel
                                                header={
                                                    <div className="flex justify-between items-center w-full">
                                                        <span className="font-medium">Chi tiết đơn hàng ({cart?.data?.items?.length || 0} sản phẩm)</span>
                                                        <span className="text-red-500 font-semibold">{formatPrice(calculateTotal())}</span>
                                                    </div>
                                                }
                                                key="1"
                                            >
                                                <List
                                                    itemLayout="horizontal"
                                                    dataSource={cart?.data?.items || []}
                                                    renderItem={item => {
                                                        const finalPrice = getItemPrice(item);
                                                        const hasFlashSale = item.has_discount && item.discount_price;
                                                        ;

                                                        return (
                                                            <List.Item className="py-2 px-0">
                                                                <div className="flex w-full">
                                                                    <div className="relative mr-3">
                                                                        <div className="w-14 h-14 border rounded overflow-hidden">
                                                                            <img
                                                                                src={item.image}
                                                                                className="w-full !h-full object-cover"
                                                                                alt="" />
                                                                        </div>
                                                                        <div className="absolute -top-2 -right-2 bg-gray-700 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs">
                                                                            {item.quantity}
                                                                        </div>

                                                                    </div>
                                                                    <div className="flex-1">
                                                                        <div className="flex justify-between">
                                                                            <div>
                                                                                <Text strong className="text-sm line-clamp-2">{item.name}</Text>
                                                                                {item.category && (
                                                                                    <Text type="secondary" className="text-xs block">{item.category}</Text>
                                                                                )}
                                                                            </div>
                                                                            <div className="text-right">
                                                                                <Text strong className="text-sm">{formatPrice(finalPrice * item.quantity)}</Text>
                                                                                {hasFlashSale && item.price > finalPrice && (
                                                                                    <div>
                                                                                        <Text delete type="secondary" className="text-xs">
                                                                                            {formatPrice(item.price * item.quantity)}
                                                                                        </Text>
                                                                                    </div>
                                                                                )}
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                </div>
                                                            </List.Item>
                                                        );
                                                    }}
                                                />
                                            </Collapse.Panel>
                                        </Collapse>
                                    </div>

                                    {/* Desktop Order Summary */}
                                    <div className="hidden lg:block">
                                        <List
                                            itemLayout="horizontal"
                                            dataSource={cart?.data?.items || []}
                                            renderItem={item => {
                                                const finalPrice = getItemPrice(item);
                                                const hasFlashSale = item.has_discount && item.discount_price;


                                                return (
                                                    <List.Item className="py-3 relative">
                                                        <div className="flex w-full">
                                                            <div className="relative mr-4">
                                                                <div className="w-16 h-16 border rounded overflow-hidden">
                                                                    <img
                                                                        src={item.image}
                                                                        className="w-full !h-full object-cover"
                                                                        alt="" />
                                                                </div>
                                                                <div className="absolute -top-2 -right-2 bg-gray-700 text-white rounded-full w-6 h-6 flex items-center justify-center text-xs">
                                                                    {item.quantity}
                                                                </div>
                                                            </div>
                                                            <div className="flex-grow">
                                                                <Text strong className="text-base line-clamp-1">{item.name}</Text>
                                                                {item.category && (
                                                                    <Text type="secondary" className="text-sm block">{item.category}</Text>
                                                                )}
                                                            </div>
                                                            <div className="text-right">
                                                                <Text strong>{formatPrice(finalPrice * item.quantity)}</Text>
                                                                {hasFlashSale && item.price > finalPrice && (
                                                                    <div>
                                                                        <Text delete type="secondary" className="text-xs">
                                                                            {formatPrice(item.price * item.quantity)}
                                                                        </Text>
                                                                    </div>
                                                                )}
                                                            </div>
                                                        </div>
                                                    </List.Item>
                                                );
                                            }}
                                        />
                                    </div>

                                    <Divider />
                                    <div className="space-y-2 mb-4">
                                        <div className="flex justify-between">
                                            <Text>Tạm tính:</Text>
                                            <Text>{formatPrice(calculateOriginalSubtotal())}</Text>
                                        </div>

                                        {totalDiscount > 0 && (
                                            <div className="flex justify-between text-red-500">
                                                <Text type="danger">
                                                    <FireOutlined /> Flash Sale:
                                                </Text>
                                                <Text type="danger" strong>
                                                    -{formatPrice(totalDiscount)}
                                                </Text>
                                            </div>
                                        )}

                                        {/* Hệ thống giảm giá khi thanh toán online */}
                                        {paymentMethod === 'online' && settings?.discount_global && (
                                            <div className="flex justify-between text-green-500">
                                                <Text type="danger">
                                                    <FireOutlined /> Hệ thống giảm giá:
                                                </Text>
                                                <Text type="danger" strong>
                                                    -{formatPrice(calculateSystemDiscount())}
                                                </Text>
                                            </div>
                                        )}

                                        {settings?.shipping_fee > 0 && (
                                            <div className="flex justify-between">
                                                <Text>Phí vận chuyển:</Text>
                                                <Text>{formatPrice(settings?.shipping_fee)}</Text>
                                            </div>
                                        )}
                                        <Divider style={{ margin: '12px 0' }} />
                                        <div className="flex justify-between font-semibold">
                                            <Title level={5} style={{ margin: 0 }}>Tổng cộng:</Title>
                                            <Title level={4} type="danger" style={{ margin: 0 }}>
                                                {formatPrice(calculateTotal())}
                                            </Title>
                                        </div>
                                    </div>

                                    {/* Fixed Mobile Order Button */}
                                    <div className="block lg:hidden fixed bottom-0 left-0 right-0 z-50 p-3 bg-white border-t border-gray-200">
                                        <div className="space-y-1 mb-2">
                                            <div className="flex justify-between items-center text-sm">
                                                <Text type="secondary">Tạm tính:</Text>
                                                <Text type="secondary">{formatPrice(calculateSubtotal())}</Text>
                                            </div>

                                            {paymentMethod === 'online' && settings?.discount_global && (
                                                <div className="flex justify-between items-center text-sm">
                                                    <Text type="danger">Giảm online ({settings.discount_global}%):</Text>
                                                    <Text type="danger">-{formatPrice(calculateSystemDiscount())}</Text>
                                                </div>
                                            )}

                                            <div className="flex justify-between items-center">
                                                <Text strong>Tổng cộng:</Text>
                                                <Text strong className="text-red-500 text-lg">{formatPrice(calculateTotal())}</Text>
                                            </div>
                                        </div>
                                        <Button
                                            type="primary"
                                            htmlType="submit"
                                            size="large"
                                            block
                                            loading={processing}
                                            style={{
                                                background: '#000',
                                                borderColor: '#000'
                                            }}
                                        >
                                            ĐẶT HÀNG
                                            {paymentMethod === 'online' && settings?.discount_global && (
                                                <span className="ml-2 text-xs">
                                                    (Tiết kiệm {formatPrice(calculateSystemDiscount())})
                                                </span>
                                            )}
                                        </Button>
                                    </div>

                                    {/* Desktop Order Button */}
                                    <div className="hidden lg:block">
                                        <Button
                                            type="primary"
                                            htmlType="submit"
                                            size="large"
                                            block
                                            loading={processing}
                                            style={{
                                                background: '#000',
                                                borderColor: '#000',
                                                height: 50
                                            }}
                                        >
                                            ĐẶT HÀNG
                                        </Button>

                                        <div className="mt-4 text-center">
                                            <Text type="secondary" className="text-xs">
                                                Bằng cách nhấn "Đặt hàng", bạn đồng ý với{' '}
                                                <a href="/terms" className="text-black underline">Điều khoản dịch vụ</a>
                                                {' '}và{' '}
                                                <a href="/privacy" className="text-black underline">Chính sách bảo mật</a>
                                                {' '}của chúng tôi
                                            </Text>
                                        </div>
                                    </div>
                                </Card>
                            </div>
                        </Col>
                    </Row>
                </Form>
            </div>

            <style jsx>{`
                .payment-option .ant-card-body {
                    padding: 12px;
                }
                .ant-form-item-label > label {
                    font-weight: 500;
                }
                .line-clamp-1 {
                    display: -webkit-box;
                    -webkit-line-clamp: 1;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            `}</style>
        </div>
    );
};

export default Checkout;
