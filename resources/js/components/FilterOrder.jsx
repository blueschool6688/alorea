import { useDebounce } from '@/hooks/useDebounce';
import { FilterOutlined, SearchOutlined } from '@ant-design/icons';
import { Button, Card, Col, DatePicker, Input, Row, Select } from 'antd';
import { useCallback, useEffect, useState } from 'react';

const { RangePicker } = DatePicker;
const { Option } = Select;

const FilterOrder = ({ onSearch, loading = false, filters, setFilters }) => {
    const [searchValue, setSearchValue] = useState(filters?.search || '');
    const debouncedSearch = useDebounce(searchValue, 300); // Tăng debounce time
    const handleInputChange = useCallback((field, value) => {
        setFilters(prev => ({
            ...prev,
            [field]: value
        }));
    }, [setFilters]);

    // Sync searchValue with external filters (only when filters change externally)
    useEffect(() => {
        if (filters?.search !== searchValue) {
            setSearchValue(filters?.search || '');
        }
    }, [filters?.search]);

    // Update search filter when debounced value changes, but don't trigger API
    useEffect(() => {
        if (debouncedSearch !== filters?.search && searchValue === debouncedSearch) {
            setFilters(prev => ({
                ...prev,
                search: debouncedSearch
            }));
        }
    }, [debouncedSearch, filters?.search, searchValue, setFilters]);

    const handleSearch = () => {
        // Ensure search value is synced before calling API
        const finalFilters = {
            ...filters,
            search: searchValue.trim()
        };

        console.log('FilterOrder: Manual search triggered', finalFilters);
        onSearch(finalFilters);
    };

    const handleReset = () => {
        const resetFilters = {
            search: '',
            status: 'all',
            dateRange: null
        };

        // Reset local search value
        setSearchValue('');

        // Update filters and trigger search in one go
        setFilters(resetFilters);
        console.log('FilterOrder: Reset triggered', resetFilters);
        onSearch(resetFilters);
    };

    return (
        <Card
            size="small"
            className="mb-4 shadow-sm border border-gray-200"
            title={
                <div className="flex items-center gap-2">
                    <FilterOutlined className="text-blue-500" />
                    <span className="font-medium">Bộ lọc</span>
                </div>
            }
        >
            <Row gutter={[16, 16]}>
                <Col xs={24} lg={12} sm={8}>
                    <div className="relative">
                        <Input
                            placeholder="Tìm kiếm mã đơn hàng, tên khách hàng..."
                            prefix={<SearchOutlined />}
                            value={searchValue}
                            onChange={(e) => setSearchValue(e.target.value)}
                            allowClear
                            onPressEnter={handleSearch}
                            className="w-full"
                        />
                    </div>
                </Col>
                <Col xs={24} lg={12} sm={8}>
                    <Select
                        placeholder="Chọn trạng thái"
                        value={filters.status}
                        onChange={(value) => handleInputChange('status', value)}
                        className="w-full"
                        suffixIcon={<FilterOutlined />}
                    >
                        <Option value="all">
                            <span className="flex items-center gap-2">
                                Tất cả trạng thái
                            </span>
                        </Option>
                        <Option value="pending">
                            <span className="flex items-center gap-2">
                                Đang chờ xử lý
                            </span>
                        </Option>
                        <Option value="paid">
                            <span className="flex items-center gap-2">
                                Đã thanh toán
                            </span>
                        </Option>
                        <Option value="failed">
                            <span className="flex items-center gap-2">
                                Đã hủy
                            </span>
                        </Option>
                    </Select>
                </Col>
                <Col xs={24} lg={12} sm={8}>
                    <RangePicker
                        placeholder={['Từ ngày', 'Đến ngày']}
                        value={filters.dateRange}
                        onChange={(dates) => handleInputChange('dateRange', dates)}
                        className="w-full"
                        format="DD/MM/YYYY"
                        allowClear
                    />
                </Col>
                <Col xs={24}>
                    <div className="flex flex-col sm:flex-row gap-2 justify-center sm:justify-end">
                        <Button
                            onClick={handleReset}
                            disabled={loading}
                            className="flex items-center justify-center"
                        >
                            Đặt lại
                        </Button>
                        <Button
                            type="primary"
                            icon={<SearchOutlined />}
                            onClick={handleSearch}
                            loading={loading}
                            disabled={loading}
                            className="flex items-center justify-center"
                            style={{ background: "black", borderColor: "black" }}
                        >
                            {loading ? 'Đang tìm kiếm...' : 'Tìm kiếm'}
                        </Button>
                    </div>
                </Col>
            </Row>
        </Card>
    );
};

// Default props
FilterOrder.defaultProps = {
    loading: false,
    filters: {
        search: '',
        status: 'all',
        dateRange: null
    }
};

export default FilterOrder;
