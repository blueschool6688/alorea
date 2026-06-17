import api from '@/utils/api';
import {
    CheckCircleOutlined,
    ClockCircleOutlined,
    QrcodeOutlined
} from '@ant-design/icons';
import {
    Alert,
    Button,
    Card,
    Col,
    Descriptions,
    Divider,
    List,
    message,
    Row,
    Space,
    Spin,
    Steps,
    Tag,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { useNavigate, useParams } from 'react-router-dom';
import useAuth from '../hooks/useAuth';
import GeneralPath from '../routes/GeneralPath';

const { Title, Text, Paragraph } = Typography;
const { Step } = Steps;

const OrderPayment = () => {
    const { isAuthenticated, user, loading, initialized } = useAuth()
    const { orderId } = useParams();
    const navigate = useNavigate();
    const [loadingData, setLoading] = useState(true);
    const [order, setOrder] = useState(null);
    const [paymentStatus, setPaymentStatus] = useState('pending');

    useEffect(() => {
        if (loading) {
            return;
        }
        // if (initialized && !loading && !user && !isAuthenticated && !isLoggingOut) {
        //     navigate('/404', { replace: true });
        //     return;
        // }
        const fetchOrderDetails = async () => {
            try {
                setLoading(true);
                const path = !loading && isAuthenticated && user ? GeneralPath.GET_ORDER_ENDPOINT : GeneralPath.GET_ORDER_GUEST_ENDPOINT;
                const response = await api.get(path + `/${orderId}`);
                if (response && response?.data) {
                    setOrder(response?.data);
                    setPaymentStatus(response.data.payment_status || 'pending');
                    console.log(response?.data)
                } else {
                    message.error('Không tìm thấy đơn hàng');
                    navigate('/404', { replace: true });
                    return;
                }
            } catch (error) {
                message.error('Có lỗi xảy ra khi tải thông tin đơn hàng');
            } finally {
                setLoading(false);
            }
        };

        if (orderId) {
            fetchOrderDetails();
        }

        // }
    }, [orderId, navigate, isAuthenticated, loading, initialized, user]);

    useEffect(() => {
        let statusChecker;
        if (order?.payment_method === 'ONLINE' && paymentStatus === 'pending') {
            statusChecker = setInterval(async () => {
                try {
                    const response = await api.get(`/payment-status/${order.id}`);
                    if (response?.data?.payment_status && response.data.payment_status !== 'pending') {
                        console.log('Payment status changed from pending to:', response.data.payment_status);
                        setPaymentStatus(response.data.payment_status);
                        clearInterval(statusChecker);
                        if (response.data.order) {
                            setOrder(prevOrder => ({
                                ...prevOrder,
                                ...response.data.order
                            }));
                        }
                    }
                } catch (error) {
                    console.error('Error checking payment status:', error);
                }
            }, 3000);
        }

        return () => {
            if (statusChecker) {
                console.log('Clearing payment status checker...');
                clearInterval(statusChecker);
            }
        };
    }, [order?.id, order?.payment_method]);

    // Format price
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    // Handle view orders
    const handleViewOrders = () => {
        navigate('/profile?next_to=order');
    };

    if (loadingData) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <Spin size="large" />
            </div>
        );
    }

    if (!order) {
        return (
            <div className="min-h-screen flex items-center justify-center">
                <Card>
                    <div className="text-center">
                        <Title level={4}>Không tìm thấy đơn hàng</Title>
                        <Button type="primary" onClick={() => navigate('/')}>
                            Về trang chủ
                        </Button>
                    </div>
                </Card>
            </div>
        );
    }

    // Render payment content based on payment method and status
    const renderPaymentContent = () => {
        if (order?.status === 'cancelled') {
            return (
                <Card className="mb-6">
                    <div className="text-center py-8">
                        <ClockCircleOutlined style={{ fontSize: 64, color: '#ff4d4f', marginBottom: 16 }} />
                        <Title level={3} style={{ color: '#ff4d4f', marginBottom: 8 }}>
                            Đơn hàng đã bị hủy
                        </Title>
                        <Paragraph className="text-lg text-gray-600 mb-6">
                            Đơn hàng của bạn đã bị hủy. Vui lòng liên hệ bộ phận hỗ trợ nếu bạn cần trợ giúp.
                        </Paragraph>
                    </div>
                </Card>
            );
        }
        if (paymentStatus === 'failed') {
            return (
                <Card className="mb-6">
                    <div className="text-center py-8">
                        <ClockCircleOutlined style={{ fontSize: 64, color: '#ff4d4f', marginBottom: 16 }} />
                        <Title level={3} style={{ color: '#ff4d4f', marginBottom: 8 }}>
                            Thanh toán thất bại
                        </Title>
                        <Paragraph className="text-lg text-gray-600 mb-6">
                            Có lỗi xảy ra trong quá trình thanh toán. Vui lòng thử lại.
                        </Paragraph>
                    </div>
                </Card>
            );
        }

        // Online payment
        if (paymentStatus === 'paid') {
            return (
                <Card className="mb-6">
                    <div className="text-center py-8">
                        <CheckCircleOutlined style={{ fontSize: 64, color: '#52c41a', marginBottom: 16 }} />
                        <Title level={3} style={{ color: '#52c41a', marginBottom: 8 }}>
                            Thanh toán thành công!
                        </Title>
                        <Paragraph className="text-lg text-gray-600">
                            Cảm ơn bạn đã thanh toán. Đơn hàng của bạn đang được xử lý.
                        </Paragraph>
                    </div>
                </Card>
            );
        }

        if (order.payment_method === 'COD') {
            return (
                <Card className="mb-6">
                    <div className="text-center py-8">
                        <CheckCircleOutlined style={{ fontSize: 64, color: '#52c41a', marginBottom: 16 }} />
                        <Title level={3} style={{ color: '#52c41a', marginBottom: 8 }}>
                            Đặt hàng thành công!
                        </Title>
                        <Paragraph className="text-lg text-gray-600 mb-6">
                            Cảm ơn bạn đã đặt hàng. Đơn hàng của bạn sẽ được giao trong thời gian sớm nhất.
                        </Paragraph>
                        <Alert
                            message="Thanh toán khi nhận hàng (COD)"
                            description="Bạn sẽ thanh toán bằng tiền mặt khi nhận được hàng từ nhân viên giao hàng."
                            type="info"
                            showIcon
                            className="mb-6"
                        />
                    </div>
                </Card>
            );
        }

        return (
            <Card className="mb-6 mt-4">
                <div className="text-center mb-6">
                    <QrcodeOutlined style={{ fontSize: 48, color: '#1890ff', marginBottom: 16 }} />
                    <Title level={3} style={{ marginBottom: 8 }}>
                        Thanh toán online
                    </Title>
                    <Paragraph className="text-lg text-gray-600">
                        Vui lòng quét mã QR để thanh toán trong thời gian quy định
                    </Paragraph>
                </div>

                {order?.payment_url && (
                    <Row gutter={24} align="center">
                        <Col xs={24} lg={12} className="text-center mb-6 lg:mb-0">
                            <Card size="small" className="inline-block">
                                <img src={order?.payment_url} alt={`QR Code for Order ${order.id}`} />
                            </Card>
                            <div className="mt-4">
                                <Text type="secondary" className="block">
                                    Quét mã QR bằng ứng dụng ngân hàng
                                </Text>
                            </div>
                        </Col>
                    </Row>

                )}
            </Card>
        );
    };

    return (
        <div className="bg-gray-50 min-h-screen py-12 mt-12">
            <div className="max-w-4xl mx-auto px-4">
                {/* Payment Status Steps */}
                {/*
                                <option value="confirmed" {{ request('status') == 'confirmed' ? 'selected' : '' }}>Đã xác nhận</option>
                                <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Đang xử lý</option>
                                <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Đã giao</option>
                                <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Hoàn thành</option>*/}
                <Card className="mb-6">
                    <Steps
                        current={
                            order?.status == 'cancelled' ? 1 :
                                order?.status === 'pending' ? 0 :
                                    order?.status === 'confirmed' ? 1 :
                                        order?.status === 'processing' ? 2 :
                                            order?.status === 'delivered' ? 3 : 4

                        }
                        status={
                            order?.status === 'cancelled' ? 'error' :
                                paymentStatus === 'failed' || paymentStatus === 'expired' ? 'error' :
                                    'process'
                        }
                    >
                        <Step
                            title="Đặt hàng"
                            description={
                                order?.status === 'pending' ? 'Đang chờ' :
                                    order?.status === 'cancelled' ? 'Đã hủy' :
                                        'Hoàn thành'
                            }
                        />
                        <Step
                            title={order?.payment_method == 'ONLINE' ? "Thanh toán" : "Xác nhận"}
                            description={
                                order?.status === 'pending' ? 'Chờ xác nhận' :
                                    order?.status === 'confirmed' && order?.payment_method == 'ONLINE' && paymentStatus === 'pending' ? 'Chờ thanh toán' :
                                        order?.status === 'confirmed' && order?.payment_method == 'ONLINE' && paymentStatus === 'paid' ? 'Đã thanh toán' :
                                            order?.status === 'cancelled' ? 'Đã hủy' :
                                                paymentStatus === 'failed' ? 'Thanh toán thất bại' :
                                                    paymentStatus === 'expired' ? 'Hết hạn thanh toán' :
                                                        order?.status === 'processing' || order?.status === 'delivered' || order?.status === 'completed' ? 'Hoàn thành' :
                                                            'Chờ xử lý'
                            }
                        />
                        <Step
                            title="Xử lý đơn hàng"
                            description={
                                order?.status === 'processing' ? 'Đang xử lý' :
                                    order?.status === 'delivered' || order?.status === 'completed' ? 'Hoàn thành' :
                                        order?.status === 'cancelled' ? 'Đã hủy' :
                                            'Chờ xử lý'
                            }
                        />
                        <Step
                            title="Giao hàng"
                            description={
                                order?.status === 'delivered' ? 'Đã giao hàng' :
                                    order?.status === 'completed' && paymentStatus == 'paid' ? 'Hoàn thành' :
                                        order?.status === 'cancelled' ? 'Đã hủy' :
                                            'Chờ giao hàng'
                            }
                        />

                        <Step
                            title="Hoàn thành"
                            description={
                                order?.status === 'completed' ? 'Hoàn thành' :
                                    order?.status === 'cancelled' ? 'Đã hủy' : 'Chờ hoàn thành'
                            }
                        />
                    </Steps>
                </Card>

                {/* Payment Content */}
                {renderPaymentContent()}

                {/* Order Information */}
                <Row gutter={24}>
                    <Col xs={24} lg={16}>
                        <Card title="Chi tiết đơn hàng" className="mb-6">
                            <Descriptions column={1} bordered size="small">
                                <Descriptions.Item label="Mã đơn hàng">
                                    <Text strong>#{order.order_number}</Text>
                                </Descriptions.Item>
                                <Descriptions.Item label="Ngày đặt">
                                    {new Date(order.created_at).toLocaleString('vi-VN')}
                                </Descriptions.Item>
                                <Descriptions.Item label="Phương thức thanh toán">
                                    <Tag color={order.payment_method === 'COD' ? 'orange' : 'blue'}>
                                        {order.payment_method === 'COD' ? 'COD' : 'Chuyển khoản'}
                                    </Tag>
                                </Descriptions.Item>
                                <Descriptions.Item label="Trạng thái thanh toán">
                                    <Tag color={
                                        paymentStatus === 'paid' ? 'success' :
                                            paymentStatus === 'pending' ? 'processing' :
                                                'error'
                                    }>
                                        {
                                            paymentStatus === 'paid' ? 'Đã thanh toán' :
                                                paymentStatus === 'pending' ? 'Chờ thanh toán' :
                                                    paymentStatus === 'failed' ? 'Thanh toán thất bại' :
                                                        paymentStatus === 'expired' ? 'Hết hạn' :
                                                            'Không xác định'
                                        }
                                    </Tag>
                                </Descriptions.Item>
                            </Descriptions>
                        </Card>
                    </Col>

                    <Col xs={24} lg={8}>
                        <Card title="Sản phẩm đã đặt" className="mb-6">
                            <List
                                itemLayout="horizontal"
                                dataSource={order.items || []}
                                renderItem={item => (
                                    <List.Item>
                                        <div className="flex w-full">
                                            <div className="w-12 h-12 border rounded overflow-hidden mr-3 flex-shrink-0">
                                                <img src={item.product?.main_image_url} alt="" className="w-full h-full object-cover" />
                                            </div>
                                            <div className="flex-grow">
                                                <div className="font-medium text-sm line-clamp-2">
                                                    {item.product_name}
                                                </div>
                                                <div className="text-xs text-gray-500">
                                                    SL:  {item.quantity}
                                                </div>

                                                <Text strong className="text-sm">{formatPrice(item?.price * item.quantity)}</Text>
                                                {+item?.pv_sales !== +item?.price && (
                                                    <div>
                                                        <Text delete type="secondary" className="text-xs">
                                                            {formatPrice(item.pv_sales * item.quantity)}
                                                        </Text>
                                                    </div>
                                                )}
                                            </div>
                                        </div>
                                    </List.Item>
                                )}
                            />

                            <Divider style={{ margin: '12px 0' }} />
                            {console.log(order)}
                            <div className="space-y-2">
                                {order.discount > 0 && (
                                    <div className="flex justify-between text-green-600">
                                        <Text type="danger">Giảm giá:</Text>
                                        <Text type="danger">-{formatPrice(order.discount)}</Text>
                                    </div>
                                )}
                                {order.system_discount > 0 && (
                                    <div className="flex justify-between text-green-600">
                                        <Text type="danger">Hệ thống giảm giá:</Text>
                                        <Text type="danger">-{formatPrice(order.system_discount)}</Text>
                                    </div>
                                )}
                                <Divider style={{ margin: '8px 0' }} />
                                <div className="flex justify-between font-semibold">
                                    <Text strong>Tổng cộng:</Text>
                                    <Text strong style={{ color: '#ff4d4f', fontSize: '16px' }}>
                                        {formatPrice(order.total)}
                                    </Text>
                                </div>
                            </div>
                        </Card>

                        {/* Action Buttons */}
                        {!loading && isAuthenticated && user && order.user_id === user.id && (
                            <Card size="small">
                                <Space direction="vertical" className="w-full">
                                    <Button
                                        type="primary"
                                        block
                                        onClick={handleViewOrders}
                                        style={{ background: '#000', borderColor: '#000' }}
                                    >
                                        Xem đơn hàng của tôi
                                    </Button>
                                </Space>
                            </Card>
                        )}
                    </Col>
                </Row>
            </div>

            <style jsx>{`
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

export default OrderPayment;
