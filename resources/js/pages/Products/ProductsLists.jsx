import FilterSidebar from '@/components/FilterSidebar';
import ProductCard from '@/components/ProductCard';
import { useCart } from '@/lib/context/CartContext';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import {
    AppstoreOutlined,
    FilterOutlined,
    SortAscendingOutlined,
    UnorderedListOutlined
} from '@ant-design/icons';
import {
    Button,
    Card,
    Col,
    Empty,
    Pagination,
    Row,
    Select,
    Space,
    Spin,
    Typography
} from 'antd';
import { useCallback, useEffect, useState } from 'react';

const { Title, Text, Paragraph } = Typography;

const ProductsLists = () => {
    const [products, setProducts] = useState([]);
    const [filtersData, setFiltersData] = useState({
        gender: {},
        scents: []
    });
    const [currentPage, setCurrentPage] = useState(1);
    const [pageSize] = useState(12);
    const [filters, setFilters] = useState({
        gender: [],
        scent: [],
        priceRange: [],
    });
    const { addToCart } = useCart();
    const [sortBy, setSortBy] = useState('newest');
    const [viewMode, setViewMode] = useState('grid');
    const [showMobileFilter, setShowMobileFilter] = useState(false);
    const [loadingData, setLoading] = useState(true);
    const [pagination, setPagination] = useState({
        current: 1,
        total: 0,
        per_page: 12
    });

    // Build query parameters for API
    const buildQueryParams = useCallback((page = 1, currentFilters = filters, currentSortBy = sortBy) => {
        const params = new URLSearchParams();

        // Pagination
        params.append('page', page.toString());
        params.append('per_page', pageSize.toString());

        // Gender filter
        if (currentFilters.gender && currentFilters.gender.length > 0) {
            currentFilters.gender.forEach(gender => {
                params.append('gender[]', gender);
            });
        }

        // Scent filter
        if (currentFilters.scent && currentFilters.scent.length > 0) {
            currentFilters.scent.forEach(scent => {
                params.append('scent[]', scent);
            });
        }

        // Price range filter
        if (currentFilters.priceRange && currentFilters.priceRange.length > 0) {
            currentFilters.priceRange.forEach(range => {
                switch (range) {
                    case '0-500':
                        params.append('min_price', '0');
                        params.append('max_price', '500000');
                        break;
                    case '500-1000':
                        params.append('min_price', '500000');
                        params.append('max_price', '1000000');
                        break;
                    case '1000-2000':
                        params.append('min_price', '1000000');
                        params.append('max_price', '2000000');
                        break;
                    case '2000+':
                        params.append('min_price', '2000000');
                        break;
                }
            });
        }

        // Sorting
        if (currentSortBy) {
            let sortParam = '';
            switch (currentSortBy) {
                case 'newest':
                    sortParam = 'created_at:desc';
                    break;
                case 'oldest':
                    sortParam = 'created_at:asc';
                    break;
                case 'price-asc':
                    sortParam = 'price:asc';
                    break;
                case 'price-desc':
                    sortParam = 'price:desc';
                    break;
                case 'rating':
                    sortParam = 'rating:desc';
                    break;
                default:
                    sortParam = 'created_at:desc';
            }
            params.append('sort', sortParam);
        }

        return params.toString();
    }, [pageSize]);

    // Fetch products from API with filters
    const fetchProducts = useCallback(async (page = 1, currentFilters = filters, currentSortBy = sortBy) => {
        setLoading(true);
        try {
            const queryString = buildQueryParams(page, currentFilters, currentSortBy);
            console.log('Fetching products with query:', queryString);

            const response = await api.get(`${GeneralPath.GENERAL_DATA_PRODUCTS_ENDPOINT}?${queryString}`);

            if (response && response?.data) {
                const responseData = response.data;

                // Update filters data if available
                if (responseData.filters) {
                    setFiltersData({
                        gender: responseData.filters.gender || {},
                        scents: responseData.filters.scents || []
                    });
                }

                // Update products and pagination
                if (responseData.products && responseData.products.data) {
                    setProducts(responseData.products.data);
                    setPagination({
                        current: responseData.products.current_page,
                        total: responseData.products.total,
                        per_page: responseData.products.per_page
                    });
                    setCurrentPage(responseData.products.current_page);
                } else {
                    setProducts([]);
                    setPagination({ current: 1, total: 0, per_page: 12 });
                }
            }
        } catch (error) {
            console.error('Error fetching products:', error);
            setProducts([]);
            setPagination({ current: 1, total: 0, per_page: 12 });
        } finally {
            setLoading(false);
        }
    }, [buildQueryParams]);

    useEffect(() => {
        fetchProducts(1, filters, sortBy);
    }, []);

    useEffect(() => {
        if (filters.gender.length > 0 || filters.scent.length > 0 || filters.priceRange.length > 0 || sortBy !== 'newest') {
            fetchProducts(1, filters, sortBy);
        }
    }, [filters, sortBy, fetchProducts]);

    useEffect(() => {
        if (currentPage > 1) {
            fetchProducts(currentPage, filters, sortBy);
        }
    }, [currentPage]);
    const handleFilterChange = useCallback((filterType, value, checked) => {
        console.log('Filter change:', { filterType, value, checked });

        setFilters(prev => {
            const currentValues = prev[filterType] || [];
            let newValues;

            if (checked) {
                newValues = [...currentValues, value];
            } else {
                newValues = currentValues.filter(item => item !== value);
            }

            return {
                ...prev,
                [filterType]: newValues
            };
        });

        // Reset to page 1 when filter changes
        setCurrentPage(1);
    }, []);

    // Handle sort change
    const handleSortChange = useCallback((newSortBy) => {
        console.log('Sort change:', newSortBy);
        setSortBy(newSortBy);
        setCurrentPage(1);
    }, []);

    // Clear all filters
    const clearFilters = useCallback(() => {
        const emptyFilters = {
            gender: [],
            scent: [],
            priceRange: [],
        };
        setFilters(emptyFilters);
        setCurrentPage(1);
        fetchProducts(1, emptyFilters, sortBy);
    }, []);

    // Handle page change
    const handlePageChange = (page, size) => {
        setCurrentPage(page);

        // Scroll to top
        const productsSection = document.querySelector('.products-section');
        if (productsSection) {
            productsSection.scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
        } else {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        }
    };

    const sortOptions = [
        { value: 'newest', label: 'Mới nhất' },
        { value: 'oldest', label: 'Cũ nhất' },
        { value: 'price-asc', label: 'Giá tăng dần' },
        { value: 'price-desc', label: 'Giá giảm dần' },
        { value: 'rating', label: 'Đánh giá cao' }
    ];

    return (
        <section className="bg-gray-50 py-12">
            <div className="relative bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white py-20 mb-12">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center space-y-6">
                        <Title level={1} className="!text-white !text-5xl md:!text-6xl !font-serif !mb-4">
                            BỘ SƯU TẬP NƯỚC HOA
                        </Title>
                        <div className="w-32 h-1 bg-white mx-auto mb-8"></div>
                        <Paragraph className="!text-white/90 !text-xl !font-sans !max-w-3xl !mx-auto !leading-relaxed">
                            Khám phá bộ sưu tập nước hoa cao cấp với hơn 50+ mùi hương độc đáo từ ALORÉA
                        </Paragraph>
                    </div>
                </div>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="flex flex-col lg:flex-row gap-8">
                    <FilterSidebar
                        filters={filters}
                        filtersData={filtersData}
                        onFilterChange={handleFilterChange}
                        onClearFilters={clearFilters}
                        loading={loadingData}
                    />

                    {/* Mobile Filter Button */}
                    <div className="lg:hidden mb-4">
                        <Button
                            onClick={() => setShowMobileFilter(!showMobileFilter)}
                            icon={<FilterOutlined />}
                            size="large"
                            block
                            className="h-12 flex items-center justify-center gap-2"
                        >
                            <span>Bộ lọc</span>
                            {(filters.gender.length + filters.scent.length + filters.priceRange.length) > 0 && (
                                <div className="bg-black text-white text-xs px-2 py-1 rounded-full font-medium ml-2">
                                    {filters.gender.length + filters.scent.length + filters.priceRange.length}
                                </div>
                            )}
                        </Button>
                    </div>

                    {/* Mobile Filter Sidebar */}
                    {showMobileFilter && (
                        <FilterSidebar
                            filters={filters}
                            filtersData={filtersData}
                            onFilterChange={handleFilterChange}
                            onClearFilters={clearFilters}
                            isMobile={true}
                            onClose={() => setShowMobileFilter(false)}
                            loading={loadingData}
                        />
                    )}

                    {/* Products Section */}
                    <div className="flex-1 products-section">
                        <Card className="mb-6" bodyStyle={{ padding: '16px' }}>
                            <Row justify="space-between" align="middle" gutter={[16, 16]}>
                                <Col xs={24} sm={12}>
                                    <Typography.Text type="secondary">
                                        {loadingData ? (
                                            <Space>
                                                <Spin size="small" />
                                                <span>Đang tải...</span>
                                            </Space>
                                        ) : (
                                            <>
                                                Hiển thị <Typography.Text strong>{((pagination.current - 1) * pagination.per_page) + 1}-{Math.min(pagination.current * pagination.per_page, pagination.total)}</Typography.Text> trong số{' '}
                                                <Typography.Text strong>{pagination.total}</Typography.Text> sản phẩm
                                            </>
                                        )}
                                    </Typography.Text>
                                </Col>

                                <Col xs={24} sm={12}>
                                    <Space size="middle" className="">
                                        <Space>
                                            <SortAscendingOutlined className="text-gray-500" />
                                            <Select
                                                value={sortBy}
                                                onChange={handleSortChange}
                                                options={sortOptions}
                                                loading={loadingData}
                                            />
                                        </Space>

                                     
                                    </Space>
                                </Col>
                            </Row>
                        </Card>

                        {/* Products Grid/List */}
                        {loadingData ? (
                            <div className="text-center py-16">
                                <Spin size="large" />
                                <div className="mt-4">
                                    <Typography.Text type="secondary">Đang tải sản phẩm...</Typography.Text>
                                </div>
                            </div>
                        ) : products.length === 0 ? (
                            <Empty
                                image={Empty.PRESENTED_IMAGE_SIMPLE}
                                description={
                                    <Space direction="vertical" size="small">
                                        <Typography.Text strong>Không tìm thấy sản phẩm</Typography.Text>
                                        <Typography.Text type="secondary">Thử thay đổi bộ lọc hoặc từ khóa tìm kiếm</Typography.Text>
                                    </Space>
                                }
                            >
                                <Button
                                    type="primary"
                                    onClick={clearFilters}
                                    style={{ backgroundColor: '#8B2635' }}
                                >
                                    Xóa bộ lọc
                                </Button>
                            </Empty>
                        ) : (
                            <div className={`grid mt-4 gap-6 ${viewMode === 'grid'
                                ? 'grid-cols-1 sm:grid-cols-2 lg:grid-cols-3'
                                : 'grid-cols-1'
                                }`}>
                                {products.map((product) => (
                                    <ProductCard
                                        key={product.id}
                                        product={product}
                                        viewMode={viewMode}
                                    />
                                ))}
                            </div>
                        )}

                        {/* Pagination */}
                        {pagination.total > pagination.per_page && (
                            <div className="flex justify-center mt-12">
                                <Pagination
                                    current={pagination.current}
                                    total={pagination.total}
                                    pageSize={pagination.per_page}
                                    onChange={handlePageChange}
                                    showSizeChanger={false}
                                    showQuickJumper={true}
                                    showTotal={(total, range) =>
                                        `${range[0]}-${range[1]} trong ${total} sản phẩm`
                                    }
                                    className="custom-pagination"
                                    disabled={loadingData}
                                />
                            </div>
                        )}
                    </div>
                </div>
            </div>

            <style jsx>{`
                .custom-pagination .ant-pagination-item-active {
                    background-color: #8B2635 !important;
                    border-color: #8B2635 !important;
                }
                .custom-pagination .ant-pagination-item-active a {
                    color: white !important;
                }
                .custom-pagination .ant-pagination-item:hover {
                    border-color: #8B2635 !important;
                }
                .custom-pagination .ant-pagination-item:hover a {
                    color: #8B2635 !important;
                }
            `}</style>
        </section>
    );
};

export default ProductsLists;
