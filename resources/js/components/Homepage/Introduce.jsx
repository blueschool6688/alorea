import { CheckCircleOutlined, ClockCircleOutlined, CrownOutlined, GlobalOutlined, HeartOutlined, StarOutlined } from '@ant-design/icons';
import { useEffect, useState } from 'react';

const Introduce = () => {
    const [isVisible, setIsVisible] = useState(false);
    const [animatedNumbers, setAnimatedNumbers] = useState({
        years: 0,
        products: 0,
        customers: 0,
        countries: 0
    });

    // Animation for numbers
    useEffect(() => {
        const targets = {
            years: 10,
            products: 100,
            customers: 50000,
            countries: 25
        };

        const animateNumbers = () => {
            const duration = 2000; // 2 seconds
            const steps = 60; // 60 FPS
            const stepTime = duration / steps;
            let currentStep = 0;

            const timer = setInterval(() => {
                currentStep++;
                const progress = currentStep / steps;

                setAnimatedNumbers({
                    years: Math.floor(targets.years * progress),
                    products: Math.floor(targets.products * progress),
                    customers: Math.floor(targets.customers * progress),
                    countries: Math.floor(targets.countries * progress)
                });

                if (currentStep >= steps) {
                    clearInterval(timer);
                    setAnimatedNumbers(targets);
                }
            }, stepTime);
        };

        if (isVisible) {
            animateNumbers();
        }
    }, [isVisible]);

    // Intersection Observer for animations
    useEffect(() => {
        const observer = new IntersectionObserver(
            ([entry]) => {
                if (entry.isIntersecting) {
                    setIsVisible(true);
                }
            },
            { threshold: 0.3 }
        );

        const element = document.getElementById('introduce-section');
        if (element) {
            observer.observe(element);
        }

        return () => {
            if (element) {
                observer.unobserve(element);
            }
        };
    }, []);

    // Brand commitments
    const commitments = [
        {
            icon: <ClockCircleOutlined className="text-3xl" />,
            title: "Bám mùi 8-12h",
            description: "Công nghệ lưu hương tiên tiến, đảm bảo mùi hương tồn tại suốt cả ngày dài",
            color: "text-blue-600",
            bgColor: "bg-blue-100"
        },
        {
            icon: <CrownOutlined className="text-3xl" />,
            title: "Thiết kế sang trọng",
            description: "Chai nước hoa được thiết kế tinh tế, thể hiện đẳng cấp và phong cách riêng biệt",
            color: "text-purple-600",
            bgColor: "bg-purple-100"
        },
        {
            icon: <GlobalOutlined className="text-3xl" />,
            title: "Nguyên liệu quốc tế",
            description: "Sử dụng 100% nguyên liệu nhập khẩu từ Pháp, Ý và Bulgary theo tiêu chuẩn châu Âu",
            color: "text-green-600",
            bgColor: "bg-green-100"
        }
    ];

    return (
        <section id="introduce-section" className="py-20 bg-gradient-to-b from-white to-gray-50 relative overflow-hidden">
            <div className="absolute top-0 left-0 w-72 h-72 bg-gradient-to-br from-burgundy-primary/10 to-rose-500/10 rounded-full blur-3xl -translate-x-1/2 -translate-y-1/2"></div>
            <div className="absolute bottom-0 right-0 w-96 h-96 bg-gradient-to-tl from-purple-500/10 to-pink-500/10 rounded-full blur-3xl translate-x-1/2 translate-y-1/2"></div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div className="text-center mb-16">
                    <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-light text-gray-900 mb-4 tracking-wide">
                        CÂU CHUYỆN ALORÉA
                    </h2>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto"></div>
                </div>

                <div className="max-w-4xl mx-auto mb-20">
                    <div className={`transition-all duration-1000 ${isVisible ? 'opacity-100 transform translate-y-0' : 'opacity-0 transform translate-y-10'}`}>
                        <div className="mb-12">
                            <div className="relative">
                                <div className="absolute -top-4 -left-4 text-6xl text-black/20 font-serif">"</div>

                                <div className="bg-white/80 backdrop-blur-sm rounded-3xl p-8 shadow-lg border border-gray-100">
                                    <p className="font-sans text-lg md:text-xl leading-relaxed text-gray-700 mb-6 relative z-10">
                                        <span className="font-semibold text-burgundy-primary">ALORÉA</span> ra đời từ sự kết hợp của
                                         Allure
                                        <span className='font-bold'> (sức quyến rũ)</span> và
                                         Aurora
                                        <span className='font-bold'>(bình minh)</span>, tượng trưng cho vẻ đẹp sang trọng, quyền lực nhưng vẫn đầy thơ mộng.
                                    </p>

                                    <p className="font-sans text-lg md:text-xl leading-relaxed text-gray-700 italic">
                                        Mỗi sản phẩm của ALORÉA là một hành trình hương thơm, khơi gợi cảm xúc,
                                        tôn vinh phong cách và dấu ấn cá nhân.
                                    </p>
                                </div>

                                {/* Decorative Elements */}
                                <div className="absolute -bottom-6 -right-6 w-12 h-12 bg-gradient-to-br from-black-primary to-white-500 rounded-full opacity-20"></div>
                                <div className="absolute -top-2 -right-8 w-6 h-6 bg-gradient-to-br from-white-500 to-black-primary rounded-full opacity-30"></div>
                            </div>
                        </div>
                    </div>
                </div>

                {/* Brand Commitments */}
                <div className={`mb-20 transition-all duration-1000 delay-600 ${isVisible ? 'opacity-100 transform translate-y-0' : 'opacity-0 transform translate-y-10'}`}>
                    <div className="text-center mb-12">
                        <h3 className="text-3xl md:text-4xl font-bold text-gray-900 mb-4 font-serif">Cam kết chất lượng</h3>
                        <p className="text-gray-600 max-w-2xl mx-auto font-sans">
                            ALORÉA luôn đặt chất lượng lên hàng đầu với những cam kết vượt trội về sản phẩm và dịch vụ
                        </p>
                    </div>

                    <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                        {commitments.map((commitment, index) => (
                            <div
                                key={index}
                                className={`group bg-white rounded-3xl p-8 shadow-lg hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 border border-gray-100 ${isVisible ? 'animate-fade-in' : ''
                                    }`}
                                style={{ animationDelay: `${(index + 1) * 200}ms` }}
                            >
                                <div className={`text-white bg-black w-16 h-16 rounded-2xl flex items-center justify-center mb-6 group-hover:scale-110 transition-transform duration-300`}>
                                    {commitment.icon}
                                </div>

                                <h4 className="text-xl font-bold text-gray-900 mb-4 group-hover:text-burgundy-primary transition-colors duration-300">
                                    {commitment.title}
                                </h4>

                                <p className="text-gray-600 leading-relaxed">
                                    {commitment.description}
                                </p>

                                {/* Hover Effect */}
                                <div className="mt-6 opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                    <div className={`w-full h-1 bg-gradient-to-r ${commitment.color.replace('text-', 'from-')} to-transparent rounded-full`}></div>
                                </div>
                            </div>
                        ))}
                    </div>
                </div>
            </div>

            {/* Custom Animations */}
            <style jsx>{`
                @keyframes fadeIn {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                .animate-fade-in {
                    animation: fadeIn 0.8s ease-out forwards;
                    opacity: 0;
                }
            `}</style>
        </section>
    );
};

export default Introduce;
