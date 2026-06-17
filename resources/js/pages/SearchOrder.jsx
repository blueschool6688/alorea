import useAuth from "@/hooks/useAuth";
import api from '@/utils/api';
import {
    CreditCardOutlined,
    DollarCircleOutlined,
    EyeOutlined,
    MailOutlined,
    SearchOutlined,
    ShoppingOutlined
} from '@ant-design/icons';
import {
    Alert,
    Badge,
    Button,
    Card,
    Col,
    Collapse,
    ConfigProvider,
    DatePicker,
    Divider,
    Empty,
    Form,
    Image,
    Input,
    List,
    Result,
    Row,
    Select,
    Space,
    Spin,
    Steps,
    Table,
    Tag,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate } from 'react-router-dom';


const { Title, Text, Paragraph } = Typography;
const { Panel } = Collapse;
const { RangePicker } = DatePicker;
const { Option } = Select;

const SearchOrder = () => {
    const [form] = Form.useForm();
    const { isAuthenticated, loading } = useAuth();
    const [loadingData, setLoading] = useState(false);
    const [orders, setOrders] = useState([]);
    const [searched, setSearched] = useState(false);
    const [error, setError] = useState(null);
    const [isMobile, setIsMobile] = useState(false);
    const [expandedOrder, setExpandedOrder] = useState(null);
    const navigate = useNavigate();
    const [filters, setFilters] = useState({
        status: 'all',
        payment_status: 'all',
        dateRange: null,
        sortBy: 'newest'
    });

    // Check screen size
    useEffect(() => {
        const checkMobile = () => {
            setIsMobile(window.innerWidth < 768);
        };

        checkMobile();
        window.addEventListener('resize', checkMobile);

        return () => window.removeEventListener('resize', checkMobile);
    }, []);

    useEffect(() => {
        if (!loading && isAuthenticated) {
            navigate('/404');
        }

    }, [loading, isAuthenticated])

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const getStatusColor = (status) => {
        const colors = {
            'pending': 'orange',
            'confirmed': 'blue',
            'processing': 'cyan',
            'shipping': 'geekblue',
            'delivered': 'green',
            'cancelled': 'red',
            'refunded': 'purple'
        };
        return colors[status] || 'default';
    };

    const getStatusText = (status) => {
        const texts = {
            'pending': 'Chờ xác nhận',
            'confirmed': 'Đã xác nhận',
            'processing': 'Đang xử lý',
            'shipping': 'Đang giao hàng',
            'delivered': 'Đã giao hàng',
            'cancelled': 'Đã hủy',
            'refunded': 'Đã hoàn tiền'
        };
        return texts[status] || status;
    };

    const getPaymentStatusColor = (status) => {
        return status === 'paid' ? 'green' : status === 'pending' ? 'orange' : 'red';
    };

    const getPaymentStatusText = (status) => {
        const texts = {
            'paid': 'Đã thanh toán',
            'pending': 'Chờ thanh toán',
            'failed': 'Thất bại'
        };
        return texts[status] || status;
    };

    const getCurrentStep = (status) => {
        const steps = {
            'pending': 0,
            'confirmed': 1,
            'shipped': 2,
            'completed': 3,
            'cancelled': -1,
            'refunded': -1
        };
        return steps[status] || 0;
    };

    const handleSearch = async (values) => {
        setLoading(true);
        setError(null);
        setOrders([]);
        setSearched(true);

        try {
            let orderNumber = values.orderNumber?.trim() || '';
            if (orderNumber.startsWith('ORD-')) {
                orderNumber = orderNumber.substring(4);
            }

            const params = new URLSearchParams({ email: values.email });
            if (orderNumber) {
                params.append('order_number', orderNumber);
            }

            const response = await api.get(`/guest-orders/search?${params}`);

            if (response?.data && response.data.length > 0) {
                setOrders(response.data);
            } else {
                setError('Không tìm thấy đơn hàng với thông tin đã nhập');
            }
        } catch (error) {
            console.error('Search error:', error);
            if (error.response?.status === 404) {
                setError('Không tìm thấy đơn hàng với thông tin đã nhập');
            } else {
                setError('Có lỗi xảy ra khi tìm kiếm. Vui lòng thử lại sau.');
            }
        } finally {
            setLoading(false);
        }
    };

    // Mobile-optimized product list
    const renderMobileProducts = (items) => (
        <List
            itemLayout="horizontal"
            dataSource={items}
            renderItem={item => (
                <List.Item className="px-0">
                    <List.Item.Meta
                        avatar={
                            <Badge count={item.quantity} size="small">
                                <Image
                                    src={item.product?.main_image_url || '/placeholder.jpg'}
                                    alt={item.product_name}
                                    width={50}
                                    height={50}
                                    className="rounded-lg object-cover"
                                    preview={false}
                                />
                            </Badge>
                        }
                        title={
                            <Text strong className="text-sm line-clamp-2">{item.product_name}</Text>
                        }
                        description={
                            <div className="flex justify-between items-center">
                                <Text className="text-xs">
                                    {formatPrice(item.price)} × {item.quantity}
                                </Text>
                                <Text strong className="text-red-500 text-sm">
                                    {formatPrice(item.price * item.quantity)}
                                </Text>
                            </div>
                        }
                    />
                </List.Item>
            )}
        />
    );

    // Desktop table columns for order items
    const columns = [
        {
            title: 'Sản phẩm',
            dataIndex: 'product',
            key: 'product',
            width: '40%',
            render: (_, record) => (
                <div className="flex items-center space-x-3">
                    <Image
                        src={record.product?.main_image_url || '/placeholder.jpg'}
                        alt={record.product_name}
                        width={50}
                        height={50}
                        className="rounded-lg object-cover"
                        preview={false}
                    />
                    <div className="flex-1">
                        <Text strong className="block text-sm">{record.product_name}</Text>
                        <Text type="secondary" className="text-xs">
                            SKU: {record.product_sku || 'N/A'}
                        </Text>
                    </div>
                </div>
            ),
        },
        {
            title: 'Đơn giá',
            dataIndex: 'pv_sales',
            key: 'pv_sales',
            width: '20%',
            render: (_, record) => {
                if (record?.pv_sales == record?.price) {
                    return <Text className="text-sm">{formatPrice(record.pv_sales)}</Text>
                }
                return <Text delete className="text-sm mr-2">{formatPrice(record.pv_sales)}</Text>
            },
            align: 'right',
            responsive: ['md'],
        },
        {
            title: 'Giá sau giảm',
            dataIndex: 'price',
            key: 'price',
            width: '20%',
            render: (price) => <Text className="text-sm">{formatPrice(price)}</Text>,
            align: 'right',
            responsive: ['md'],
        },
        {
            title: 'SL',
            dataIndex: 'quantity',
            key: 'quantity',
            width: '10%',
            align: 'center',
            render: (quantity) => <Text className="text-sm">{quantity}</Text>
        },
        {
            title: 'Thành tiền',
            key: 'total',
            width: '20%',
            render: (_, record) => (
                <Text strong className="text-sm">{formatPrice(record.price * record.quantity)}</Text>
            ),
            align: 'right',
        },
    ];

    // Render individual order card
    const renderOrderCard = (order, index) => (
        <Card
            key={order.id}
            className="!mb-4 shadow-sm border border-gray-200 hover:shadow-md transition-shadow"
            size={isMobile ? "small" : "default"}
        >
            <div className="space-y-4">
                {/* Order Header */}
                <div className="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 pb-3 border-b">
                    <div className="flex items-center gap-3">
                        <div>
                            <Title level={5} className="mb-1 text-base sm:text-lg">
                                #{order.order_number}
                            </Title>
                            <Text type="secondary" className="text-xs sm:text-sm">
                                {new Date(order.created_at).toLocaleDateString('vi-VN', {
                                    day: '2-digit',
                                    month: '2-digit',
                                    year: 'numeric',
                                    hour: '2-digit',
                                    minute: '2-digit'
                                })}
                            </Text>
                        </div>
                        <Space size="small" wrap>
                            <Tag
                                color={getStatusColor(order.status)}
                                className="text-xs"
                            >
                                {getStatusText(order.status)}
                            </Tag>
                            <Tag
                                color={getPaymentStatusColor(order.payment_status)}
                                className="text-xs"
                            >
                                {getPaymentStatusText(order.payment_status)}
                            </Tag>
                        </Space>
                    </div>
                    <div className="text-right">
                        <div className="text-xs text-gray-500 mb-1">Tổng tiền</div>
                        <Text strong className="text-red-500 text-lg">
                            {formatPrice(order.total)}
                        </Text>
                    </div>
                </div>

                {/* Quick Info */}
                <Row gutter={[16, 8]} className="text-sm">
                    <Col xs={24} sm={8}>
                        <div className="flex items-center gap-2">
                            <CreditCardOutlined className="text-gray-400" />
                            <span>{order.payment_method === 'cod' ? 'COD' : 'Online'}</span>
                        </div>
                    </Col>
                    <Col xs={24} sm={8}>
                        <div className="flex items-center gap-2">
                            <ShoppingOutlined className="text-gray-400" />
                            <span>{order.items?.length || 0} sản phẩm</span>
                        </div>
                    </Col>
                </Row>

                {/* Expandable Details */}
                <div className="flex justify-between items-center pt-2">
                    <Text type="secondary" className="text-sm">
                        {order.customer_name} • {order.customer_email} • {order.customer_phone}
                    </Text>
                    <div className="">
                        <Button
                            type="link"
                            size="small"
                            icon={<EyeOutlined />}
                            onClick={() => setExpandedOrder(expandedOrder === order.id ? null : order.id)}
                            className="p-0"
                        >
                            {expandedOrder === order.id ? 'Thu gọn' : 'Chi tiết'}
                        </Button>
                        {order.payment_method === 'ONLINE' && order.payment_status === 'pending' && (
                            <Button
                                target='__blank'
                                type="link"
                                size="small"
                                icon={<EyeOutlined />}
                                href={`/order/${order?.order_number.split("ORD-")[1]}`}
                                className="p-0"
                            >
                                Thanh toán ngay
                            </Button>
                        )}
                    </div>
                </div>

                {/* Expanded Content */}
                {expandedOrder === order.id && (
                    <div className="mt-4 pt-4 border-t space-y-4">
                        {/* Progress Steps */}
                        <Card size="small" title="Tiến trình đơn hàng">
                            <Steps
                                current={getCurrentStep(order.status)}
                                status={order.status === 'cancelled' ? 'error' : 'process'}
                                direction={isMobile ? 'vertical' : 'horizontal'}
                                size="small"
                                items={[
                                    { title: 'Đặt hàng' },
                                    { title: 'Thanh toán' },
                                    { title: 'Xử lý' },
                                    { title: 'Vận chuyển' },
                                    { title: 'Hoàn thành' },
                                ]}
                            />
                        </Card>

                        {/* Customer & Shipping Info */}
                        <Row gutter={[16, 16]}>
                            <Col xs={24} lg={12}>
                                <Card size="small" title="Thông tin khách hàng">
                                    <div className="space-y-2 text-sm">
                                        <div><strong>Tên:</strong> {order.customer_name}</div>
                                        <div><strong>Email:</strong> {order.customer_email}</div>
                                        <div><strong>SĐT:</strong> {order.customer_phone}</div>
                                    </div>
                                </Card>
                            </Col>
                            <Col xs={24} lg={12}>
                                <Card size="small" title="Địa chỉ giao hàng">
                                    <div className="text-sm">
                                        <div>{order.customer_address}</div>
                                        <Text type="secondary">
                                            {order.ward?.name}, {order.provinces?.name}
                                        </Text>
                                        {order.notes && (
                                            <div className="mt-2 pt-2 border-t">
                                                <strong>Ghi chú:</strong> {order.notes}
                                            </div>
                                        )}
                                    </div>
                                </Card>
                            </Col>
                        </Row>

                        {/* Products */}
                        <Card size="small" title={`Sản phẩm (${order.items?.length || 0})`}>
                            {isMobile ? (
                                renderMobileProducts(order.items || [])
                            ) : (
                                <Table
                                    dataSource={order.items || []}
                                    columns={columns}
                                    pagination={false}
                                    rowKey="id"
                                    size="small"
                                />
                            )}

                            <Divider style={{ margin: '12px 0' }} />

                            {/* Order Summary */}
                            <div className="bg-gray-50 p-3 rounded">
                                <div className="space-y-1 max-w-xs ml-auto text-sm">
                                    {order.discount > 0 && (
                                        <div className="flex justify-between text-red-500">
                                            <span>Giảm giá:</span>
                                            <span>-{formatPrice(order.discount)}</span>
                                        </div>
                                    )}

                                    {order.system_discount > 0 && (
                                        <div className="flex justify-between text-red-500">
                                            <span>Giảm giá online:</span>
                                            <span>-{formatPrice(order.system_discount)}</span>
                                        </div>
                                    )}

                                    {order.shipping_fee > 0 && (
                                        <div className="flex justify-between">
                                            <span>Phí vận chuyển:</span>
                                            <span>{formatPrice(order.shipping_fee)}</span>
                                        </div>
                                    )}

                                    <Divider style={{ margin: '8px 0' }} />

                                    <div className="flex justify-between font-semibold text-base">
                                        <span>Tổng cộng:</span>
                                        <Text strong className="text-red-500">
                                            {formatPrice(order.total)}
                                        </Text>
                                    </div>
                                </div>
                            </div>
                        </Card>

                        {/* Payment Alert */}
                        {order.payment_method === 'online' && order.payment_status === 'pending' && (
                            <Alert
                                message="Đơn hàng chưa được thanh toán"
                                description={
                                    <div className="mt-2">
                                        <p className="text-sm">Vui lòng hoàn tất thanh toán để chúng tôi xử lý đơn hàng của bạn.</p>
                                        {order.payment_url && (
                                            <Button
                                                type="primary"
                                                size="small"
                                                href={order.payment_url}
                                                target="_blank"
                                                className="mt-2"
                                                icon={<DollarCircleOutlined />}
                                            >
                                                Thanh toán ngay
                                            </Button>
                                        )}
                                    </div>
                                }
                                type="warning"
                                showIcon
                                className="rounded"
                            />
                        )}
                    </div>
                )}
            </div>
        </Card>
    );

    return (
        <ConfigProvider
            theme={{
                token: {
                    colorPrimary: '#8B2635',
                },
            }}
        >
            <div className="min-h-screen bg-gray-50">
                {/* Hero Section - Responsive */}
                <div className="bg-gradient-to-br from-burgundy-primary to-burgundy-dark">
                    <div className="relative overflow-hidden">
                        <div className="absolute inset-0 bg-black/20"></div>
                        <div className="relative px-4 py-12 sm:py-16 lg:py-20 mt-12">
                            <div className="max-w-4xl mx-auto text-center">
                                <Title
                                    level={1}
                                    className="!text-white !text-2xl sm:!text-4xl lg:!text-5xl !font-serif !mb-4"
                                >
                                    TRA CỨU ĐƠN HÀNG
                                </Title>
                                <div className="w-16 sm:w-24 h-1 bg-white mx-auto mb-4 sm:mb-6"></div>
                                <Paragraph className="!text-white/90 !text-base sm:!text-lg !font-sans !max-w-2xl !mx-auto">
                                    Nhập thông tin để theo dõi tình trạng đơn hàng của bạn
                                </Paragraph>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Search Form - Responsive */}
                <div className="px-4 -mt-8 sm:-mt-12 relative z-10">
                    <div className="max-w-2xl mx-auto">
                        <Card className="shadow-2xl border-0 rounded-2xl overflow-hidden">
                            <div className="p-4 sm:p-6 lg:p-8">
                                <Form
                                    form={form}
                                    layout="vertical"
                                    onFinish={handleSearch}
                                    size={isMobile ? "middle" : "large"}
                                >
                                    <Row gutter={[16, 0]}>
                                        <Col xs={24} sm={12}>
                                            <Form.Item
                                                name="email"
                                                label={<Text strong>Email đặt hàng</Text>}
                                                rules={[
                                                    { required: true, message: 'Vui lòng nhập email' },
                                                    { type: 'email', message: 'Email không hợp lệ' }
                                                ]}
                                            >
                                                <Input
                                                    prefix={<MailOutlined className="text-gray-400" />}
                                                    placeholder="your@email.com"
                                                />
                                            </Form.Item>
                                        </Col>
                                        <Col xs={24} sm={12}>
                                            <Form.Item
                                                name="orderNumber"
                                                label={<Text strong>Mã đơn hàng (tùy chọn)</Text>}
                                            >
                                                <Input
                                                    prefix={<SearchOutlined className="text-gray-400" />}
                                                    placeholder="ORD-XXXXXX"
                                                />
                                            </Form.Item>
                                        </Col>
                                    </Row>

                                    <Form.Item className="mb-4">
                                        <Button
                                            type="primary"
                                            htmlType="submit"
                                            loadingData={loadingData}
                                            block
                                            size={isMobile ? "middle" : "large"}
                                            icon={<SearchOutlined />}
                                            className="h-12 font-semibold"
                                        >
                                            {loadingData ? 'Đang tìm kiếm...' : 'Tìm kiếm đơn hàng'}
                                        </Button>
                                    </Form.Item>
                                </Form>
                            </div>
                        </Card>
                    </div>
                </div>

                {/* Results Section */}
                <div className="px-4 py-8 sm:py-12">
                    <div className="max-w-6xl mx-auto">
                        {/* Loading State */}
                        {loadingData && (
                            <div className="text-center py-16">
                                <Spin size="large" />
                                <div className="mt-4">
                                    <Text className="text-lg">Đang tìm kiếm đơn hàng...</Text>
                                </div>
                            </div>
                        )}

                        {/* Error State */}
                        {error && searched && !loadingData && (
                            <Result
                                status="404"
                                title="Không tìm thấy đơn hàng"
                                subTitle={error}
                                extra={
                                    <Button
                                        type="primary"
                                        size="large"
                                        onClick={() => {
                                            setSearched(false);
                                            setError(null);
                                            form.resetFields();
                                        }}
                                    >
                                        Tìm kiếm lại
                                    </Button>
                                }
                            />
                        )}

                        {/* Orders Results */}
                        {orders.length > 0 && !loadingData && (
                            <div className="space-y-6">
                                {/* Orders List */}
                                {console.log(orders)}
                                {orders.length > 0 ? (
                                    <div>
                                        <div className="flex justify-between items-center mb-4">
                                            <Title level={4} className="mb-0">
                                                Đơn hàng của bạn ({orders.length})
                                            </Title>
                                        </div>
                                        {orders.map((order, index) => renderOrderCard(order, index))}
                                    </div>
                                ) : (
                                    <Empty
                                        description="Không tìm thấy đơn hàng phù hợp với bộ lọc"
                                        image={Empty.PRESENTED_IMAGE_SIMPLE}
                                    >
                                        <Button
                                            onClick={() => setFilters({ status: 'all', payment_status: 'all', dateRange: null, sortBy: 'newest' })}
                                        >
                                            Xóa bộ lọc
                                        </Button>
                                    </Empty>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <style jsx>{`
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            `}</style>
        </ConfigProvider>
    );
};

export default SearchOrder;
