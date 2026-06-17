import { useSettings } from '@/lib/context/SettingContext';
import api from '@/utils/api';
import {
    EnvironmentOutlined,
    MailOutlined,
    PhoneOutlined
} from '@ant-design/icons';
import { message } from 'antd';
import { useState } from 'react';


const Footer = ({ contactRef }) => {
    const [email, setEmail] = useState('');
    const [isSubscribed, setIsSubscribed] = useState(false);
    const [isLoading, setIsLoading] = useState(false);
    const { settings } = useSettings();

    const handleSubscribe = async (e) => {
        e.preventDefault();
        if (!email) return;
        setIsLoading(true);

        try {
            const response = await api.post('send-contact', { email })
            if (response) {
                message.success('Đăng ký thành công! Vui lòng kiểm tra email của bạn.');
                setIsLoading(false);
                setEmail('');
            }
        } catch (error) {
            message.error('Đăng ký không thành công! Vui lòng thử lại sau.');
        }
    };

    const socialLinks = [
        {
            name: 'TikTok',
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-5.2 1.74 2.89 2.89 0 0 1 2.31-4.64 2.93 2.93 0 0 1 .88.13V9.4a6.84 6.84 0 0 0-1-.05A6.33 6.33 0 0 0 5 20.1a6.34 6.34 0 0 0 10.86-4.43v-7a8.16 8.16 0 0 0 4.77 1.52v-3.4a4.85 4.85 0 0 1-1-.1z" />
                </svg>
            ),
            url: settings?.link_social_tiktok,
            color: 'hover:text-white-500'
        },
        {
            name: 'Instagram',
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z" />
                </svg>
            ),
            url: settings?.link_social_instagram,
            color: 'hover:text-pink-600'
        },
        {
            name: 'Facebook',
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M24 12.073c0-6.627-5.373-12-12-12s-12 5.373-12 12c0 5.99 4.388 10.954 10.125 11.854v-8.385H7.078v-3.47h3.047V9.43c0-3.007 1.792-4.669 4.533-4.669 1.312 0 2.686.235 2.686.235v2.953H15.83c-1.491 0-1.956.925-1.956 1.874v2.25h3.328l-.532 3.47h-2.796v8.385C19.612 23.027 24 18.062 24 12.073z" />
                </svg>
            ),
            url: settings?.link_social_facebook,
            color: 'hover:text-blue-600'
        },
        {
            name: 'YouTube',
            icon: (
                <svg className="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                    <path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z" />
                </svg>
            ),
            url: settings?.link_social_youtube,
            color: 'hover:text-red-600'
        }
    ];

    const quickLinks = [
        { name: 'Giới thiệu', url: '/about' },
        { name: 'Sản phẩm', url: '/products' },
        { name: 'Blog', url: '/blogs' },
        { name: 'Liên hệ', url: '/#' },
        { name: 'Chính sách bảo mật', url: '/#' },
        { name: 'Điều khoản sử dụng', url: '/#' }
    ];

    const productCategories = [
        { name: 'Nước hoa nam', url: '/products/men' },
        { name: 'Nước hoa nữ', url: '/products/women' },
        { name: 'Bộ quà tặng', url: '/products/gift-sets' },
        { name: 'Nước hoa unisex', url: '/products/unisex' },
        { name: 'Sale off', url: '/products/sale' },
        { name: 'Sản phẩm mới', url: '/products/new' }
    ];

    return (
        <footer className="bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white relative overflow-hidden" ref={contactRef}>
            {/* Background Decorations */}
            <div className="absolute inset-0 bg-black/20"></div>

            <div className="relative z-10">
                {/* Newsletter Section */}
                {/* <div className="border-b border-gray-800/50">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                        <div className="text-center max-w-3xl mx-auto">
                            <div className="flex items-center justify-center gap-3 mb-4">
                                <div className="w-12 h-12 bg-gray-800 rounded-full flex items-center justify-center">
                                    <HeartOutlined className="text-xl text-gray-300" />
                                </div>
                                <h3 className="text-2xl md:text-3xl font-bold font-serif">
                                    Đăng ký nhận ưu đãi
                                </h3>
                            </div>

                            <p className="text-gray-400 mb-8 text-lg font-sans">
                                Đăng ký email để nhận <span className="font-bold text-white">ưu đãi 10%</span> cho đơn hàng đầu tiên
                                và những thông tin mới nhất về sản phẩm từ ALORÉA
                            </p>

                            {!isSubscribed ? (
                                <form onSubmit={handleSubscribe} className="max-w-md mx-auto">
                                    <div className="flex gap-3">
                                        <div className="flex-1 relative">
                                            <input
                                                type="email"
                                                value={email}
                                                onChange={(e) => setEmail(e.target.value)}
                                                placeholder="Nhập email của bạn..."
                                                className="w-full px-6 py-4 bg-gray-900 backdrop-blur-sm border border-gray-700 rounded-full text-white placeholder-gray-500 focus:outline-none focus:border-gray-500 focus:ring-2 focus:ring-gray-500/20 transition-all duration-300"
                                                required
                                            />
                                        </div>
                                        <button
                                            type="submit"
                                            disabled={isLoading}
                                            className="px-8 py-4 bg-gray-800 text-white rounded-full hover:bg-gray-700 transition-all duration-300 transform hover:scale-105 disabled:opacity-50 disabled:cursor-not-allowed flex items-center gap-2 font-semibold"
                                        >
                                            {isLoading ? (
                                                <div className="w-5 h-5 border-2 border-white/30 border-t-white rounded-full animate-spin"></div>
                                            ) : (
                                                <SendOutlined />
                                            )}
                                            Đăng ký
                                        </button>
                                    </div>
                                </form>
                            ) : (
                                <div className="max-w-md mx-auto">
                                    <div className="bg-green-500/20 border border-green-500/30 rounded-2xl p-6 backdrop-blur-sm">
                                        <div className="flex items-center justify-center gap-3 mb-3">
                                            <CheckCircleOutlined className="text-green-400 text-2xl" />
                                            <span className="text-green-400 font-bold text-lg">Đăng ký thành công!</span>
                                        </div>
                                        <p className="text-gray-300">
                                            Cảm ơn bạn đã đăng ký! Mã giảm giá 10% đã được gửi đến email của bạn.
                                        </p>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </div> */}

                {/* Main Footer Content */}
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                        {/* Brand Info & Contact */}
                        <div className="lg:col-span-1">
                            <div className="mb-8">
                                <h2 className="text-3xl font-bold font-serif text-white mb-4">
                                    ALORÉA
                                </h2>
                                <p className="!font-sans text-white leading-relaxed mb-6">
                                    Thương hiệu nước hoa cao cấp, mang đến những trải nghiệm hương thơm độc đáo và quyến rũ.
                                </p>
                            </div>

                            {/* Contact Info */}
                            <div className="space-y-4">
                                <h4 className="text-lg font-bold text-white mb-4">Liên hệ</h4>

                                <div className="flex items-center gap-3 group">
                                    <div className="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors duration-300">
                                        <PhoneOutlined className="text-gray-300" />
                                    </div>
                                    <div>
                                        <a href="tel:1900-xxxx" className="text-white hover:text-gray-300 transition-colors duration-300">
                                            {settings?.hotline}
                                        </a>
                                    </div>
                                </div>

                                <div className="flex items-center gap-3 group">
                                    <div className="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors duration-300">
                                        <MailOutlined className="text-gray-300" />
                                    </div>
                                    <div>
                                        <a href="mailto:contact@alorea.vn" className="!text-white hover:text-gray-300 transition-colors duration-300">
                                            {settings?.email_contact}
                                        </a>
                                    </div>
                                </div>

                                <div className="flex items-start gap-3 group">
                                    <div className="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors duration-300 mt-1">
                                        <EnvironmentOutlined className="text-gray-300" />
                                    </div>
                                    <div>
                                        <a className="text-white hover:text-gray-300 transition-colors duration-300">
                                            {settings?.address}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {/* Quick Links */}
                        <div>
                            <h4 className="text-lg font-bold text-white mb-6 font-serif">Liên kết nhanh</h4>
                            <ul className="space-y-3">
                                {quickLinks.map((link, index) => (
                                    <li key={index}>
                                        <a
                                            href={link.url}
                                            className="text-white hover:text-white transition-colors duration-300 flex items-center gap-2 group"
                                        >
                                            <span className="w-1 h-1 bg-white rounded-full group-hover:w-2 transition-all duration-300"></span>
                                            {link.name}
                                        </a>
                                    </li>
                                ))}
                            </ul>
                        </div>

                        {/* Social Media */}
                        <div>
                            <h4 className="text-lg font-bold text-white mb-6 font-serif">Theo dõi chúng tôi</h4>
                            <div className="space-y-4 mb-8">
                                {socialLinks.map((social, index) => (
                                    <a
                                        key={index}
                                        target='__blank'
                                        href={social.url}
                                        className={`flex items-center gap-3 text-gray-400 ${social.color} transition-all duration-300 group`}
                                    >
                                        <div className="w-10 h-10 bg-gray-800 rounded-lg flex items-center justify-center group-hover:bg-gray-700 transition-colors duration-300">
                                            {social.icon}
                                        </div>
                                        <span className="group-hover:translate-x-1 transition-transform duration-300 text-white">
                                            {social.name}
                                        </span>
                                    </a>
                                ))}
                            </div>
                        </div>
                    </div>
                </div>

                {/* Bottom Bar */}
                <div className="border-t border-white">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-6">
                        <div className="flex flex-col md:flex-row justify-between items-center gap-4">
                            <div className="text-white text-sm">
                                © 2025 ALORÉA. Tất cả quyền được bảo lưu.
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </footer>
    );
};

export default Footer;
