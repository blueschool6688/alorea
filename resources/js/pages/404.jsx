import { ArrowLeftOutlined, HomeOutlined } from '@ant-design/icons';
import { Button, Space, Typography } from 'antd';
import { useNavigate } from 'react-router-dom';

const { Title, Text, Paragraph } = Typography;

const NotFound = () => {
    const navigate = useNavigate();

    const handleGoBack = () => {
        navigate(-1);
    };

    const handleGoHome = () => {
        navigate('/');
    };

    return (
        <div className="min-h-screen flex flex-col items-center justify-center bg-white px-4 !font-sans">
            <div className="text-center max-w-lg">
                {/* 404 Stylized Text */}
                <div className="relative mb-6">
                    <Title
                        style={{
                            fontSize: '160px',
                            margin: 0,
                            lineHeight: 1,
                            fontWeight: 800,
                            opacity: 0.05,
                            color: '#000'
                        }}
                    >
                        404
                    </Title>
                    <div className="absolute inset-0 flex items-center justify-center !font-serif">
                        <Title
                            level={1}
                            style={{
                                margin: 0,
                                fontSize: '54px',
                                fontWeight: 700,
                            }}
                        >
                            Không tìm thấy
                        </Title>
                    </div>
                </div>

                {/* Message */}
                <Paragraph
                    style={{
                        fontSize: '16px',
                        marginBottom: '32px',
                        color: '#666'
                    }}
                >
                    Trang bạn đang tìm kiếm không tồn tại hoặc đã bị di chuyển.
                    Vui lòng kiểm tra lại đường dẫn hoặc quay lại trang chủ.
                </Paragraph>

                {/* Divider element */}
                <div
                    className="w-16 h-1 bg-black mx-auto mb-8"
                    style={{ opacity: 0.8 }}
                ></div>

                {/* Actions */}
                <Space size="middle">
                    <Button
                        icon={<ArrowLeftOutlined />}
                        onClick={handleGoBack}
                        size="large"
                    >
                        Quay lại
                    </Button>
                    <Button
                        type="primary"
                        icon={<HomeOutlined />}
                        onClick={handleGoHome}
                        size="large"
                        style={{
                            backgroundColor: "#000",
                            borderColor: "#000"
                        }}
                    >
                        Trang chủ
                    </Button>
                </Space>
            </div>
        </div>
    );
};

export default NotFound;
