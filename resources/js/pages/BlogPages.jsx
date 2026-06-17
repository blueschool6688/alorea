import api from '@/utils/api';
import {
    ClockCircleOutlined,
    EyeOutlined,
    SearchOutlined,
    UserOutlined
} from '@ant-design/icons';
import {
    Avatar,
    Badge,
    Button,
    Card,
    Col,
    Empty,
    Input,
    Pagination,
    Row,
    Select,
    Space,
    Spin,
    Tag,
    Typography
} from 'antd';
import { useCallback, useEffect, useState } from 'react';
import { Link } from 'react-router-dom';

const { Title, Paragraph, Text } = Typography;
const { Search } = Input;
const { Option } = Select;
const { Meta } = Card;

const BlogPages = () => {
    const [blogs, setBlogs] = useState([]);
    const [categories, setCategories] = useState([]);
    const [loading, setLoading] = useState(true);
    const [searchLoading, setSearchLoading] = useState(false);
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize] = useState(12);
    const [total, setTotal] = useState(0);
    const [searchTerm, setSearchTerm] = useState('');
    const [selectedCategory, setSelectedCategory] = useState('');
    const [sortBy, setSortBy] = useState('newest');
    const [viewMode, setViewMode] = useState('grid');

    // Fetch blogs from API
    const fetchBlogs = useCallback(async (page = 1, search = '', category = '', sort = 'newest') => {
        try {
            setLoading(page === 1);
            setSearchLoading(page !== 1);

            const params = new URLSearchParams();
            params.append('page', page.toString());
            params.append('per_page', pageSize.toString());

            if (search) params.append('search', search);
            if (category) params.append('category', category);

            // Sort mapping
            let sortParam = '';
            switch (sort) {
                case 'newest':
                    sortParam = 'created_at:desc';
                    break;
                case 'oldest':
                    sortParam = 'created_at:asc';
                    break;
                case 'popular':
                    sortParam = 'views_count:desc';
                    break;
                case 'most_liked':
                    sortParam = 'likes_count:desc';
                    break;
                default:
                    sortParam = 'created_at:desc';
            }
            params.append('sort', sortParam);

            console.log('Fetching blogs with params:', params.toString());

            const response = await api.get(`${'get-blogs'}?${params.toString()}`);
            console.log('Blog API Response:', response);

            if (response?.data) {
                const responseData = response.data.data || response.data;

                // Handle paginated response
                if (responseData.data && Array.isArray(responseData.data)) {
                    const formattedBlogs = responseData.data.map(blog => ({
                        id: blog.id,
                        title: blog.title,
                        slug: blog.slug,
                        excerpt: blog.excerpt || blog.short_description,
                        content: blog.content,
                        featured_image: blog.featured_image || blog.image,
                        created_at: blog.created_at,
                        updated_at: blog.updated_at,
                        views_count: blog.views_count || 0,
                        likes_count: blog.likes_count || 0,
                        comments_count: blog.comments_count || 0,
                        read_time: blog.read_time || Math.ceil((blog.content?.length || 0) / 1000) || 5,
                        status: blog.status,
                        is_featured: blog.is_featured,
                        meta_title: blog.meta_title,
                        meta_description: blog.meta_description,
                        category: blog.category ? {
                            id: blog.category.id,
                            name: blog.category.name,
                            slug: blog.category.slug
                        } : null,
                        author: blog.author_name ?? 'Admin',
                        tags: blog.tags || []
                    }));

                    setBlogs(formattedBlogs);
                    setTotal(responseData.total || formattedBlogs.length);
                    setCurrentPage(responseData.current_page || page);
                } else if (Array.isArray(responseData)) {
                    // Handle direct array response
                    const formattedBlogs = responseData.map(blog => ({
                        id: blog.id,
                        title: blog.title,
                        slug: blog.slug,
                        excerpt: blog.excerpt || blog.short_description,
                        content: blog.content,
                        featured_image: blog.featured_image_url || blog.image,
                        created_at: blog.created_at,
                        updated_at: blog.updated_at,
                        views_count: blog.views_count || 0,
                        likes_count: blog.likes_count || 0,
                        comments_count: blog.comments_count || 0,
                        read_time: blog.read_time || Math.ceil((blog.content?.length || 0) / 1000) || 5,
                        status: blog.status,
                        is_featured: blog.is_featured,
                        category: blog.category ? {
                            id: blog.category.id,
                            name: blog.category.name,
                            slug: blog.category.slug
                        } : null,
                        author: blog.author_name ?? 'Admin',
                        tags: blog.tags || []
                    }));

                    setBlogs(formattedBlogs);
                    setTotal(formattedBlogs.length);
                }
            }
        } catch (error) {
            console.error('Error fetching blogs:', error);
            setBlogs([]);
            setTotal(0);
        } finally {
            setLoading(false);
            setSearchLoading(false);
        }
    }, [pageSize]);

    useEffect(() => {
        fetchBlogs();
    }, [fetchBlogs]);

    const handleSearch = useCallback((value) => {
        setSearchTerm(value);
        setCurrentPage(1);
        fetchBlogs(1, value, selectedCategory, sortBy);
    }, [selectedCategory, sortBy, fetchBlogs]);

    // Handle category filter
    const handleCategoryChange = useCallback((value) => {
        setSelectedCategory(value);
        setCurrentPage(1);
        fetchBlogs(1, searchTerm, value, sortBy);
    }, [searchTerm, sortBy, fetchBlogs]);

    // Handle sort change
    const handleSortChange = useCallback((value) => {
        setSortBy(value);
        setCurrentPage(1);
        fetchBlogs(1, searchTerm, selectedCategory, value);
    }, [searchTerm, selectedCategory, fetchBlogs]);

    // Handle page change
    const handlePageChange = useCallback((page) => {
        setCurrentPage(page);
        fetchBlogs(page, searchTerm, selectedCategory, sortBy);

        // Scroll to top
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }, [searchTerm, selectedCategory, sortBy, fetchBlogs]);

    // Format date
    const formatDate = (dateString) => {
        return new Date(dateString).toLocaleDateString('vi-VN', {
            year: 'numeric',
            month: 'long',
            day: 'numeric'
        });
    };

    // Truncate text
    const truncateText = (text, maxLength = 150) => {
        if (!text) return '';
        return text.length > maxLength ? text.substring(0, maxLength) + '...' : text;
    };

    // Sort options
    const sortOptions = [
        { value: 'newest', label: 'Mới nhất' },
        { value: 'oldest', label: 'Cũ nhất' },
        { value: 'popular', label: 'Phổ biến nhất' },
        { value: 'most_liked', label: 'Được yêu thích nhất' }
    ];

    // Blog Card Component
    const BlogCard = ({ blog, viewMode }) => {
        if (viewMode === 'list') {
            return (
                <Card className="mb-6 shadow-md hover:shadow-xl transition-all duration-300">
                    <Row gutter={[24, 16]}>
                        <Col xs={24} sm={8} md={6}>
                            <div className="relative overflow-hidden rounded-lg h-48">
                                <img
                                    src={blog.featured_image}
                                    alt={blog.title}
                                    className="w-full h-full object-cover hover:scale-105 transition-transform duration-300"
                                    onError={(e) => {
                                        e.target.src = '/images/blog-placeholder.jpg';
                                    }}
                                />
                                {blog.is_featured && (
                                    <Tag color="gold" className="absolute top-2 right-2">
                                        Nổi bật
                                    </Tag>
                                )}
                            </div>
                        </Col>
                        <Col xs={24} sm={16} md={18}>
                            <div className="h-full flex flex-col justify-between">
                                <div>
                                    <div className="flex items-center gap-2 mb-3">
                                        {blog.category && (
                                            <Tag color="black">{blog.category.name}</Tag>
                                        )}
                                        <Text type="secondary" className="text-sm">
                                            {formatDate(blog.created_at)}
                                        </Text>
                                    </div>

                                    <Link to={`/blog/${blog.slug}`}>
                                        <Title level={3} className="!mb-3 hover:text-gray-600 transition-colors">
                                            {blog.title}
                                        </Title>
                                    </Link>

                                    <Paragraph className="text-gray-600 mb-4">
                                        {truncateText(blog.excerpt, 200)}
                                    </Paragraph>
                                </div>

                                <div className="flex items-center justify-between">
                                    <Space>
                                        <Text className="text-sm">{blog.author_name}</Text>
                                    </Space>

                                    <Space size="large">
                                        <Space size="small">
                                            <ClockCircleOutlined className="text-gray-400" />
                                            <Text type="secondary" className="text-sm">{blog.read_time} phút</Text>
                                        </Space>
                                        <Space size="small">
                                            <EyeOutlined className="text-gray-400" />
                                            <Text type="secondary" className="text-sm">{blog.views_count}</Text>
                                        </Space>

                                    </Space>
                                </div>
                            </div>
                        </Col>
                    </Row>
                </Card>
            );
        }

        return (
            <Card
                hoverable
                className="h-full shadow-md hover:shadow-xl transition-all duration-300 group"
                cover={
                    <div className="relative overflow-hidden h-48">
                        <img
                            src={blog.featured_image}
                            alt={blog.title}
                            className="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
                            onError={(e) => {
                                e.target.src = '/images/blog-placeholder.jpg';
                            }}
                        />
                        {blog.is_featured && (
                            <Tag color="gold" className="absolute top-2 right-2">
                                Nổi bật
                            </Tag>
                        )}
                        {blog.category && (
                            <Tag color="black" className="absolute bottom-2 left-2">
                                {blog.category.name}
                            </Tag>
                        )}
                    </div>
                }
            >
                <div className="flex flex-col h-full">
                    <div className="flex-1">
                        <Link to={`/blog/${blog.slug}`}>
                            <Title level={4} className="!mb-3 hover:text-gray-600 transition-colors line-clamp-2">
                                {blog.title}
                            </Title>
                        </Link>

                        <Paragraph className="text-gray-600 mb-4 line-clamp-3">
                            {truncateText(blog.excerpt)}
                        </Paragraph>
                    </div>

                    <div className="border-t pt-4">
                        <div className="flex items-center justify-between mb-3">
                            <Space>
                                <Avatar
                                    src={blog.author.avatar}
                                    icon={<UserOutlined />}
                                    size="small"
                                    className="bg-black"
                                />
                                <Text className="text-sm">{blog.author.name}</Text>
                            </Space>
                            <Text type="secondary" className="text-sm">
                                {formatDate(blog.created_at)}
                            </Text>
                        </div>

                        <div className="flex items-center justify-between text-gray-400">
                            <Space size="large">
                                <Space size="small">
                                    <ClockCircleOutlined />
                                    <Text type="secondary" className="text-sm">{blog.read_time} phút</Text>
                                </Space>
                                <Space size="small">
                                    <EyeOutlined />
                                    <Text type="secondary" className="text-sm">{blog.views_count}</Text>
                                </Space>
                            </Space>
                        </div>
                    </div>
                </div>
            </Card>
        );
    };

    return (
        <div className="min-h-screen bg-white">
            {/* Hero Section */}
            <div className="relative bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white py-20 mt-12">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <Title level={1} className="!text-white !text-5xl md:!text-6xl !font-serif !mb-6">
                        BLOGS
                    </Title>
                    <div className="w-32 h-1 bg-white mx-auto mb-8"></div>
                    <Paragraph className="!text-white/90 !text-xl max-w-3xl mx-auto !font-sans">
                        Khám phá thế giới nước hoa qua những bài viết chuyên sâu về hương thơm,
                        cách sử dụng và xu hướng làm đẹp mới nhất
                    </Paragraph>
                </div>
            </div>

            {/* Main Content */}
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                {/* Filter & Search Bar */}
                <Card className="mb-8 shadow-lg">
                    <Row gutter={[16, 16]} align="middle">
                        <Col xs={24} md={8}>
                            <Search
                                placeholder="Tìm kiếm bài viết..."
                                className='rouned-lg'
                                allowClear
                                enterButton={
                                    <Button
                                        type="primary"
                                        icon={<SearchOutlined />}
                                        style={{ backgroundColor: '#191537', borderColor: 'black' }}
                                    />
                                }
                                size="large"
                                onSearch={handleSearch}
                                loading={searchLoading}
                            />
                        </Col>
                        {/* <Col xs={12} md={4}>
                            <Select
                                placeholder="Danh mục"
                                allowClear
                                size="large"
                                className="w-full"
                                value={selectedCategory || undefined}
                                onChange={handleCategoryChange}
                            >
                                {categories.map(category => (
                                    <Option key={category.id} value={category.slug || category.id}>
                                        {category.name}
                                    </Option>
                                ))}
                            </Select>
                        </Col> */}
                        <Col xs={24} md={8}>
                            <Select
                                size="large"
                                className="w-full"
                                value={sortBy}
                                onChange={handleSortChange}
                            >
                                {sortOptions.map(option => (
                                    <Option key={option.value} value={option.value}>
                                        {option.label}
                                    </Option>
                                ))}
                            </Select>
                        </Col>

                        <Col xs={24} md={8}>
                            <div className="flex items-center justify-between">
                                <Text type="secondary">
                                    {loading ? (
                                        <Space>
                                            <Spin size="small" />
                                            Đang tải...
                                        </Space>
                                    ) : (
                                        `Tìm thấy ${total} bài viết`
                                    )}
                                </Text>
                                {/*
                                <Button.Group>
                                    <Button
                                        onClick={() => setViewMode('grid')}
                                        type={viewMode === 'grid' ? 'primary' : 'default'}
                                        icon={<AppstoreOutlined />}
                                        style={viewMode === 'grid' ? { backgroundColor: 'black', borderColor: 'black' } : {}}
                                    />
                                    <Button
                                        onClick={() => setViewMode('list')}
                                        type={viewMode === 'list' ? 'primary' : 'default'}
                                        icon={<UnorderedListOutlined />}
                                        style={viewMode === 'list' ? { backgroundColor: 'black', borderColor: 'black' } : {}}
                                    />
                                </Button.Group> */}
                            </div>
                        </Col>
                    </Row>
                </Card>

                {/* Blog Posts */}
                {loading ? (
                    <div className="text-center py-16">
                        <Spin size="large" />
                        <div className="mt-4">
                            <Text type="secondary">Đang tải bài viết...</Text>
                        </div>
                    </div>
                ) : blogs.length === 0 ? (
                    <Empty
                        image={Empty.PRESENTED_IMAGE_SIMPLE}
                        description={
                            <div>
                                <Text strong>Không tìm thấy bài viết nào</Text>
                                <br />
                                <Text type="secondary">Thử thay đổi từ khóa tìm kiếm hoặc bộ lọc</Text>
                            </div>
                        }
                    />
                ) : (
                    <div className='mt-8'>
                        {viewMode === 'grid' ? (
                            <Row gutter={[24, 24]}>
                                {blogs.map(blog => {
                                    if (blog?.is_featured) {
                                        return (
                                            <Badge.Ribbon text="Nổi bật" color="gold">
                                                <Col key={blog.id} xs={24} sm={12} >
                                                    <BlogCard blog={blog} viewMode={viewMode} />
                                                </Col>
                                            </Badge.Ribbon>
                                        )
                                    }
                                    return (
                                        <Col key={blog.id} xs={24} sm={12} >
                                            <BlogCard blog={blog} viewMode={viewMode} />
                                        </Col>
                                    )
                                }
                                )}
                            </Row>
                        ) : (
                            <div>
                                {blogs.map(blog => {
                                    if (blog?.is_featured) {
                                        return (
                                            <Badge.Ribbon text="Hippies">
                                                <BlogCard key={blog.id} blog={blog} viewMode="list" />
                                            </Badge.Ribbon>
                                        )
                                    }
                                    return <BlogCard key={blog.id} blog={blog} viewMode="list" />;
                                })}
                            </div>
                        )}
                    </div>
                )}

                {/* Pagination */}
                {total > pageSize && (
                    <div className="flex justify-center mt-12">
                        <Pagination
                            current={currentPage}
                            total={total}
                            pageSize={pageSize}
                            onChange={handlePageChange}
                            showSizeChanger={false}
                            showQuickJumper={true}
                            showTotal={(total, range) =>
                                `${range[0]}-${range[1]} trong ${total} bài viết`
                            }
                            className="custom-pagination"
                        />
                    </div>
                )}
            </div>

            {/* Custom CSS */}
            <style jsx>{`
                .custom-pagination .ant-pagination-item-active {
                    background-color: black !important;
                    border-color: black !important;
                }
                .custom-pagination .ant-pagination-item-active a {
                    color: white !important;
                }
                .custom-pagination .ant-pagination-item:hover {
                    border-color: black !important;
                }
                .custom-pagination .ant-pagination-item:hover a {
                    color: black !important;
                }
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
        </div>
    );
};

export default BlogPages;
