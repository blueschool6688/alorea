import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import { applySEOToDocument, generateBlogDetailSEO } from '@/utils/seoUtils';
import {
    ArrowUpOutlined,
    CalendarOutlined,
    ClockCircleOutlined,
    EyeOutlined,
    HomeOutlined,
    TagOutlined,
    UserOutlined
} from '@ant-design/icons';
import {
    Avatar,
    Breadcrumb,
    Button,
    Card,
    Col,
    Form,
    Input,
    List,
    Row,
    Space,
    Spin,
    Tag,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { Link, useParams } from 'react-router-dom';

const { Title, Paragraph, Text } = Typography;
const { TextArea } = Input;

const BlogDetail = () => {
    const { slug } = useParams();
    const [blog, setBlog] = useState(null);
    const [relatedBlogs, setRelatedBlogs] = useState([]);
    const [loading, setLoading] = useState(true);
    const [form] = Form.useForm();

    // Fetch blog detail
    useEffect(() => {
        const fetchBlogDetail = async () => {
            try {
                setLoading(true);
                const response = await api.get(`${GeneralPath.GENERAL_DATA_BLOGS_DETAIL_ENDPOINT}/${slug}`);

                if (response?.data) {
                    setBlog(response.data?.blog);
                    setRelatedBlogs(response.data?.relatedBlogs);
                }
            } catch (error) {
                console.error('Error fetching blog detail:', error);
            } finally {
                setLoading(false);
            }
        };

        if (slug) {
            fetchBlogDetail();
        }
    }, [slug]);

    useEffect(() => {
        if (blog) {
            const seoData = generateBlogDetailSEO(blog, relatedBlogs);
            applySEOToDocument(seoData);
        }
    }, [blog, relatedBlogs, slug]);

    useEffect(() => {
        return () => {
            document.title = 'ALORÉNA - Nước Hoa Chính Hãng';

            const articleScript = document.querySelector('script[type="application/ld+json"][data-type="article"]');
            if (articleScript) {
                articleScript.remove();
            }

            const breadcrumbScript = document.querySelector('script[type="application/ld+json"][data-type="breadcrumb"]');
            if (breadcrumbScript) {
                breadcrumbScript.remove();
            }
        };
    }, []);


    // Share functions
    const shareUrl = window.location.href;
    const shareTitle = blog?.title || '';

    const scrollToTop = () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    };

    if (loading) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-white">
                <Spin size="large" />
            </div>
        );
    }

    if (!blog) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-white">
                <div className="text-center">
                    <Title level={3} className="text-gray-800">Blog không tồn tại</Title>
                    <Link to="/blog">
                        <Button type="primary" className="bg-black hover:bg-gray-800 border-black">
                            Quay lại danh sách blog
                        </Button>
                    </Link>
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen bg-white !mt-16">
            <div className="relative bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white">
                <div className="absolute inset-0 bg-black/20"></div>
                <div
                    className="relative min-h-[60vh] flex items-center bg-cover bg-center"
                >
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
                        {/* Breadcrumb */}
                        <Breadcrumb className="!mb-8 !mt-3" separator=">">
                            <Breadcrumb.Item>
                                <Link to="/" className="!text-white hover:text-white">
                                    <HomeOutlined className="mr-1" />
                                    Trang chủ
                                </Link>
                            </Breadcrumb.Item>
                            <Breadcrumb.Item>
                                <Link to="/blogs" className="!text-white hover:text-white">
                                    Blog
                                </Link>
                            </Breadcrumb.Item>
                            <Breadcrumb.Item className="text-white">
                                {blog.title}
                            </Breadcrumb.Item>
                        </Breadcrumb>

                        {/* Blog Meta */}
                        <div className="mb-6">
                            <Space wrap className="mb-4">
                                {blog.category && (
                                    <Tag className="bg-white text-black font-medium px-3 py-1">
                                        {blog.category.name}
                                    </Tag>
                                )}
                            </Space>

                            <Title level={1} className="!text-white !mb-6 !text-4xl md:!text-5xl !font-bold !leading-tight">
                                {blog.title}
                            </Title>

                            <Paragraph className="!text-white/90 !text-xl !mb-8 !leading-relaxed">
                                {blog.excerpt}
                            </Paragraph>

                            {/* Author & Meta Info */}
                            <div className="flex flex-wrap items-center gap-6 text-white/80">
                                <Space>
                                    <span className="font-medium">{blog.author?.name || 'Admin'}</span>
                                </Space>

                                <Space>
                                    <CalendarOutlined />
                                    <span>{new Date(blog.created_at).toLocaleDateString('vi-VN')}</span>
                                </Space>

                                <Space>
                                    <ClockCircleOutlined />
                                    <span>{blog.read_time || '5'} phút đọc</span>
                                </Space>

                                <Space>
                                    <EyeOutlined />
                                    <span>{blog.views_count || 0} lượt xem</span>
                                </Space>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <Row gutter={[32, 32]}>
                    {/* Article Content */}
                    <Col xs={24} lg={16}>
                        <div className="bg-white">
                            {/* Article Body */}
                            <div className="prose prose-lg max-w-none">
                                <div
                                    className="text-gray-800 leading-relaxed !font-sans"
                                    dangerouslySetInnerHTML={{ __html: blog.content }}
                                    style={{
                                        lineHeight: '1.8',
                                        fontSize: '18px'
                                    }}
                                />
                            </div>

                            {/* Tags */}
                            {blog.tags && blog.tags.length > 0 && (
                                <div className="mt-12 pt-8 border-t border-gray-200">
                                    <Space wrap size="small">
                                        <TagOutlined className="text-gray-500" />
                                        {blog.tags.map(tag => (
                                            <Tag
                                                key={tag.id}
                                                className="bg-gray-100 border-gray-300 text-gray-700 hover:bg-black hover:text-white transition-colors cursor-pointer"
                                            >
                                                {tag.name}
                                            </Tag>
                                        ))}
                                    </Space>
                                </div>
                            )}

                            {/* Author Bio */}
                            {blog.author && (
                                <Card className="mt-12 bg-gray-50 border-0">
                                    <div className="flex items-start gap-4">
                                        <Avatar
                                            size={80}
                                            src={blog.author.avatar}
                                            icon={<UserOutlined />}
                                            className="bg-black"
                                        />
                                        <div className="flex-1">
                                            <Title level={4} className="!mb-2 !text-black">
                                                {blog.author.name}
                                            </Title>
                                            <Text className="text-gray-600 text-base leading-relaxed">
                                                {blog.author.bio || 'Tác giả chuyên về các chủ đề làm đẹp và nước hoa.'}
                                            </Text>
                                        </div>
                                    </div>
                                </Card>
                            )}


                        </div>
                    </Col>


                    {/* Sidebar */}
                    <Col xs={24} lg={8}>
                        <div className="space-y-8">
                            {/* Related Posts */}
                            {relatedBlogs.length > 0 && (
                                <Card
                                    title="Bài viết liên quan"
                                    className="border-0 shadow-lg"
                                    headStyle={{
                                        backgroundColor: '#fff',
                                        color: 'black',
                                        fontSize: '18px',
                                        fontWeight: 'bold'
                                    }}
                                >
                                    <List
                                        dataSource={relatedBlogs.slice(0, 5)}
                                        renderItem={(item) => (
                                            <List.Item className="border-0 px-0">
                                                <List.Item.Meta
                                                    avatar={
                                                        <img
                                                            src={item.featured_image_url}
                                                            alt={item.title}
                                                            className="w-16 h-16 object-cover rounded"
                                                        />
                                                    }
                                                    title={
                                                        <Link
                                                            to={`/blog/${item.slug}`}
                                                            className="text-black hover:text-gray-600 font-medium text-sm leading-tight"
                                                        >
                                                            {item.title}
                                                        </Link>
                                                    }
                                                    description={
                                                        <Text type="secondary" className="text-xs">
                                                            {new Date(item.created_at).toLocaleDateString('vi-VN')}
                                                        </Text>
                                                    }
                                                />
                                            </List.Item>
                                        )}
                                    />
                                </Card>
                            )}

                            {/* Popular Tags */}
                            <Card
                                title="Tags phổ biến"
                                className="border-0 shadow-lg !mt-3"
                                headStyle={{
                                    backgroundColor: '#fff',
                                    color: 'black',
                                    fontSize: '18px',
                                    fontWeight: 'bold'
                                }}
                            >
                                <Space wrap size="small">
                                    {['Nước hoa', 'Làm đẹp', 'Hương thơm', 'Perfume', 'Beauty Tips', 'Skincare'].map(tag => (
                                        <Tag
                                            key={tag}
                                            className="bg-gray-100 border-gray-300 text-gray-700 hover:bg-black hover:text-white transition-colors cursor-pointer"
                                        >
                                            {tag}
                                        </Tag>
                                    ))}
                                </Space>
                            </Card>

                            {/* Back to Top */}
                            <Button
                                icon={<ArrowUpOutlined />}
                                onClick={scrollToTop}
                                className="w-full bg-black text-white hover:bg-gray-800 border-black !mt-3"
                                size="large"
                            >
                                Lên đầu trang
                            </Button>
                        </div>
                    </Col>
                </Row>
            </div>

            {/* Custom Styles */}
            <style jsx>{`
                .prose h1, .prose h2, .prose h3, .prose h4, .prose h5, .prose h6 {
                    color: #000 !important;
                    font-weight: bold !important;
                    margin-top: 2rem !important;
                    margin-bottom: 1rem !important;
                }

                .prose p {
                    color: #374151 !important;
                    margin-bottom: 1.5rem !important;
                }

                .prose img {
                    border-radius: 8px !important;
                    margin: 2rem 0 !important;
                }

                .prose blockquote {
                    border-left: 4px solid #000 !important;
                    background: #f9fafb !important;
                    padding: 1rem 1.5rem !important;
                    margin: 2rem 0 !important;
                    font-style: italic !important;
                }

                .prose ul, .prose ol {
                    color: #374151 !important;
                }

                .prose a {
                    color: #000 !important;
                    text-decoration: underline !important;
                }

                .prose a:hover {
                    color: #6b7280 !important;
                }
            `}</style>
        </div>
    );
};

export default BlogDetail;
