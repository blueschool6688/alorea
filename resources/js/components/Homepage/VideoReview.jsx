import { useSettings } from '@/lib/context/SettingContext';
import { StarFilled } from '@ant-design/icons';
import { Card, Col, Row } from 'antd';

const VideoReview = () => {
    const { settings } = useSettings();

    // Mock data giống như trong hình
    const testimonials = [
        {
            id: 1,
            name: "Đức Lộc",
            position: "Nhân viên thiết kế",
            avatar: "https://res.cloudinary.com/dcbsaugq3/image/upload/v1760364758/Anh-profile-nam-3-min.jpg_xko4ey.webp",
            rating: 5,
            content: "Nghe tên “Wild Soul” tưởng hương kiểu gắt, ai ngờ thơm cực kỳ nam tính luôn. Mùi đầu hơi cam chanh, sau đó có quế với gỗ nhẹ, đúng kiểu đàn ông phong trần nhưng sạch sẽ. Mình xịt trước khi đi làm hoặc gặp khách, ai cũng bảo thơm “một cách dễ chịu”. Hương bám tầm 7 tiếng, càng về sau càng ấm. Ai mê vibe tự do, mạnh mẽ kiểu “bad boy chuẩn mực” chắc chắn sẽ khoái mùi này."
        },
        {
            id: 2,
            name: "Khánh Vy",
            position: "Sinh viên ngành thời trang",
            avatar: "https://res.cloudinary.com/dcbsaugq3/image/upload/v1760364719/Chup-anh-chan-dung-chinh-dien_qorejf.jpg",
            rating: 5,
            content: "Mùi này đúng kiểu “gây nghiện” luôn á! Mới xịt nghe tươi tắn, sau đó lại ngọt ấm dần lên kiểu sang sang. Mình dùng đi học, bạn ngồi cạnh hỏi liền “xịt nước hoa gì thơm thế?”. Thơm mà không bị hắc hay gắt, giữ hương ổn lắm luôn, buổi sáng xịt mà chiều vẫn còn thoang thoảng. Hợp mấy bạn nữ tự tin, thích sự quyến rũ nhẹ nhàng nha. Mình cho 10/10 luôn, chai này dùng xong chắc mua lại."
        },
        {
            id: 3,
            name: "Mai Phương",
            position: "Nhân viên văn phòng",
            avatar: "https://res.cloudinary.com/dcbsaugq3/image/upload/v1760364686/photo-1-1610602796546963495282_spffbe.jpg",
            rating: 5,
            content: "Trước giờ mình không thích nước hoa ngọt, mà lạ là Amber Muse lại làm mình mê. Mùi đầu nhẹ, sau đó ấm kiểu dễ chịu cực, có chút vani với gỗ mà không bị ngấy. Dùng đi làm rất hợp, đồng nghiệp cứ hỏi mùi gì mà “thơm kiểu hiền hiền, ấm ấm”. Mình thấy mùi này hợp mấy cô nàng thích sự dịu dàng, trưởng thành nhưng không “bà thím”. Hương bám trên áo tới tận tối, xịt buổi sáng là đủ tự tin cả ngày luôn!"
        }
    ];

    const renderStars = (rating) => {
        return Array.from({ length: 5 }, (_, index) => (
            <StarFilled
                key={index}
                className={`!text-lg ${index < rating ? '!text-yellow-400' : '!text-gray-300'}`}
            />
        ));
    };

    return (
        <section className="py-16 bg-gradient-to-b from-gray-50 to-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="text-center mb-16">
                    <h2 className="font-serif text-black text-4xl md:text-5xl lg:text-6xl font-light mb-8 tracking-wide">
                        KHÁCH HÀNG NÓI GÌ VỂ ALORÉA
                    </h2>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto mb-8"></div>
                </div>

                {/* Testimonials Grid */}
                <Row gutter={[32, 32]} className="mb-12">
                    {testimonials.map((testimonial) => (
                        <Col xs={24} md={8} key={testimonial.id}>
                            <Card
                                className="h-full shadow-lg hover:shadow-xl transition-all duration-300 border-0 rounded-2xl"
                                bodyStyle={{
                                    padding: '2rem',
                                    height: '100%',
                                    display: 'flex',
                                    flexDirection: 'column',
                                    justifyContent: 'space-between'
                                }}
                            >
                                {/* Rating Stars */}
                                <div className="flex justify-center mb-6">
                                    <div className="flex gap-1">
                                        {renderStars(testimonial.rating)}
                                    </div>
                                </div>

                                {/* Content */}
                                <div className="text-center mb-8 flex-grow">
                                    <p className="text-gray-700 text-base leading-relaxed font-sans">
                                        "{testimonial.content}"
                                    </p>
                                </div>

                                {/* Customer Info */}
                                <div className="text-center">
                                    <div className="mb-4">
                                        <img
                                            width={80}
                                            height={80}
                                            className="rounded-full mx-auto object-cover border-4 border-transparent transition-all duration-300 hover:border-burgundy-primary hover:scale-105"
                                            src={testimonial.avatar}
                                            alt={`${testimonial.name} avatar`}
                                            onError={(e) => {
                                                e.target.src = 'https://via.placeholder.com/80x80/cccccc/666666?text=' + testimonial.name.charAt(0);
                                            }}
                                        />
                                    </div>
                                    <h4 className="font-bold text-lg text-black mb-1">
                                        {testimonial.name}
                                    </h4>
                                    <p className="text-gray-500 text-sm">
                                        {testimonial.position}
                                    </p>
                                </div>
                            </Card>
                        </Col>
                    ))}
                </Row>
            </div>

            {/* Custom Styles */}
            <style jsx>{`
                .ant-card {
                    background: linear-gradient(135deg, #ffffff 0%, #fafafa 100%);
                }

                .ant-card:hover {
                    transform: translateY(-8px);
                }

                .ant-card-body {
                    background: transparent;
                }

                @media (max-width: 768px) {
                    .ant-card-body {
                        padding: 1.5rem !important;
                    }
                }

                /* Star animation */
                .anticon-star {
                    transition: all 0.3s ease;
                }

                .ant-card:hover .anticon-star {
                    transform: scale(1.1);
                }

                /* Avatar hover effect */
                .ant-avatar {
                    transition: all 0.3s ease;
                    border: 4px solid transparent;
                }

                .ant-card:hover .ant-avatar {
                    border-color: var(--burgundy-primary, #8B2635);
                    transform: scale(1.05);
                }

                /* Button styles */
                button {
                    font-family: inherit;
                    letter-spacing: 0.5px;
                }
            `}</style>
        </section>
    );
};

export default VideoReview;

