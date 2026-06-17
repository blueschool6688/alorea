import { ClearOutlined, CloseOutlined, FilterOutlined } from '@ant-design/icons';
import { Button, Card, Checkbox, Divider, Drawer, Space, Spin, Typography } from 'antd';

const { Title, Text } = Typography;

const FilterSidebar = ({
    filters,
    filtersData,
    onFilterChange,
    onClearFilters,
    isMobile = false,
    onClose,
    loading = false
}) => {
    const getGenderOptions = () => {
        if (!filtersData.gender || typeof filtersData.gender !== 'object') {
            return [];
        }
        return Object.entries(filtersData.gender).map(([key, label]) => ({
            value: key,
            label: label
        }));
    };

    // Get scents array directly from API
    const getScentOptions = () => {
        if (!filtersData.scents || !Array.isArray(filtersData.scents)) {
            return [];
        }
        return filtersData.scents;
    };

    const priceRanges = [
        { value: '0-500', label: 'Dưới 500.000đ' },
        { value: '500-1000', label: '500.000đ - 1.000.000đ' },
        { value: '1000-2000', label: '1.000.000đ - 2.000.000đ' },
        { value: '2000+', label: 'Trên 2.000.000đ' }
    ];

    const getTotalActiveFilters = () => {
        return (filters.gender?.length || 0) +
            (filters.scent?.length || 0) +
            (filters.priceRange?.length || 0);
    };

    // Handle filter change with loading state
    const handleFilterChange = (filterType, value, checked) => {
        if (loading) return; // Prevent changes while loading
        onFilterChange(filterType, value, checked);
    };

    // Handle clear filters with loading state
    const handleClearFilters = () => {
        if (loading) return;
        onClearFilters();
    };

    const FilterContent = () => (
        <div className="h-full overflow-y-auto">
            {/* Header */}
            <div className="flex items-center justify-between mb-6 px-1">
                <Space align="center">
                    <FilterOutlined className="text-lg text-burgundy-primary" />
                    <Title level={4} className="!mb-0 !text-gray-800">Bộ lọc</Title>
                    {getTotalActiveFilters() > 0 && (
                        <div className="bg-burgundy-primary text-white text-xs px-2 py-1 rounded-full font-medium">
                            {getTotalActiveFilters()}
                        </div>
                    )}
                    {loading && <Spin size="small" />}
                </Space>

                {/* Close button for mobile */}
                {isMobile && (
                    <Button
                        type="text"
                        icon={<CloseOutlined />}
                        onClick={onClose}
                        className="!p-2 !text-gray-600 hover:!text-burgundy-primary hover:!bg-burgundy-50"
                        disabled={loading}
                        size="large"
                    />
                )}
            </div>

            {/* Clear all filters button */}
            {getTotalActiveFilters() > 0 && (
                <Button
                    icon={<ClearOutlined />}
                    onClick={handleClearFilters}
                    className="w-full mb-6 !border-burgundy-primary !text-burgundy-primary hover:!bg-burgundy-50"
                    loading={loading}
                    disabled={loading}
                    size="large"
                >
                    Xóa tất cả bộ lọc ({getTotalActiveFilters()})
                </Button>
            )}

            {/* Gender Filter */}
            <div className="mb-8">
                <Text strong className="block mb-4 text-base text-gray-800">
                    Giới tính
                </Text>
                <Space direction="vertical" size="middle" className="w-full">
                    {getGenderOptions().length > 0 ? (
                        getGenderOptions().map(option => (
                            <Checkbox
                                key={option.value}
                                checked={filters.gender?.includes(option.value)}
                                onChange={(e) => handleFilterChange('gender', option.value, e.target.checked)}
                                disabled={loading}
                                className="!text-gray-700 hover:!text-burgundy-primary text-base py-1"
                            >
                                <span className="ml-2">{option.label}</span>
                            </Checkbox>
                        ))
                    ) : (
                        <Text type="secondary" className="text-sm py-2">
                            Không có dữ liệu giới tính
                        </Text>
                    )}
                </Space>
            </div>

            <Divider className="!my-6" />

            {/* Scent Filter */}
            <div className="mb-8">
                <Text strong className="block mb-4 text-base text-gray-800">
                    Hương thơm
                    {loading && <Spin size="small" className="ml-2" />}
                </Text>
                <Space direction="vertical" size="middle" className="w-full max-h-60 overflow-y-auto">
                    {getScentOptions().length > 0 ? (
                        getScentOptions().map(scent => (
                            <Checkbox
                                key={scent.id}
                                checked={filters.scent?.includes(scent.id.toString())}
                                onChange={(e) => handleFilterChange('scent', scent.id.toString(), e.target.checked)}
                                disabled={loading}
                                className="!text-gray-700 hover:!text-burgundy-primary text-base py-1 w-full"
                            >
                                <div className="ml-2 flex flex-col">
                                    <span className="font-medium">{scent.name}</span>
                                    {scent.slug && (
                                        <Text type="secondary" className="text-xs">
                                            {scent.slug}
                                        </Text>
                                    )}
                                </div>
                            </Checkbox>
                        ))
                    ) : (
                        <Text type="secondary" className="text-sm py-2">
                            Không có dữ liệu hương thơm
                        </Text>
                    )}
                </Space>
            </div>

            <Divider className="!my-6" />

            {/* Price Range Filter */}
            <div className="mb-8">
                <Text strong className="block mb-4 text-base text-gray-800">
                    Khoảng giá
                </Text>
                <Space direction="vertical" size="middle" className="w-full">
                    {priceRanges.map(range => (
                        <Checkbox
                            key={range.value}
                            checked={filters.priceRange?.includes(range.value)}
                            onChange={(e) => handleFilterChange('priceRange', range.value, e.target.checked)}
                            disabled={loading}
                            className="!text-gray-700 hover:!text-burgundy-primary text-base py-1"
                        >
                            <span className="ml-2 font-medium">{range.label}</span>
                        </Checkbox>
                    ))}
                </Space>
            </div>

            {/* Mobile Action Buttons */}
            {isMobile && (
                <div className="sticky bottom-0 bg-white pt-4 pb-2 mt-8 border-t space-y-3">
                    <Button
                        type="primary"
                        className="w-full !h-12 !text-base font-medium"
                        style={{ backgroundColor: '#8B2635', borderColor: '#8B2635' }}
                        onClick={onClose}
                        disabled={loading}
                        size="large"
                    >
                        {loading ? (
                            <Space>
                                <Spin size="small" />
                                <span>Đang áp dụng...</span>
                            </Space>
                        ) : (
                            `Xem kết quả ${getTotalActiveFilters() > 0 ? `(${getTotalActiveFilters()} bộ lọc)` : ''}`
                        )}
                    </Button>
                </div>
            )}
        </div>
    );

    // Mobile version using Drawer
    if (isMobile) {
        return (
            <Drawer
                title={null}
                placement="left"
                onClose={onClose}
                open={true}
                width="100%"
                maxWidth="400px"
                className="filter-drawer"
                closable={false}
                bodyStyle={{
                    padding: '24px',
                    paddingBottom: '80px', // Space for sticky button
                    background: '#fafafa'
                }}
                headerStyle={{ display: 'none' }}
                style={{
                    '.ant-drawer-content': {
                        background: '#fafafa'
                    }
                }}
            >
                <FilterContent />
            </Drawer>
        );
    }

    // Desktop version
    return (
        <div className="w-80 hidden lg:block">
            <Card
                className="sticky top-4 shadow-lg border-0"
                bodyStyle={{
                    padding: '24px',
                    background: '#fafafa'
                }}
                style={{
                    borderRadius: '12px',
                    maxHeight: 'calc(100vh - 100px)',
                    overflow: 'hidden'
                }}
            >
                <FilterContent />
            </Card>
        </div>
    );
};

export default FilterSidebar;
