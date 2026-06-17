import HtmlContent from '@/components/HtmlContent';
import { useCart } from '@/lib/context/CartContext';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import {
    ArrowRightOutlined,
    LoadingOutlined,
    PlayCircleOutlined
} from '@ant-design/icons';
import {
    Alert,
    Button,
    Col,
    Modal,
    Row,
    Space,
    Spin,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
import ProductCard from '../components/ProductCard';


const { Title, Text, Paragraph } = Typography;

const Collections = () => {
    const [selectedVideo, setSelectedVideo] = useState(null);
    const [activeCollection, setActiveCollection] = useState(null);
    const { addToCart } = useCart();
    const [collections, setCollections] = useState([]);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);

    // Fetch collections data from API
    useEffect(() => {
        const fetchCollections = async () => {
            try {
                setLoading(true);
                setError(null);
                const response = await api.get(GeneralPath.GENERAL_DATA_COLLECTIONS_ENDPOINT);

                if (response && response.data) {
                    setCollections(response.data);
                    if (response.data.length > 0) {
                        setActiveCollection(response.data[0].id);
                    }
                } else {
                    throw new Error('Invalid response format');
                }
            } catch (err) {
                console.error('Error fetching collections:', err);
                setError(err.response?.data?.message || err.message || 'Failed to load collections');
            } finally {
                setLoading(false);
            }
        };

        fetchCollections();
    }, []);

    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const handleVideoPlay = (videoUrl, productName) => {
        setSelectedVideo({ url: videoUrl, title: productName });
    };

    // Get current active collection
    const currentCollection = collections.find(col => col.id === activeCollection);

    // Loading state
    if (loading) {
        return (
            <div className="bg-white min-h-screen">
                <div className="flex items-center justify-center min-h-screen">
                    <Spin size="large" indicator={<LoadingOutlined style={{ fontSize: 48 }} spin />} />
                </div>
            </div>
        );
    }

    // Error state
    if (error) {
        return (
            <div className="bg-white min-h-screen">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <Alert
                        message="Lỗi tải dữ liệu"
                        description={error}
                        type="error"
                        showIcon
                        action={
                            <Button size="small" onClick={() => window.location.reload()}>
                                Thử lại
                            </Button>
                        }
                    />
                </div>
            </div>
        );
    }

    // No collections state
    if (!collections.length) {
        return (
            <div className="bg-white min-h-screen">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="text-center">
                        <Title level={2}>Chưa có bộ sưu tập nào</Title>
                        <Paragraph>Vui lòng quay lại sau.</Paragraph>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="bg-white min-h-screen">
            {/* Hero Section */}
            <div className="relative bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white py-20 mt-12">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center space-y-6">
                        <Title level={1} className="!text-white !text-5xl md:!text-6xl !font-serif !mb-4">
                            COLLECTIONS
                        </Title>
                        <div className="w-32 h-1 bg-white mx-auto mb-8"></div>
                        <Paragraph className="!text-white !text-xl !font-sans !max-w-3xl !mx-auto !leading-relaxed">
                            Khám phá những bộ sưu tập nước hoa độc đáo, mỗi câu chuyện một hương thơm.
                            Từ những mùi hương mạnh mẽ dành cho phái mạnh đến những hương thơm quyến rũ cho phái đẹp.
                        </Paragraph>
                    </div>
                </div>
            </div>

            {/* Collection Navigation */}
            <div className="bg-gray-50 py-8 z-30 border-b">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-center">
                        <Space size="large" wrap className="justify-center">
                            {collections.map((collection) => (
                                <Button
                                    key={collection.id}
                                    size="large"
                                    type={activeCollection === collection.id ? 'primary' : 'default'}
                                    className={`!px-12 !py-6 font-serif ${activeCollection === collection.id
                                        ? '!bg-burgundy-primary/80 !border-[#191537] hover:!from-gray-800 hover:!to-black'
                                        : 'border-gray-300 text-gray-700 hover:!bg-burgundy-primary/80 hover:!text-white transition-all duration-600'
                                        }`}
                                    onClick={() => setActiveCollection(collection.id)}
                                >
                                    {collection.title}
                                </Button>
                            ))}
                        </Space>
                    </div>
                </div>
            </div>

            {/* Collection Content */}
            {currentCollection && (
                <div className="py-16">
                    <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                        {/* Collection Header */}
                        <div className="mb-16">
                            <Title level={2} className="!text-4xl !font-serif !mb-4 !text-black !text-center ">
                                {currentCollection.title}
                            </Title>
                            {currentCollection.sub_title && (
                                <Text className="text-xl text-gray-600 font-medium mb-6 block !text-center ">
                                    {currentCollection.sub_title}
                                </Text>
                            )}
                            {currentCollection.description && (
                                <HtmlContent content={currentCollection.description} />
                            )}

                            {/* Main Collection Video */}
                            {currentCollection.video_stream_url && (
                                <div className="mt-12">
                                    <div className="relative max-w-4xl mx-auto">
                                        <div className="relative aspect-video bg-gray-900 rounded-2xl overflow-hidden cursor-pointer group">
                                            <video
                                                src={currentCollection.video_stream_url}
                                                autoPlay
                                                preload='metadata'
                                                muted
                                                loop
                                                playsInline
                                                className="w-full h-full object-cover"
                                            />
                                            <div
                                                className="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors duration-300 z-10"
                                                onClick={() => handleVideoPlay(currentCollection.video_stream_url, `${currentCollection.title} Collection`)}
                                            ></div>
                                            <div
                                                className="absolute inset-0 flex items-center justify-center z-20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"
                                                onClick={() => handleVideoPlay(currentCollection.video_stream_url, `${currentCollection.title} Collection`)}
                                            >
                                                <div className="text-center">
                                                    <PlayCircleOutlined className="text-6xl text-white mb-4 group-hover:scale-110 transition-transform duration-300" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>

                        {/* Products Grid */}
                        {currentCollection.products && currentCollection.products.length > 0 ? (
                            <Row gutter={[32, 32]}>
                                {currentCollection.products.map((product) => (
                                    <Col xs={24} md={12} lg={8} key={product.id}>
                                        <ProductCard
                                            key={product.id}
                                            product={product}
                                        />
                                    </Col>
                                ))}
                            </Row>
                        ) : (
                            <div className="text-center py-16">
                                <Paragraph className="text-gray-500">
                                    Bộ sưu tập này chưa có sản phẩm nào.
                                </Paragraph>
                            </div>
                        )}

                        {/* Call to Action */}
                        <div className="text-center mt-16">
                            <div className="bg-gray-50 p-12 rounded-2xl">
                                <Title level={3} className="!mb-4 !text-black">
                                    Khám phá toàn bộ bộ sưu tập
                                </Title>
                                <Paragraph className="text-gray-600 mb-8 max-w-2xl mx-auto">
                                    Trải nghiệm đầy đủ những mùi hương độc đáo và tìm ra chai nước hoa hoàn hảo cho riêng bạn
                                </Paragraph>
                                <Space size="large">
                                    <Button
                                        size="large"
                                        type="primary"
                                        className="!bg-black !border-black hover:!bg-gray-800 h-12 px-8"
                                    >
                                        <Link to="/products">
                                            Xem tất cả sản phẩm <ArrowRightOutlined />
                                        </Link>
                                    </Button>
                                </Space>
                            </div>
                        </div>
                    </div>
                </div>
            )}

            {/* Video Modal */}
            <Modal
                open={selectedVideo !== null}
                onCancel={() => setSelectedVideo(null)}
                footer={null}
                width="90vw"
                style={{ maxWidth: '1200px' }}
                centered
                className="video-modal"
            >
                {selectedVideo && (
                    <div className="aspect-video">
                        <video
                            src={selectedVideo.url}
                            controls
                            autoPlay
                            muted={false}
                            className="w-full h-full"
                            title={selectedVideo.title}
                        >
                            Trình duyệt của bạn không hỗ trợ video.
                        </video>
                    </div>
                )}
            </Modal>

            {/* Custom Styles */}
            <style jsx>{`
                .line-clamp-3 {
                    display: -webkit-box;
                    -webkit-line-clamp: 3;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }
                .video-modal .ant-modal-content {
                    padding: 0;
                    background: black;
                }
                .video-modal .ant-modal-close {
                    font-size: 20px;
                    color: white;
                }
            `}</style>
        </div>
    );
};

export default Collections;
