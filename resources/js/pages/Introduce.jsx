import { useSettings } from '@/lib/context/SettingContext';
import { applySEOToDocument, generateIntroduceSEO } from '@/utils/seoUtils';
import {
    ClockCircleOutlined,
    CrownOutlined,
    HeartOutlined,
    LoadingOutlined,
    PlayCircleOutlined,
    SafetyOutlined,
    StarOutlined,
    TrophyOutlined
} from '@ant-design/icons';
import {
    Button,
    Card,
    Col,
    Image,
    Modal,
    Progress,
    Row,
    Spin,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { Link } from 'react-router-dom';
const { Title, Text, Paragraph } = Typography;

const Introduce = () => {
    const [selectedVideo, setSelectedVideo] = useState(null);
    const { settings, loading } = useSettings();

    // Brand story data
    const brandStory = {
        mission: "ALORÉA được ra đời từ khát vọng kiến tạo một thương hiệu nước hoa quốc tế, nhưng vẫn giữ trọn dấu ấn tinh tế Á Đông. Mỗi chai nước hoa ALORÉA là một câu chuyện hương thơm, chạm đến cảm xúc, nâng tầm phong cách và khơi dậy sự tự tin.",
        tagline: "ALORÉA không chỉ là nước hoa – ALORÉA là phong cách sống.",
        subtitle: "Mỗi chai nước hoa là một dấu ấn riêng biệt, giúp bạn tỏa sáng và tự tin ở bất cứ nơi đâu."
    };

    // Signature collections
    const signatureCollections = [
        {
            name: "Bloom Noir",
            description: "Nữ tính, quyến rũ, ngọt ngào như nụ hồng e ấp trong đêm",
            color: "from-pink-500 to-purple-600",
            image: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=400&h=400&fit=crop"
        },
        {
            name: "Rouge Elixir",
            description: "Nồng nàn, mạnh mẽ, cháy bỏng như ngọn lửa của đam mê",
            color: "from-red-500 to-rose-600",
            image: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop"
        },
        {
            name: "Amber Muse",
            description: "Ấm áp, bí ẩn, kiêu sa, để lại dấu ấn khó phai",
            color: "from-amber-500 to-orange-600",
            image: "https://images.unsplash.com/photo-1541643600914-78b084683601?w=400&h=400&fit=crop"
        },
        {
            name: "Wild Soul",
            description: "Tự do, nam tính, phóng khoáng như tâm hồn hoang dã",
            color: "from-green-500 to-teal-600",
            image: "https://images.unsplash.com/photo-1592945403244-b3fbafd7f539?w=400&h=400&fit=crop"
        },
        {
            name: "Azure Spirit",
            description: "Tươi mát, lịch lãm, mang hơi thở đại dương",
            color: "from-blue-500 to-cyan-600",
            image: "https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=400&h=400&fit=crop"
        },
        {
            name: "Obsidian Oud",
            description: "Quyền lực, bí ẩn, sang trọng vượt thời gian",
            color: "from-gray-800 to-black",
            image: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=400&h=400&fit=crop"
        }
    ];

    // Core values - updated with brand info
    const coreValues = [
        {
            icon: <StarOutlined className="text-4xl" />,
            title: "Chất lượng quốc tế",
            description: "Lựa chọn nguyên liệu cao cấp, áp dụng công nghệ châu Âu hiện đại để tạo nên những sản phẩm đẳng cấp thế giới.",
            color: "from-yellow-400 to-yellow-600"
        },
        {
            icon: <CrownOutlined className="text-4xl" />,
            title: "Tinh tế & đẳng cấp",
            description: "Thiết kế sang trọng, hương thơm độc đáo – khẳng định phong cách riêng biệt của người sở hữu.",
            color: "from-purple-400 to-indigo-600"
        },
        {
            icon: <HeartOutlined className="text-4xl" />,
            title: "Thân thiện & gần gũi",
            description: "ALORÉA luôn đồng hành cùng khách hàng, biến mỗi khoảnh khắc trở thành một trải nghiệm hương thơm đáng nhớ.",
            color: "from-pink-400 to-rose-600"
        }
    ];

    const qualityFeatures = [
        {
            icon: <ClockCircleOutlined />,
            title: "Bám mùi 8-12 giờ",
            description: "Công nghệ giữ hương tiên tiến, đảm bảo mùi hương bền vững suốt ngày dài"
        },
        {
            icon: <StarOutlined />,
            title: "Nguyên liệu cao cấp",
            description: "Tinh dầu thiên nhiên nhập khẩu từ Pháp, Ý và các vùng đất nổi tiếng"
        },
        {
            icon: <SafetyOutlined />,
            title: "An toàn tuyệt đối",
            description: "Đạt chuẩn quốc tế, không gây kích ứng, phù hợp mọi loại da"
        },
        {
            icon: <TrophyOutlined />,
            title: "Chứng nhận chất lượng",
            description: "Được kiểm định bởi các tổ chức uy tín và đạt nhiều giải thưởng"
        }
    ];
    const processVideos = [
        {
            id: 1,
            title: "Quy trình sản xuất",
            thumbnail: "https://images.unsplash.com/photo-1588405748880-12d1d2a59d75?w=600&h=400&fit=crop",
            videoUrl: settings?.intro_video_manufacture_stream_url,
            description: "Khám phá quy trình sản xuất tỉ mỉ từ nguyên liệu thô đến sản phẩm hoàn thiện"
        },
        {
            id: 2,
            title: "Thiết kế đóng gói",
            thumbnail: "https://images.unsplash.com/photo-1594735797823-a8c4b0b71db5?w=600&h=400&fit=crop",
            videoUrl: settings?.intro_video_design_stream_url,
            description: "Nghệ thuật đóng gói tinh tế, mỗi chi tiết đều được chăm chút kỹ lưỡng"
        }
    ];

    // Company statistics
    const statistics = [
        { title: "Năm thành lập", value: 2020, suffix: "" },
        { title: "Sản phẩm", value: 50, suffix: "+" },
        { title: "Khách hàng", value: 10000, suffix: "+" },
        { title: "Quốc gia", value: 15, suffix: "" }
    ];

    const handleVideoPlay = (video) => {
        setSelectedVideo(video);
    };

    // Apply SEO when component mounts
    useEffect(() => {
        const seoData = generateIntroduceSEO({
            brandStory,
            signatureCollections,
            coreValues,
            statistics
        });
        applySEOToDocument(seoData);

        return () => {
            // Reset title when component unmounts
            document.title = 'ALORÉA - Nước Hoa Chính Hãng';
        };
    }, []);

    if (loading) {
        return (
            <div className="bg-white min-h-screen">
                <div className="flex items-center justify-center min-h-screen">
                    <Spin size="large" indicator={<LoadingOutlined style={{ fontSize: 48 }} spin />} />
                </div>
            </div>
        );
    }

    return (
        <div className="min-h-screen">
            {/* Hero Section - Updated with brand story */}
            <div className="relative bg-gradient-to-br from-burgundy-primary to-burgundy-dark text-white py-32">
                <div className="absolute inset-0 bg-black/20"></div>
                <div className="relative z-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <Row gutter={[48, 48]} align="middle">
                        <Col xs={24} lg={12}>
                            <div className="space-y-8">
                                <div>
                                    <Title level={1} className="!text-white !text-6xl md:!text-7xl !font-serif !mb-4">
                                        Thương Hiệu ALORÉA
                                    </Title>
                                    <div className="w-32 h-1 bg-gradient-to-r from-white to-gray-400 mb-8"></div>
                                </div>

                                <Paragraph className="!text-white/90 !font-sans !text-xl !leading-relaxed max-w-2xl !mb-6">
                                    {brandStory.mission}
                                </Paragraph>
                                <div className="pt-8">
                                    <Link to="/collections">
                                        <Button
                                            type="primary"
                                            size="large"
                                            className="!bg-white !text-black !border-white hover:!bg-gray-100 !h-14 !px-8 !text-lg !font-medium"
                                        >
                                            Khám phá bộ sưu tập
                                        </Button>
                                    </Link>
                                </div>
                            </div>
                        </Col>

                        <Col xs={24} lg={12}>
                            <div className="relative">
                                <div className="absolute inset-0 bg-gradient-to-br from-white/20 to-transparent rounded-3xl transform rotate-3"></div>
                                <Image
                                    src={settings?.logo_url}
                                    alt="ALORÉA Perfume"
                                    className="relative z-10 w-full rounded-3xl shadow-2xl"
                                    preview={false}
                                />
                            </div>
                        </Col>
                    </Row>
                </div>
            </div>

            {/* Signature Collections - NEW SECTION */}
            <div className="py-20 bg-gradient-to-br from-gray-50 to-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <Title level={2} className="!text-4xl !font-serif !mb-6 !text-black">
                            Bộ Sưu Tập Đặc Trưng
                        </Title>
                        <div className="w-24 h-1 bg-black mx-auto mb-8"></div>
                        <Paragraph className="!text-gray-600 !text-lg max-w-3xl mx-auto !font-sans">
                            Mỗi chai nước hoa ALORÉA là một câu chuyện hương thơm, chạm đến cảm xúc và nâng tầm phong cách
                        </Paragraph>
                    </div>

                    <Row gutter={[32, 32]}>

                        {signatureCollections.map((collection, index) => (
                            <Col xs={24} md={8} key={index}>
                                <Card
                                    className="h-full text-center border-2 border-gray-100 hover:border-black hover:shadow-2xl transition-all duration-500 group"
                                    bodyStyle={{ padding: '3rem 2rem' }}
                                >
                                    <Title level={3} className="!text-2xl !font-serif !mb-4 !text-black">
                                        {collection.name}
                                    </Title>

                                    <Paragraph className="!text-gray-700 !leading-relaxed !text-base !font-sans">
                                        {collection.description}
                                    </Paragraph>
                                </Card>
                            </Col>
                        ))}
                    </Row>

                    <div className="text-center mt-12">
                        <Link to="/products">
                            <Button
                                type="primary"
                                size="large"
                                className="!bg-black !border-black hover:!bg-gray-800 !h-12 !px-8 !text-base"
                            >
                                Xem tất cả sản phẩm
                            </Button>
                        </Link>
                    </div>
                </div>
            </div>

            {/* Core Values - Updated */}
            <div className="py-20 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <Title level={2} className="!text-4xl !font-serif !mb-6 !text-black">
                            Sứ Mệnh & Giá Trị
                        </Title>
                        <div className="w-24 h-1 bg-black mx-auto mb-8"></div>
                        <Paragraph className="!text-gray-600 !text-lg max-w-3xl mx-auto !font-sans">
                            Ba giá trị cốt lõi tạo nên bản sắc riêng biệt của thương hiệu ALORÉA
                        </Paragraph>
                    </div>

                    <Row gutter={[32, 32]}>
                        {coreValues.map((value, index) => (
                            <Col xs={24} md={8} key={index}>
                                <Card
                                    className="h-full text-center border-2 border-gray-100 hover:border-black hover:shadow-2xl transition-all duration-500 group"
                                    bodyStyle={{ padding: '3rem 2rem' }}
                                >
                                    <div className="w-20 h-20 mx-auto mb-6 rounded-full bg-black flex items-center justify-center text-white group-hover:scale-110 transition-transform duration-300">
                                        {value.icon}
                                    </div>

                                    <Title level={3} className="!text-2xl !font-serif !mb-4 !text-black">
                                        {value.title}
                                    </Title>

                                    <Paragraph className="!text-gray-700 !leading-relaxed !text-base !font-sans">
                                        {value.description}
                                    </Paragraph>
                                </Card>
                            </Col>
                        ))}
                    </Row>
                </div>
            </div>

            {/* Manufacturing Process */}
            <div className="py-20 bg-gray-50">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <Title level={2} className="!text-4xl !font-serif !mb-6 !text-black">
                            Quy Trình Sản Xuất
                        </Title>
                        <div className="w-24 h-1 bg-black mx-auto mb-8"></div>
                        <Paragraph className="!text-gray-600 !text-lg max-w-3xl mx-auto !font-sans">
                            Khám phá hành trình tạo nên những chai nước hoa hoàn hảo
                        </Paragraph>
                    </div>

                    <Row gutter={[32, 32]}>
                        {processVideos.map((video) => (
                            <Col xs={24} lg={12} key={video.id}>
                                <Card
                                    className="overflow-hidden border-2 border-gray-200 hover:border-black hover:shadow-xl transition-all duration-500 group"
                                    bodyStyle={{ padding: 0 }}
                                >
                                    <div className="relative aspect-video overflow-hidden">
                                        {video.videoUrl ? (
                                            <video
                                                src={video.videoUrl}
                                                controls
                                                preload='metadata'
                                                poster={video.thumbnail}
                                                className="w-full h-full object-cover"
                                                style={{ background: "#000" }}
                                                title={video.title}
                                            >
                                                Trình duyệt của bạn không hỗ trợ video.
                                            </video>
                                        ) : (
                                            <Image
                                                src={video.thumbnail}
                                                alt={video.title}
                                                className="w-full h-full object-cover"
                                                preview={false}
                                            />
                                        )}

                                        {/* Nếu muốn overlay nút play khi là ảnh, còn video thì không cần */}
                                        {!video.videoUrl && (
                                            <div
                                                className="absolute inset-0 bg-black/40 group-hover:bg-black/60 transition-colors duration-300 flex items-center justify-center z-10 cursor-pointer"
                                                onClick={() => handleVideoPlay(video)}
                                            >
                                                <div className="text-center">
                                                    <PlayCircleOutlined className="text-6xl text-white mb-4 group-hover:scale-110 transition-transform duration-300" />
                                                    <Text className="text-white text-xl font-semibold">
                                                        Xem video
                                                    </Text>
                                                </div>
                                            </div>
                                        )}
                                    </div>

                                    <div className="p-8">
                                        <Title level={3} className="!text-2xl !font-serif !mb-4 !text-black">
                                            {video.title}
                                        </Title>
                                        <Paragraph className="!text-gray-700 !leading-relaxed">
                                            {video.description}
                                        </Paragraph>
                                    </div>
                                </Card>
                            </Col>
                        ))}
                    </Row>
                </div>
            </div>
            <div className="py-20 bg-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="text-center mb-16">
                        <Title level={2} className="!text-4xl !font-serif !mb-6 !text-black">
                            Cam Kết Chất Lượng
                        </Title>
                        <div className="w-24 h-1 bg-black mx-auto mb-8"></div>
                        <Paragraph className="!text-gray-600 !text-lg max-w-3xl mx-auto !font-sans">
                            Những tiêu chuẩn khắt khe để đảm bảo mỗi sản phẩm đều hoàn hảo
                        </Paragraph>
                    </div>

                    <Row gutter={[32, 32]}>
                        {qualityFeatures.map((feature, index) => (
                            <Col xs={24} md={12} key={index}>
                                <Card
                                    className="h-full border-2 border-gray-100 hover:border-black hover:shadow-xl transition-all duration-500"
                                    bodyStyle={{ padding: '2rem' }}
                                >
                                    <div className="flex items-start space-x-4">
                                        <div className="w-12 h-12 bg-black text-white rounded-full flex items-center justify-center text-xl flex-shrink-0">
                                            {feature.icon}
                                        </div>
                                        <div>
                                            <Title level={4} className="!text-xl !font-serif !mb-3 !text-black">
                                                {feature.title}
                                            </Title>
                                            <Paragraph className="!text-gray-700 !leading-relaxed !mb-0">
                                                {feature.description}
                                            </Paragraph>
                                        </div>
                                    </div>
                                </Card>
                            </Col>
                        ))}
                    </Row>

                    {/* Quality Progress */}
                    <div className="mt-16 bg-gray-50 p-12 rounded-2xl">
                        <Title level={3} className="!text-2xl !font-serif !mb-8 !text-center !text-black">
                            Chỉ Số Chất Lượng
                        </Title>

                        <Row gutter={[32, 32]}>
                            <Col xs={24} md={6}>
                                <div className="text-center">
                                    <Progress
                                        type="circle"
                                        percent={95}
                                        strokeColor="#000"
                                        size={120}
                                        format={() => <span className="text-2xl font-bold">95%</span>}
                                    />
                                    <Text className="block mt-4 text-gray-700 font-medium">Độ hài lòng khách hàng</Text>
                                </div>
                            </Col>
                            <Col xs={24} md={6}>
                                <div className="text-center">
                                    <Progress
                                        type="circle"
                                        percent={12}
                                        strokeColor="#000"
                                        size={120}
                                        format={() => <span className="text-2xl font-bold">12h</span>}
                                    />
                                    <Text className="block mt-4 text-gray-700 font-medium">Thời gian bám mùi</Text>
                                </div>
                            </Col>
                            <Col xs={24} md={6}>
                                <div className="text-center">
                                    <Progress
                                        type="circle"
                                        percent={100}
                                        strokeColor="#000"
                                        size={120}
                                        format={() => <span className="text-2xl font-bold">100%</span>}
                                    />
                                    <Text className="block mt-4 text-gray-700 font-medium">Nguyên liệu tự nhiên</Text>
                                </div>
                            </Col>
                            <Col xs={24} md={6}>
                                <div className="text-center">
                                    <Progress
                                        type="circle"
                                        percent={99}
                                        strokeColor="#000"
                                        size={120}
                                        format={() => <span className="text-2xl font-bold">99%</span>}
                                    />
                                    <Text className="block mt-4 text-gray-700 font-medium">Độ an toàn</Text>
                                </div>
                            </Col>
                        </Row>
                    </div>
                </div>
            </div>

            {/* Brand Promise - NEW SECTION */}
            <div className="py-20 bg-white !text-black">
                <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <Title level={2} className="!text-black !text-4xl !font-serif !mb-8">
                        Lời Kết
                    </Title>

                    <div className="space-y-6">
                        <Paragraph className="!text-black/90 !text-2xl !font-serif !italic !leading-relaxed">
                            "{brandStory.tagline}"
                        </Paragraph>

                        <Paragraph className="!text-black/80 !text-lg !leading-relaxed max-w-2xl mx-auto">
                            {brandStory.subtitle}
                        </Paragraph>

                        <div className="pt-8">
                            <Text className="!text-black !text-xl !font-medium">
                                👉 Hãy để ALORÉA đồng hành cùng bạn – Lan tỏa hương thơm, chạm đến trái tim.
                            </Text>
                        </div>
                    </div>
                </div>
            </div>

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
                            src={selectedVideo.videoUrl}
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
                .video-modal .ant-modal-content {
                    padding: 0;
                    background: black;
                }
                .video-modal .ant-modal-close {
                    font-size: 20px;
                    color: white;
                }
                .custom-timeline .ant-timeline-item-tail {
                    border-left: 2px solid #000;
                }
                .custom-timeline .ant-timeline-item-head {
                    background: #000;
                    border-color: #000;
                }
            `}</style>
        </div>
    );
};

export default Introduce;
