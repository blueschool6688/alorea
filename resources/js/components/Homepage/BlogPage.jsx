import { ArrowRightOutlined, CalendarOutlined, ClockCircleOutlined, EyeOutlined } from '@ant-design/icons';
import { useState } from 'react';
import { Link, useNavigate } from 'react-router-dom';

const BlogPage = ({ blogs }) => {
    const [hoveredPost, setHoveredPost] = useState(null);
    const [likedPosts, setLikedPosts] = useState(new Set());
    const navigate = useNavigate();

    // Blog posts data
    const blogPosts = [
        {
            id: 1,
            title: "Top 5 mùi nước hoa nữ quyến rũ nhất 2025",
            excerpt: "Khám phá những mùi hương nữ tính đang làm say đắm phái mạnh trong năm 2025. Từ hương hoa cổ điển đến những note hiện đại táo bạo...",
            featuredImage: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=800&h=600&fit=crop",
            author: "Chuyên gia Hương liệu ALORÉA",
            publishDate: "15 Tháng 12, 2024",
            readTime: "8 phút đọc",
            views: "2.5K",
            category: "Nước hoa nữ",
            tags: ["Trending", "Women's Fragrance", "2025"],
            relatedProducts: [
                { name: "Crimson Mirage", price: "89.00", image: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=150&h=200&fit=crop" },
                { name: "Golden Sunset", price: "175.00", image: "https://images.unsplash.com/photo-1580489944761-15a19d654956?w=150&h=200&fit=crop" }
            ],
            content: "Năm 2025 đánh dấu sự trở lại của những mùi hương nữ tính cổ điển kết hợp với twist hiện đại...",
            gradient: "from-pink-500 to-rose-600"
        },
        {
            id: 2,
            title: "Cách chọn nước hoa theo cá tính & phong cách",
            excerpt: "Hướng dẫn chi tiết giúp bạn tìm ra mùi hương phản ánh đúng cá tính và phong cách sống. Từ người năng động đến những tâm hồn thơ mộng...",
            featuredImage: "https://images.unsplash.com/photo-1596462502278-27bfdc403348?w=800&h=600&fit=crop",
            author: "Tư vấn viên ALORÉA",
            publishDate: "10 Tháng 12, 2024",
            readTime: "12 phút đọc",
            views: "4.1K",
            category: "Hướng dẫn",
            tags: ["Guide", "Personality", "Lifestyle"],
            relatedProducts: [
                { name: "Ocean Breeze", price: "199.00", image: "https://images.unsplash.com/photo-1541643600914-78b084683601?w=150&h=200&fit=crop" },
                { name: "Urban Legend", price: "210.00", image: "https://images.unsplash.com/photo-1585386959984-a4155224a1ad?w=150&h=200&fit=crop" }
            ],
            content: "Việc chọn nước hoa không chỉ đơn thuần là chọn mùi hương mà còn là cách thể hiện bản thân...",
            gradient: "from-blue-500 to-indigo-600"
        },
        {
            id: 3,
            title: "Nước hoa nam: đâu là mùi hương khiến nàng không thể quên?",
            excerpt: "Bí mật đằng sau những mùi hương nam giới khiến phái nữ phải mê mệt. Phân tích tâm lý và khoa học đằng sau sức hút của từng note hương...",
            featuredImage: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=800&h=600&fit=crop",
            author: "Dr. Perfume ALORÉA",
            publishDate: "5 Tháng 12, 2024",
            readTime: "10 phút đọc",
            views: "3.8K",
            category: "Nước hoa nam",
            tags: ["Men's Fragrance", "Psychology", "Attraction"],
            relatedProducts: [
                { name: "Midnight Rose", price: "145.00", image: "https://images.unsplash.com/photo-1610736794048-1d3a26694945?w=150&h=200&fit=crop" },
                { name: "Ashes of Moonlight", price: "139.00", image: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=150&h=200&fit=crop" }
            ],
            content: "Nghiên cứu cho thấy mùi hương có thể ảnh hưởng đến 75% cảm xúc của con người...",
            gradient: "from-gray-700 to-gray-900"
        },
        {
            id: 3,
            title: "Nước hoa nam: đâu là mùi hương khiến nàng không thể quên?",
            excerpt: "Bí mật đằng sau những mùi hương nam giới khiến phái nữ phải mê mệt. Phân tích tâm lý và khoa học đằng sau sức hút của từng note hương...",
            featuredImage: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=800&h=600&fit=crop",
            author: "Dr. Perfume ALORÉA",
            publishDate: "5 Tháng 12, 2024",
            readTime: "10 phút đọc",
            views: "3.8K",
            category: "Nước hoa nam",
            tags: ["Men's Fragrance", "Psychology", "Attraction"],
            relatedProducts: [
                { name: "Midnight Rose", price: "145.00", image: "https://images.unsplash.com/photo-1610736794048-1d3a26694945?w=150&h=200&fit=crop" },
                { name: "Ashes of Moonlight", price: "139.00", image: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=150&h=200&fit=crop" }
            ],
            content: "Nghiên cứu cho thấy mùi hương có thể ảnh hưởng đến 75% cảm xúc của con người...",
            gradient: "from-gray-700 to-gray-900"
        }
    ];

    const handleLike = (postId) => {
        setLikedPosts(prev => {
            const newLiked = new Set(prev);
            if (newLiked.has(postId)) {
                newLiked.delete(postId);
            } else {
                newLiked.add(postId);
            }
            return newLiked;
        });
    };

    return (
        <section className="py-20 bg-gradient-to-b from-gray-50 to-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="text-center mb-16">
                    <h1 className="font-serif text-4xl md:text-5xl lg:text-6xl font-light text-gray-900 mb-4 tracking-wide">
                        KHÁM PHÁ BÍ MẬT CỦA MÙI HƯƠNG
                    </h1>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto mb-6"></div>
                    <p className="text-lg md:text-xl text-gray-600 max-w-4xl mx-auto leading-relaxed">
                        Những câu chuyện thú vị, bí quyết chọn lựa và xu hướng mới nhất trong thế giới nước hoa.
                        Khám phá nghệ thuật tạo hương và tìm hiểu cách mùi hương ảnh hưởng đến cuộc sống của chúng ta.
                    </p>
                </div>

                {/* Featured Post - Reduced Height */}
                <div className="mb-16">
                    <div className="relative overflow-hidden rounded-2xl shadow-xl bg-gradient-to-r from-gray-900 via-black to-gray-800">
                        <div className="absolute inset-0">
                            <img
                                src={blogs[0].featured_image_url}
                                alt={blogs[0].title}
                                className="w-full h-full object-cover opacity-30"
                            />
                            <div className="absolute inset-0 bg-gradient-to-r from-black/70 to-transparent"></div>
                        </div>

                        <div className="relative z-10 p-6 md:p-8 lg:p-10">
                            <div className="max-w-2xl">
                                <div className="flex items-center gap-3 mb-4">
                                    <span className="bg-burgundy-primary text-white px-3 py-1.5 rounded-full text-xs font-serif">
                                        BÀI VIẾT NỔI BẬT
                                    </span>
                                    <span className="text-white/80 text-xs">{blogs[0].category}</span>
                                </div>

                                <h2 className="text-2xl md:text-3xl lg:text-4xl !font-sans font-bold text-white mb-4 leading-tight">
                                    {blogs[0].title}
                                </h2>

                                <p className="text-white/90 text-base md:text-lg mb-6 leading-relaxed !font-sans line-clamp-2">
                                    {blogs[0].excerpt}
                                </p>

                                <div className="flex flex-wrap items-center gap-4 mb-6">
                                    <div className="flex items-center gap-2 text-white/80">
                                        <CalendarOutlined className="text-sm" />
                                        <span className="text-xs">{blogs[0].time_at}</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-white/80">
                                        <ClockCircleOutlined className="text-sm" />
                                        <span className="text-xs">{blogs[0].reading_time}</span>
                                    </div>
                                    <div className="flex items-center gap-2 text-white/80">
                                        <EyeOutlined className="text-sm" />
                                        <span className="text-xs">{blogs[0].views_count} lượt xem</span>
                                    </div>
                                </div>

                                <button
                                    onClick={() => navigate(`/blog/${blogs[0].slug}`)}
                                    className="cursor-pointer bg-white text-gray-900 font-semibold py-3 px-6 rounded-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 shadow-lg hover:shadow-xl flex items-center gap-2 text-sm"
                                >
                                    <span>Đọc bài viết</span>
                                    <ArrowRightOutlined className="text-xs" />
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Blog Posts Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-8 mb-16">
                    {blogs.slice(1).map((post) => (
                        <article
                            key={post.id}
                            className="group bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2"
                            onMouseEnter={() => setHoveredPost(post.id)}
                            onMouseLeave={() => setHoveredPost(null)}
                        >
                            {/* Featured Image */}
                            <div className="relative overflow-hidden aspect-[16/10]">
                                <img
                                    src={post.featured_image_url}
                                    alt={post.title}
                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                />
                                {/* Overlay */}
                                <div className="absolute inset-0 bg-gradient-to-t from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            </div>

                            {/* Content */}
                            <div className="p-6">
                                {/* Meta Info */}
                                <div className="flex items-center gap-4 mb-4 text-sm text-gray-500">
                                    <div className="flex items-center gap-1">
                                        <CalendarOutlined className="text-xs" />
                                        <span>{post.time_at}</span>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <ClockCircleOutlined className="text-xs" />
                                        <span>{post.reading_time}</span>
                                    </div>
                                    <div className="flex items-center gap-1">
                                        <EyeOutlined className="text-xs" />
                                        <span>{post.views_count}</span>
                                    </div>
                                </div>

                                {/* Title */}
                                <h3 className="text-xl font-bold font-sans mb-3  group-hover:text-burgundy-primary transition-colors duration-300 line-clamp-2">
                                    <Link className='!text-black' to={`/blog/${post.slug}`}>{post.title}</Link>
                                </h3>

                                {/* Excerpt */}
                                <p className="text-gray-600 mb-4 line-clamp-3 leading-relaxed">
                                    {post.excerpt}
                                </p>

                                {/* Author */}
                                <div className="flex items-center justify-between">
                                    <span className="text-sm text-gray-500 font-medium">{post.author_name}</span>
                                    <button onClick={() => navigate(`/blog/${post.slug}`)} className="cursor-pointer text-burgundy-primary font-semibold text-sm hover:text-burgundy-dark transition-colors duration-200 flex items-center gap-2">
                                        Đọc tiếp
                                        <ArrowRightOutlined className="text-xs" />
                                    </button>
                                </div>
                            </div>
                        </article>
                    ))}
                </div>
            </div>

            {/* Custom CSS */}
            <style jsx>{`
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .line-clamp-3 {
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
            `}</style>
        </section>
    );
};

export default BlogPage;
