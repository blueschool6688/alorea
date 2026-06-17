import CartSidebar from "@/components/CartSidebar";
import ModalLogin from "@/components/ModalLogin";
import useAuth from "@/hooks/useAuth";
import { CloseOutlined, MenuOutlined, SearchOutlined, ShoppingCartOutlined } from "@ant-design/icons";
import { Badge, Button, Divider, Drawer, Menu, Space, Tooltip } from "antd";
import { useCallback, useEffect, useMemo, useState } from "react";
import { MdAccountCircle } from "react-icons/md";
import { Link, useLocation, useSearchParams } from "react-router-dom";




const HeaderVertical = ({ dispatch, navigate, cartCount, logo, scrollToContact }) => {
    const [showLoginModal, setShowLoginModal] = useState(false);
    const [mobileMenuOpen, setMobileMenuOpen] = useState(false);
    const [isMobile, setIsMobile] = useState(false);
    const [isScrolled, setIsScrolled] = useState(false);
    const location = useLocation();
    const [cartSidebarOpen, setCartSidebarOpen] = useState(false);
    const { isAuthenticated, user, login, loading } = useAuth();
    const [searchParams] = useSearchParams();
    const loginShow = searchParams.get("login_show") === "true";


    const MENU_ITEMS = [
        {
            label: <Link to="/">Trang chủ</Link>,
            key: "home"
        },
        {
            label: <Link to="/products">Sản phẩm</Link>,
            key: "product"
        },
        {
            label: <Link to="/collections">Bộ sưu tập</Link>,
            key: "collection"
        },
        {
            label: <Link to="/blogs">BLOG</Link>,
            key: "blog"
        },
        {
            label: <Link to="/about">Về chúng tôi</Link>,
            key: "about"
        },
        {
            label: (
                <span onClick={scrollToContact} style={{ cursor: "pointer" }}>
                    Liên hệ
                </span>
            ),
            key: "contact"
        },
    ];

    useEffect(() => {
        if (loginShow) {
            setShowLoginModal(true);
        }
    }, [loginShow]);
    // Detect screen size
    useEffect(() => {
        const checkScreenSize = () => {
            setIsMobile(window.innerWidth < 1024);
        };

        checkScreenSize();
        window.addEventListener('resize', checkScreenSize);

        return () => window.removeEventListener('resize', checkScreenSize);
    }, []);
    useEffect(() => {
        if (!isMobile && mobileMenuOpen) {
            setMobileMenuOpen(false);
        }
    }, [isMobile, mobileMenuOpen]);
    useEffect(() => {
        const handleScroll = () => {
            const scrollTop = window.pageYOffset || document.documentElement.scrollTop;
            setIsScrolled(scrollTop > 50);
        };

        window.addEventListener('scroll', handleScroll);
        return () => window.removeEventListener('scroll', handleScroll);
    }, []);



    const handleToggleLoginModal = useCallback(() => {
        setShowLoginModal(prev => !prev);
    }, []);

    const handleSearchClick = useCallback(() => {
        navigate('/search-order');
    }, []);

    const handleCartClick = useCallback(() => {
        setCartSidebarOpen(true)
    }, []);

    const handleMobileMenuToggle = useCallback(() => {
        setMobileMenuOpen(prev => !prev);
    }, []);

    const handleMobileMenuClose = useCallback(() => {
        setMobileMenuOpen(false);
    }, []);

    const logoComponent = useMemo(() => (
        <Link to="/" className="block">
            <img
                src={logo}
                alt="Logo"
                className="h-14 w-auto object-contain"
            />
        </Link>
    ), []);

    // Desktop action buttons - chỉ hiển thị trên desktop (≥1024px)
    const desktopActionButtons = useMemo(() => {
        return (
            <div className="hidden lg:block">
                <Space size="small" className="flex items-center">
                    {!loading && !isAuthenticated && !user && (
                        <Tooltip placement="top" title={'Tra cứu đơn hàng'}>
                            <Button
                                type="text"
                                icon={<SearchOutlined />}
                                onClick={handleSearchClick}
                                className={`p-2 rounded cursor-pointer transition-all !text-2xl duration-300 ease-in-out transform hover:scale-110`}
                                aria-label="Tìm kiếm"
                            />
                        </Tooltip>
                    )}

                    <Divider type="vertical" className={`transition-colors duration-300 !border-gray-300`} />

                    <Badge count={cartCount}>
                        <Button
                            type="text"
                            icon={<ShoppingCartOutlined />}
                            onClick={handleCartClick}
                            className={`p-2 rounded cursor-pointer !text-2xl transition-all duration-300 !text-gray-900 `}
                            aria-label={`Giỏ hàng (${cartCount} sản phẩm)`}
                        />
                    </Badge>

                    <Divider type="vertical" className={`transition-colors duration-300 !border-gray-300`} />
                    {isAuthenticated ? (
                        <Button
                            onClick={() => navigate('/profile')}
                            className={`group px-8 py-1 border-2 !font-sans rounded-lg transition-all duration-300 hover:scale-105 !border-gray-900 !text-gray-900`}
                            style={{
                                height: 'auto',
                                padding: '4px 32px',
                                lineHeight: '1.5'
                            }}
                        >
                            <span className="flex items-center">
                                <span className="mr-1">Xin chào,</span>
                                <span className="max-w-[100px] truncate">{user?.name || 'Tài khoản'}</span>
                            </span>
                        </Button>
                    ) :
                        (
                            <button
                                onClick={handleToggleLoginModal}
                                className={`cursor-pointer group px-8 py-1 border-2 font-semibold rounded-lg transition-all duration-300 hover:scale-105 !border-gray-900 text-gray-900 hover:bg-gray-900 hover:text-white`}
                            >
                                Đăng nhập
                            </button>
                        )
                    }

                </Space>
            </div>
        );
    }, [handleSearchClick, handleCartClick, handleToggleLoginModal, isScrolled, location.pathname, cartCount, isAuthenticated]);

    // Mobile action buttons (right side)
    const mobileActionButtons = useMemo(() => {
        return (
            <div className="flex lg:hidden items-center space-x-3">
                {!loading && !isAuthenticated && !user && (
                    <Tooltip placement="top" title={'Tra cứu đơn hàng'}>
                        <Button
                            type="text"
                            icon={<SearchOutlined />}
                            onClick={handleSearchClick}
                            className={`p-2 transition-colors !text-2xl duration-300 `}
                            aria-label="Tìm kiếm"
                        />
                    </Tooltip>
                )}
                <Badge count={cartCount}>
                    <Button
                        type="text"
                        icon={<ShoppingCartOutlined />}
                        onClick={handleCartClick}
                        className={`p-2 transition-colors !text-2xl duration-300 !text-gray-900 `}
                        aria-label="Giỏ hàng"
                    />
                </Badge>

                <Button
                    type="text"
                    icon={mobileMenuOpen ? <CloseOutlined /> : <MenuOutlined />}
                    onClick={handleMobileMenuToggle}
                    className={`p-2 ml-4 text-lg transition-colors !text-2xl duration-300 !text-gray-900`}
                    aria-label="Menu"
                />
            </div>
        );
    }, [handleSearchClick, handleCartClick, handleMobileMenuToggle, mobileMenuOpen, isScrolled, location.pathname, cartCount]);

    // Mobile menu content với animations
    const mobileMenuContent = useMemo(() => (
        <div className="flex flex-col h-full bg-white">
            {/* Mobile Menu Header */}
            <div className="flex justify-between items-center p-6 bg-white transition-colors duration-200">
                <div className="w-24">
                    {logoComponent}
                </div>
                <Button
                    type="text"
                    icon={<CloseOutlined />}
                    onClick={handleMobileMenuClose}
                    className="p-2 hover:bg-gray-100 rounded-full"
                />
            </div>

            {/* Mobile Navigation */}
            <div className="flex-1 overflow-y-auto px-6 py-4">
                <Menu
                    mode="vertical"
                    items={MENU_ITEMS.map((item, index) => ({
                        ...item,
                        label: (
                            <div
                                onClick={handleMobileMenuClose}
                                className="py-3 text-lg font-medium text-gray-800 hover:text-red-600 transition-colors duration-200"
                            >
                                {item.label}
                            </div>
                        )
                    }))}
                    className="border-none bg-transparent mobile-nav-menu"
                />
            </div>

            {/* Mobile Action Buttons */}
            <div className="p-6 border-t border-gray-100 bg-gray-50 transition-colors duration-200">
                <div className="space-y-4">
                    {isAuthenticated ? (
                        <Button
                            onClick={() => {
                                handleMobileMenuClose();
                                navigate('/profile')
                            }}
                            className={`w-full group px-8 py-1 border-2 font-semibold rounded-lg transition-all duration-300 hover:scale-105 hover:!border-inherit focus:!outline-none focus:!border-inherit`}
                            style={{
                                height: 'auto',
                                padding: '4px 32px',
                                background: 'transparent',
                                lineHeight: '1.5'
                            }}
                        >
                            <span className="flex items-center">
                                <span className="mr-1">Xin chào,</span>
                                <span className="max-w-[100px] truncate">{user?.name || 'Tài khoản'}</span>
                            </span>
                        </Button>
                    ) : (
                        <Button
                            onClick={() => {
                                handleToggleLoginModal();
                                handleMobileMenuClose();
                            }}
                            className="w-full bg-red-600 text-white flex items-center justify-center gap-2 h-12 rounded-lg font-medium transition-all duration-200 transform hover:scale-[1.02]"
                            icon={<MdAccountCircle size={20} />}
                            size="large"
                        >
                            Đăng nhập
                        </Button>
                    )}
                </div>
            </div>
        </div>
    ), [logoComponent, handleMobileMenuClose, handleToggleLoginModal, handleSearchClick, handleCartClick, isAuthenticated]);

    return (
        <>
            <header className={`fixed right-0 left-0 top-0 z-50 px-0 h-16 transition-all duration-300 bg-white !backdrop-blur-md shadow-lg`}>
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full">
                    <div className="flex justify-between items-center h-16">
                        {/* Logo Section */}
                        <div className="flex-shrink-0 w-20">
                            {logoComponent}
                        </div>

                        {/* Desktop Navigation Menu */}
                        <div className="hidden lg:flex flex-1 justify-center px-8">
                            <Menu
                                mode="horizontal"
                                items={MENU_ITEMS}
                                className={`border-none !bg-transparent flex-1 justify-center max-w-4xl transition-colors duration-300 header-menu-scrolled`}
                                style={{
                                    lineHeight: "64px"
                                }}
                            />
                        </div>

                        {/* Desktop Action Buttons */}
                        {desktopActionButtons}

                        {/* Mobile Action Buttons */}
                        {mobileActionButtons}
                    </div>
                </div>
            </header>

            {/* Mobile Drawer Menu */}
            <Drawer
                title={null}
                placement="right"
                closable={false}
                onClose={handleMobileMenuClose}
                open={mobileMenuOpen}
                width={320}
                className="lg:hidden mobile-drawer"
                bodyStyle={{
                    padding: 0,
                    background: '#ffffff'
                }}
                headerStyle={{ display: 'none' }}
                maskStyle={{
                    backgroundColor: 'rgba(0, 0, 0, 0.45)',
                    backdropFilter: 'blur(4px)'
                }}
                style={{
                    transition: 'all 0.3s cubic-bezier(0.25, 0.8, 0.25, 1)',
                }}
                transitionName="slide-right"
            >
                <div className="mobile-drawer-content">
                    {mobileMenuContent}
                </div>
            </Drawer>

            {/* Login Modal */}
            <ModalLogin
                isOpen={showLoginModal}
                setOpenModal={setShowLoginModal}
                location={location}
                dispatch={dispatch}
                login={login}
                loading={loading}
                searchParams={searchParams}
                navigate={navigate}
            />
            {cartSidebarOpen && (
                <div
                    className="fixed inset-0 z-[99] bg-black/40 transition-opacity duration-300"
                    onClick={() => setCartSidebarOpen(false)}
                />
            )}
            <CartSidebar
                open={cartSidebarOpen}
                onClose={() => setCartSidebarOpen(false)}
            />
        </>
    );
};

export default HeaderVertical;
