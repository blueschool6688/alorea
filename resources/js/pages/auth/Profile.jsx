import FilterOrder from '@/components/FilterOrder';
import { useAuth } from '@/hooks/useAuth';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import {
    EditOutlined,
    ExclamationCircleOutlined,
    EyeOutlined,
    LockOutlined,
    LogoutOutlined,
    MenuOutlined,
    ShoppingOutlined,
    UserOutlined
} from '@ant-design/icons';
import {
    Avatar,
    Badge,
    Button,
    Card,
    Col,
    Descriptions,
    Divider,
    Drawer,
    Form,
    Input,
    message,
    Modal,
    Row,
    Skeleton,
    Statistic,
    Table,
    Tabs,
    Tag,
    Typography
} from 'antd';
import { useCallback, useEffect, useMemo, useRef, useState } from 'react';
import { useLocation, useNavigate } from 'react-router-dom';

const { Title, Paragraph, Text } = Typography;
const { confirm } = Modal;

const Profile = () => {
    const { user, logout, updateUserData, isAuthenticated, loading, initialized } = useAuth();
    const [activeTab, setActiveTab] = useState('profile');
    const [orders, setOrders] = useState([]);
    const [loadingOrders, setLoadingOrders] = useState(false);
    const [editMode, setEditMode] = useState(false);
    const [isLoggingOut, setIsLoggingOut] = useState(false);
    const [changePasswordVisible, setChangePasswordVisible] = useState(false);
    const [mobileMenuVisible, setMobileMenuVisible] = useState(false);
    const [isMobile, setIsMobile] = useState(false);
    const navigate = useNavigate();
    const location = useLocation();
    const [avatarFile, setAvatarFile] = useState(null);
    const [avatarPreview, setAvatarPreview] = useState('');
    const [filters, setFilters] = useState({
        search: '',
        status: 'all',
        dateRange: null
    });
    const [paginationInfo, setPaginationInfo] = useState({
        current: 1,
        pageSize: 10,
        total: 0
    });
    const [isFirstLoad, setIsFirstLoad] = useState(true);
    const isApiCallInProgress = useRef(false);
    const lastApiCallTime = useRef(0);
    const [form] = Form.useForm();
    const [passwordForm] = Form.useForm();

    useEffect(() => {
        const checkScreenSize = () => {
            setIsMobile(window.innerWidth < 768);
        };

        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);

        // Cleanup function
        return () => {
            window.removeEventListener('resize', checkScreenSize);
            // Clear any pending filter search timeout
            if (timeoutRef.current) {
                clearTimeout(timeoutRef.current);
            }
        };
    }, []);

    useEffect(() => {
        if (activeTab === 'orders' && isFirstLoad) {
            fetchOrders();
            setIsFirstLoad(false);
        }
    }, [activeTab, isFirstLoad]);

    // Check query parameter to set active tab
    useEffect(() => {
        const searchParams = new URLSearchParams(location.search);
        const nextTo = searchParams.get('next_to');

        if (nextTo === 'order') {
            setActiveTab('orders');
        }
    }, [location.search]);

    useEffect(() => {
        if (initialized && !loading && !user && !isAuthenticated && !isLoggingOut) {
            navigate('/404', { replace: true });
        }
    }, [isAuthenticated, user, loading, initialized, isLoggingOut, navigate]);

    const fetchOrders = useCallback(async (filterParams = null, page = null, pageSize = null) => {
        const now = Date.now();

        if (isApiCallInProgress.current) {
            console.log('API call blocked - already in progress');
            return;
        }

        if (now - lastApiCallTime.current < 500) {
            console.log('API call blocked - too soon after last call');
            return;
        }

        isApiCallInProgress.current = true;
        lastApiCallTime.current = now;
        setLoadingOrders(true);
        try {
            const params = new URLSearchParams();

            const currentPage = page || paginationInfo.current;
            const currentPageSize = pageSize || paginationInfo.pageSize;
            const currentFilters = filterParams || filters;

            params.append('page', currentPage.toString());
            params.append('per_page', currentPageSize.toString());

            if (currentFilters?.search?.trim()) {
                params.append('search', currentFilters.search.trim());
            }

            if (currentFilters?.status && currentFilters.status !== 'all') {
                params.append('status', currentFilters.status);
            }

            if (currentFilters?.dateRange && currentFilters.dateRange.length === 2) {
                params.append('startDate', currentFilters.dateRange[0].format('YYYY-MM-DD'));
                params.append('endDate', currentFilters.dateRange[1].format('YYYY-MM-DD'));
            }

            const response = await api.get(GeneralPath.GET_ORDERS_DATA_ENDPOINT + '?' + params.toString());
            if (response && response?.data) {
                const { data, current_page, per_page, total, last_page } = response.data;
                setOrders(data || []);
                setPaginationInfo({
                    current: current_page,
                    pageSize: per_page,
                    total: total,
                    lastPage: last_page
                });
                if (filterParams) {
                    setFilters(filterParams);
                }
            }
        } catch (error) {
            message.error(error.message);
        } finally {
            setLoadingOrders(false);
            isApiCallInProgress.current = false;
        }
    }, [filters, paginationInfo, setOrders, setPaginationInfo, setFilters]);

    const handleTableChange = useCallback((pagination) => {
        const { current, pageSize: newPageSize } = pagination;
        if (current !== paginationInfo.current || newPageSize !== paginationInfo.pageSize) {
            fetchOrders(filters, current, newPageSize);
        }
    }, [fetchOrders, filters, paginationInfo]);

    // Debounced filter search with proper cleanup
    const timeoutRef = useRef(null);

    const handleFilterSearch = useCallback((newFilters) => {
        setIsFirstLoad(false);
        if (timeoutRef.current) {
            clearTimeout(timeoutRef.current);
        }
        fetchOrders(newFilters, 1, paginationInfo.pageSize);
    }, [fetchOrders, paginationInfo.pageSize]);
    const handleLogout = () => {
        confirm({
            title: 'Bạn có chắc muốn đăng xuất?',
            icon: <ExclamationCircleOutlined />,
            content: 'Nhấn OK để xác nhận đăng xuất.',
            onOk() {
                setIsLoggingOut(true);
                return logout().then(() => {
                    navigate('/', { replace: true });
                }).catch(() => {
                    setIsLoggingOut(false);
                });
            },
        });
    };

    const handleTabChange = useCallback((key) => {
        if (key === 'logout') {
            handleLogout();
            return;
        }
        setActiveTab(key);
        if (isMobile) {
            setMobileMenuVisible(false);
        }
    }, [isMobile, handleLogout]);




    const handleEditProfile = useCallback(() => {
        setEditMode(true);
        form.setFieldsValue({
            name: user?.name || '',
            email: user?.email || '',
            phone: user?.phone || '',
            address: user?.address || '',
        });
    }, [form, user]);

    const handleSaveProfile = useCallback(async (values) => {
        const formData = new FormData();
        formData.append('name', values.name);
        formData.append('email', values.email);
        formData.append('phone', values.phone || '');
        formData.append('address', values.address || '');
        if (avatarFile) {
            formData.append('avatar', avatarFile);
        }
        try {
            const response = await api.post(GeneralPath.UPDATE_PROFILE_ENDPOINT, formData, {
                headers: { 'Content-Type': 'multipart/form-data' }
            });
            if (response?.data) {
                message.success(response?.message);
                await updateUserData();
                setEditMode(false);
            }
        } catch (error) {
            console.error('Error updating profile:', error);
            message.error('Không thể cập nhật thông tin');
        }
    }, [avatarFile, form]);

    const handleChangePassword = useCallback(async (values) => {
        try {
            const response = await api.post(GeneralPath.CHANGE_PASSWORD_ENDPOINT, values);
            if (response?.data) {
                message.success(response?.message);
                setChangePasswordVisible(false);
                passwordForm.resetFields();
            }
        } catch (error) {
            console.error('Error changing password:', error);
            message.error('Không thể đổi mật khẩu. Vui lòng kiểm tra lại thông tin');
        }
    }, [passwordForm]);

    // Memoized utility functions
    const formatPrice = useCallback((price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    }, []);

    const orderStatusColor = useCallback((status) => {
        const colors = {
            'pending': 'gold',
            'paid': 'green',
            'failed': 'red'
        };
        return colors[status] || 'default';
    }, []);

    const orderStatusText = useCallback((status) => {
        const statusNames = {
            'pending': 'Đang chờ',
            'paid': 'Đã thanh toán',
            'failed': 'Đã hủy',
        };
        return statusNames[status] || status;
    }, []);

    // Memoized order statistics to prevent recalculation
    const orderStats = useMemo(() => {
        if (!orders?.length) {
            return { total: 0, pending: 0, paid: 0, failed: 0, totalAmount: 0 };
        }

        return {
            total: orders.length,
            pending: orders.filter(o => o.payment_status === 'pending').length,
            paid: orders.filter(o => o.payment_status === 'paid').length,
            failed: orders.filter(o => o.payment_status === 'failed').length,
            totalAmount: orders.reduce((sum, order) => sum + (+order.total || 0), 0)
        };
    }, [orders]);

    // Memoized table columns to prevent recreation
    const orderColumns = useMemo(() => [
        {
            title: 'Mã đơn hàng',
            dataIndex: 'order_number',
            key: 'order_number',
            width: isMobile ? 120 : 150,
            render: (text) => (
                <Text strong className="text-xs sm:text-sm">
                    {isMobile ? `#${text.slice(-6)}` : `#${text}`}
                </Text>
            ),
        },
        {
            title: 'Ngày đặt',
            dataIndex: 'created_at',
            key: 'created_at',
            width: isMobile ? 100 : 120,
            render: (date) => (
                <Text className="text-xs sm:text-sm">
                    {new Date(date).toLocaleDateString('vi-VN', {
                        day: '2-digit',
                        month: '2-digit',
                        year: isMobile ? '2-digit' : 'numeric'
                    })}
                </Text>
            ),
        },
        {
            title: 'Tổng tiền',
            dataIndex: 'total',
            key: 'total',
            width: isMobile ? 100 : 120,
            render: (total) => (
                <Text strong type="danger" className="text-xs sm:text-sm">
                    {isMobile
                        ? `${(total / 1000000).toFixed(1)}M`
                        : formatPrice(total)
                    }
                </Text>
            ),
        },
        {
            title: 'Trạng thái',
            dataIndex: 'payment_status',
            key: 'payment_status',
            width: isMobile ? 80 : 100,
            render: (payment_status) => (
                <Tag
                    color={orderStatusColor(payment_status)}
                    className="text-xs"
                >
                    {isMobile
                        ? (payment_status === 'pending' ? 'Chờ thanh toán' :
                            payment_status === 'paid' ? 'Đã thanh toán' : 'Hủy')
                        : orderStatusText(payment_status)
                    }
                </Tag>
            ),
        },
        {
            title: 'Xem',
            key: 'action',
            width: isMobile ? 60 : 80,
            render: (_, record) => (
                <Button
                    icon={<EyeOutlined />}
                    onClick={() => navigate(`/order/${record?.order_number.split("ORD-")[1]}`)}
                    size={isMobile ? "small" : "middle"}
                    type="link"
                    className="p-0"
                />
            ),
        },
    ], [isMobile, navigate]);
    const handleAvatarInputChange = useCallback((e) => {
        const file = e.target.files[0];
        if (file) {
            setAvatarFile(file);
            setAvatarPreview(URL.createObjectURL(file));
        }
    }, []);

    // Cleanup avatar preview URL on unmount
    useEffect(() => {
        return () => {
            if (avatarPreview) {
                URL.revokeObjectURL(avatarPreview);
            }
        };
    }, [avatarPreview]);
    // Memoized menu items
    const menuItems = useMemo(() => [
        {
            key: 'profile',
            label: 'Thông tin tài khoản',
            icon: <UserOutlined />
        },
        {
            key: 'orders',
            label: 'Đơn hàng của tôi',
            icon: <ShoppingOutlined />
        },
        {
            key: 'logout',
            label: 'Đăng xuất',
            icon: <LogoutOutlined />,
            className: 'text-red-500'
        }
    ], []);

    // Memoized Profile content component
    const ProfileContent = useCallback(() => (
        <div className="p-4 sm:p-6">
            {!editMode ? (
                <div>
                    <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                        <Title level={4} className="!mb-0">Thông tin cá nhân</Title>
                        <Button
                            icon={<EditOutlined />}
                            onClick={handleEditProfile}
                            className="w-full sm:w-auto"
                        >
                            Sửa thông tin
                        </Button>
                    </div>

                    <div className="flex flex-col items-center mb-6">
                        <Avatar
                            size={isMobile ? 80 : 100}
                            icon={<UserOutlined />}
                            src={user?.avatar_url}
                            className="mb-3 bg-gray-800"
                        />
                        <Text className="text-lg font-medium text-center">{user?.name}</Text>
                        <Text type="secondary" className="text-center text-sm">
                            Thành viên từ {new Date(user?.created_at).toLocaleDateString('vi-VN')}
                        </Text>
                    </div>

                    <div className="w-full">
                        <Descriptions
                            bordered
                            column={1}
                            size="small"
                            labelStyle={{
                                width: isMobile ? 100 : 150,
                                fontWeight: 500,
                                backgroundColor: '#fafafa',
                                fontSize: isMobile ? '12px' : '14px'
                            }}
                            contentStyle={{
                                backgroundColor: '#ffffff',
                                fontSize: isMobile ? '12px' : '14px'
                            }}
                        >
                            <Descriptions.Item label="Họ và tên">{user?.name || 'Chưa cập nhật'}</Descriptions.Item>
                            <Descriptions.Item label="Email">{user?.email}</Descriptions.Item>
                            <Descriptions.Item label="Số điện thoại">{user?.phone || 'Chưa cập nhật'}</Descriptions.Item>
                            <Descriptions.Item label="Địa chỉ">{user?.address || 'Chưa cập nhật'}</Descriptions.Item>
                        </Descriptions>

                        <Button
                            icon={<LockOutlined />}
                            onClick={() => setChangePasswordVisible(true)}
                            className="mt-6 w-full sm:w-auto"
                        >
                            Đổi mật khẩu
                        </Button>
                    </div>
                </div>
            ) : (
                <div>
                    <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                        <Title level={4} className="!mb-0">Chỉnh sửa thông tin</Title>
                        <Button onClick={() => setEditMode(false)} className="w-full sm:w-auto">
                            Hủy
                        </Button>
                    </div>

                    <Form
                        form={form}
                        layout="vertical"
                        onFinish={handleSaveProfile}
                        initialValues={{
                            name: user?.name || '',
                            email: user?.email || '',
                            phone: user?.phone || '',
                            address: user?.address || '',
                        }}
                        encType="multipart/form-data"
                    >
                        <div className="grid grid-cols-1 gap-4">
                            <Form.Item label="Ảnh đại diện" name="avatar">
                                <input
                                    type="file"
                                    accept="image/*"
                                    onChange={handleAvatarInputChange}
                                    style={{ marginBottom: 8 }}
                                />
                                <Avatar
                                    size={isMobile ? 80 : 100}
                                    src={avatarPreview || user?.avatar_url}
                                    icon={<UserOutlined />}
                                    className="mb-2 bg-gray-800"
                                />
                            </Form.Item>
                            <Form.Item
                                name="name"
                                label="Họ và tên"
                                rules={[{ required: true, message: 'Vui lòng nhập họ tên' }]}
                            >
                                <Input placeholder="Nhập họ tên" />
                            </Form.Item>

                            <Form.Item
                                name="email"
                                label="Email"
                                rules={[{ required: true, message: 'Vui lòng nhập email' }]}
                            >
                                <Input placeholder="Nhập email" />
                            </Form.Item>

                            <Form.Item
                                name="phone"
                                label="Số điện thoại"
                                rules={[{ pattern: /^[0-9]{10,11}$/, message: 'Số điện thoại không hợp lệ' }]}
                            >
                                <Input placeholder="Nhập số điện thoại" />
                            </Form.Item>

                            <Form.Item
                                name="address"
                                label="Địa chỉ"
                            >
                                <Input.TextArea placeholder="Nhập địa chỉ" rows={4} />
                            </Form.Item>
                        </div>

                        <Form.Item className="mt-4">
                            <Button
                                type="primary"
                                htmlType="submit"
                                loading={loading}
                                style={{ background: "black", borderColor: "black" }}
                                className="w-full sm:w-auto"
                            >
                                Lưu thay đổi
                            </Button>
                        </Form.Item>
                    </Form>
                </div>
            )}
        </div>
    ), [editMode, user, isMobile, loading, form, handleEditProfile, handleSaveProfile, avatarFile, avatarPreview, handleAvatarInputChange]);

    // Memoized Orders content component
    const OrdersContent = useCallback(() => (
        <div className="p-4 sm:p-6">
            <div className="flex flex-col sm:flex-row sm:justify-between sm:items-center mb-6 gap-4">
                <Title level={4} className="!mb-0">Lịch sử đơn hàng</Title>
            </div>

            {loadingOrders ? (
                <Skeleton active />
            ) : (
                <>
                    {/* Order Statistics */}
                    <Row gutter={[16, 16]} className="mb-6">
                        <Col xs={12} sm={6}>
                            <Card size="small" className="text-center">
                                <Statistic
                                    title="Tổng đơn"
                                    value={orderStats.total}
                                    valueStyle={{ fontSize: isMobile ? 18 : 24, color: '#1890ff' }}
                                />
                            </Card>
                        </Col>
                        <Col xs={12} sm={6}>
                            <Card size="small" className="text-center">
                                <Statistic
                                    title="Đã thanh toán"
                                    value={orderStats.paid}
                                    valueStyle={{ fontSize: isMobile ? 18 : 24, color: '#52c41a' }}
                                />
                            </Card>
                        </Col>
                        <Col xs={12} sm={6}>
                            <Card size="small" className="text-center">
                                <Statistic
                                    title="Chờ xử lý"
                                    value={orderStats.pending}
                                    valueStyle={{ fontSize: isMobile ? 18 : 24, color: '#faad14' }}
                                />
                            </Card>
                        </Col>
                        <Col xs={12} sm={6}>
                            <Card size="small" className="text-center">
                                <Statistic
                                    title="Tổng tiền"
                                    value={orderStats.totalAmount}
                                    formatter={(value) => formatPrice(value)}
                                    valueStyle={{ fontSize: isMobile ? 14 : 18, color: '#f5222d' }}
                                />
                            </Card>
                        </Col>
                    </Row>

                    <FilterOrder
                        onSearch={handleFilterSearch}
                        filters={filters}
                        setFilters={setFilters}
                        loading={loadingOrders}
                    />

                    {
                        isMobile ? (
                            // Mobile: Card List View
                            <div className="space-y-4">
                                {orders.length > 0 ? (
                                    orders.map((order) => (
                                        <Card key={order.id} size="small" className="border border-gray-200">
                                            <div className="flex justify-between items-start mb-2">
                                                <div>
                                                    <Text strong className="text-sm">#{order.order_number.slice(-6)}</Text>
                                                    <br />
                                                    <Text type="secondary" className="text-xs">
                                                        {new Date(order.created_at).toLocaleDateString('vi-VN')}
                                                    </Text>
                                                </div>
                                                <Tag color={orderStatusColor(order.payment_status)} className="text-xs">
                                                    {order.payment_status === 'pending' ? 'Chờ thanh toán' :
                                                        order.payment_status === 'paid' ? 'Đã thanh toán' : 'Hủy'}
                                                </Tag>
                                            </div>
                                            <div className="flex justify-between items-center">
                                                <Text strong type="danger" className="text-sm">
                                                    {formatPrice(order.total)}
                                                </Text>
                                                <Button
                                                    icon={<EyeOutlined />}
                                                    size="small"
                                                    type="link"
                                                    onClick={() => navigate(`/order/${order.id}`)}
                                                >
                                                    Xem
                                                </Button>
                                            </div>
                                        </Card>
                                    ))
                                ) : (
                                    <Card className="text-center py-8">
                                        <div className="flex flex-col items-center justify-center space-y-3">
                                            <ShoppingOutlined className="text-4xl text-gray-300" />
                                            <Text type="secondary" className="text-base">
                                                Chưa có đơn hàng nào
                                            </Text>
                                            <Text type="secondary" className="text-sm">
                                                Hãy khám phá và mua sắm những sản phẩm yêu thích
                                            </Text>
                                            <Button
                                                type="primary"
                                                onClick={() => navigate('/')}
                                                className="mt-2"
                                                style={{ background: "black", borderColor: "black" }}
                                            >
                                                Mua sắm ngay
                                            </Button>
                                        </div>
                                    </Card>
                                )}
                            </div>
                        ) : (
                            // Desktop: Table View
                            <div className="overflow-x-auto">
                                <Table
                                    dataSource={orders}
                                    columns={orderColumns}
                                    rowKey="id"
                                    pagination={{
                                        current: paginationInfo.current,
                                        pageSize: paginationInfo.pageSize,
                                        total: paginationInfo.total,
                                        showSizeChanger: true,
                                        showQuickJumper: true,
                                        showTotal: (total, range) =>
                                            `${range[0]}-${range[1]} của ${total} đơn hàng`,
                                        pageSizeOptions: ['5', '10', '20', '50'],
                                        onChange: (page, size) => {
                                            handleTableChange({ current: page, pageSize: size });
                                        },
                                        onShowSizeChange: (current, size) => {
                                            handleTableChange({ current: 1, pageSize: size });
                                        }
                                    }}
                                    scroll={{ x: 600 }}
                                    size="middle"
                                    className="border border-gray-200"
                                />
                            </div>
                        )
                    }
                </>
            )}
        </div>
    ), [loadingOrders, orders, orderStats, isMobile, filters, setFilters, handleFilterSearch, orderColumns, handleTableChange, paginationInfo, navigate, formatPrice, orderStatusColor]);

    return (
        <div className="bg-white min-h-screen py-6 sm:py-12 mt-12">
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="mb-8 sm:mb-12 text-center">
                    <Title level={2} className="!font-serif !mb-2 text-xl sm:text-2xl lg:text-3xl">
                        TÀI KHOẢN CỦA BẠN
                    </Title>
                    <Divider className="max-w-md mx-auto" />
                    <Paragraph className="text-gray-600 max-w-lg mx-auto !font-sans text-sm sm:text-base">
                        Quản lý thông tin cá nhân và theo dõi đơn hàng của bạn
                    </Paragraph>
                </div>

                {/* Mobile Menu Button */}
                {isMobile && (
                    <div className="mb-4">
                        <Button
                            icon={<MenuOutlined />}
                            onClick={() => setMobileMenuVisible(true)}
                            className="w-full flex items-center justify-center"
                        >
                            Menu tài khoản
                        </Button>
                    </div>
                )}

                {/* Desktop Layout */}
                {!isMobile ? (
                    <Card
                        className="shadow-sm border border-gray-100"
                        bodyStyle={{ padding: 0 }}
                    >
                        <Tabs
                            activeKey={activeTab}
                            onChange={handleTabChange}
                            tabPosition="left"
                            className="min-h-[600px]"
                            tabBarStyle={{
                                width: 200,
                                borderRight: '1px solid #f0f0f0',
                                paddingTop: 20
                            }}
                            items={[
                                {
                                    key: 'profile',
                                    label: (
                                        <span className="flex items-center">
                                            <UserOutlined />
                                            <span className="ml-2">Thông tin tài khoản</span>
                                        </span>
                                    ),
                                    children: <ProfileContent />
                                },
                                {
                                    key: 'orders',
                                    label: (
                                        <span className="flex items-center">
                                            <ShoppingOutlined />
                                            <span className="ml-2">Đơn hàng của tôi</span>
                                            {orders.length > 0 && (
                                                <Badge count={orders.length} size="small" className="ml-1" />
                                            )}
                                        </span>
                                    ),
                                    children: <OrdersContent />
                                },
                                {
                                    key: 'logout',
                                    label: (
                                        <span
                                            className="flex items-center text-red-500"
                                        // onClick={handleLogout}
                                        >
                                            <LogoutOutlined />
                                            <span className="ml-2">Đăng xuất</span>
                                        </span>
                                    ),
                                }
                            ]}
                        />
                    </Card>
                ) : (
                    // Mobile Layout
                    <Card className="shadow-sm border border-gray-100">
                        {activeTab === 'profile' && <ProfileContent />}
                        {activeTab === 'orders' && <OrdersContent />}
                    </Card>
                )}

                {/* Mobile Drawer Menu */}
                <Drawer
                    title="Menu tài khoản"
                    placement="left"
                    onClose={() => setMobileMenuVisible(false)}
                    open={mobileMenuVisible}
                    width={280}
                >
                    <div className="space-y-2">
                        {menuItems.map(item => (
                            <Button
                                key={item.key}
                                icon={item.icon}
                                onClick={() => handleTabChange(item.key)}
                                className={`w-full text-left justify-start ${activeTab === item.key ? 'bg-gray-100' : ''
                                    } ${item.className || ''}`}
                                type={activeTab === item.key ? 'default' : 'text'}
                            >
                                <span className="flex items-center justify-between w-full">
                                    <span>{item.label}</span>
                                    {item.key === 'orders' && orders.length > 0 && (
                                        <Badge count={orders.length} size="small" />
                                    )}
                                </span>
                            </Button>
                        ))}
                    </div>
                </Drawer>
            </div>

            {/* Change Password Modal */}
            <Modal
                title="Đổi mật khẩu"
                open={changePasswordVisible}
                onCancel={() => setChangePasswordVisible(false)}
                footer={null}
                width={isMobile ? '95%' : 520}
            >
                <Form
                    form={passwordForm}
                    layout="vertical"
                    onFinish={handleChangePassword}
                >
                    <Form.Item
                        name="current_password"
                        label="Mật khẩu hiện tại"
                        rules={[{ required: true, message: 'Vui lòng nhập mật khẩu hiện tại' }]}
                    >
                        <Input.Password placeholder="Nhập mật khẩu hiện tại" />
                    </Form.Item>

                    <Form.Item
                        name="password"
                        label="Mật khẩu mới"
                        rules={[
                            { required: true, message: 'Vui lòng nhập mật khẩu mới' },
                            { min: 8, message: 'Mật khẩu phải có ít nhất 8 ký tự' }
                        ]}
                    >
                        <Input.Password placeholder="Nhập mật khẩu mới" />
                    </Form.Item>

                    <Form.Item
                        name="password_confirmation"
                        label="Xác nhận mật khẩu mới"
                        rules={[
                            { required: true, message: 'Vui lòng xác nhận mật khẩu mới' },
                            ({ getFieldValue }) => ({
                                validator(_, value) {
                                    if (!value || getFieldValue('password') === value) {
                                        return Promise.resolve();
                                    }
                                    return Promise.reject(new Error('Mật khẩu xác nhận không khớp!'));
                                },
                            }),
                        ]}
                    >
                        <Input.Password placeholder="Xác nhận mật khẩu mới" />
                    </Form.Item>

                    <div className="flex flex-col sm:flex-row sm:justify-end gap-2">
                        <Button
                            onClick={() => setChangePasswordVisible(false)}
                            className="w-full sm:w-auto"
                        >
                            Hủy
                        </Button>
                        <Button
                            type="primary"
                            htmlType="submit"
                            loading={loading}
                            style={{ background: "black", borderColor: "black" }}
                            className="w-full sm:w-auto"
                        >
                            Cập nhật mật khẩu
                        </Button>
                    </div>
                </Form>
            </Modal>
        </div>
    );
};

export default Profile;
