import { useSettings } from '@/lib/context/SettingContext';
import { useRef } from 'react';
import Slider from 'react-slick';


const Banner = () => {
    const { settings } = useSettings();
    const sliderRef = useRef(null);

    const sliderSettings = {
        dots: true,
        infinite: true,
        speed: 800,
        slidesToShow: 1,
        slidesToScroll: 1,
        autoplay: true,
        autoplaySpeed: 2000,
        pauseOnHover: true,
        lazyLoad: true,
        cssEase: 'cubic-bezier(0.4, 0, 0.2, 1)',
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    arrows: false,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    arrows: false,
                    dots: true,
                }
            }
        ]
    };

    return (
        <section className="w-full h-auto overflow-hidden mt-16">
            {settings?.banner_thumb_urls && settings?.banner_thumb_urls.length > 0 ? (
                <Slider ref={sliderRef} {...sliderSettings} className="">
                    {settings?.banner_thumb_urls.map((slide, index) => (
                        <div key={slide.id} className="">
                            <img
                                src={slide}
                                className="w-full h-auto object-cover"
                            />
                        </div>
                    ))}
                </Slider>
            ) : (
                <div className="">
                    <img
                        src={settings?.banner_image_url}
                        alt="Banner"
                        className="w-full h-auto object-contains"
                        onError={(e) => {
                            console.error('Image load error:', e);
                            e.target.style.display = 'none';
                        }}
                    />
                </div>
            )}
            {/* <div className="absolute inset-0 z-20 h-auto flex items-center justify-center">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <div className="mb-4 sm:mb-8 animate-fade-in-up">
                        <h1
                            className="font-serif text-2xl xs:text-3xl sm:text-4xl md:text-5xl lg:text-6xl xl:text-7xl font-bold leading-tight px-4 sm:px-0"
                            style={{
                                color: settings?.color_title_banner || '#ffffff',
                                textShadow: '2px 2px 4px rgba(0,0,0,0.7)'
                            }}
                        >
                            <span className="block mb-1 sm:mb-2">
                                {settings?.title_banner || "ALORÉA – Love in Every Drop"}
                            </span>
                        </h1>
                    </div>

                    <div className="font-sans mb-12 animate-fade-in-up animation-delay-300">
                        <p
                            className="text-lg md:text-xl lg:text-2xl max-w-4xl mx-auto leading-relaxed"
                            style={{
                                color: settings?.color_subtitle_banner || settings?.color_sub_title_banner || '#f8f9fa',
                                textShadow: '1px 1px 2px rgba(0,0,0,0.5)'
                            }}
                        >
                            <span className="block">
                                {settings?.sub_title_banner || "Mỗi giọt hương là một câu chuyện tình yêu, một dấu ấn khó quên."}
                            </span>
                        </p>
                    </div>
                    <div className="flex flex-col sm:flex-row gap-3 sm:gap-4 justify-center items-center mb-12 sm:mb-16 animate-fade-in-up animation-delay-600 px-4 sm:px-0">
                        <button
                            onClick={() => navigate('/products')}
                            className="cursor-pointer group w-full sm:w-auto px-6 sm:px-8 py-3 sm:py-4 border-2 font-semibold text-sm sm:text-base rounded-lg transition-all duration-300 hover:scale-105 active:scale-95"
                            style={{
                                borderColor: settings?.color_title_banner || '#ffffff',
                                color: settings?.color_title_banner || '#ffffff',
                                backgroundColor: 'transparent'
                            }}
                            onMouseEnter={(e) => {
                                e.target.style.opacity = '0.5';
                            }}
                            onMouseLeave={(e) => {
                                e.target.style.opacity = '1';
                            }}
                        >
                            Khám phá ngay
                        </button>
                    </div>
                </div>
            </div> */}

            {/* CSS Animations */}
            <style jsx>{`
                @keyframes gradient {
                    0% { background-position: 0% 50%; }
                    50% { background-position: 100% 50%; }
                    100% { background-position: 0% 50%; }
                }
                @keyframes blob {
                    0% { transform: translate(0px, 0px) scale(1); }
                    33% { transform: translate(30px, -50px) scale(1.1); }
                    66% { transform: translate(-20px, 20px) scale(0.9); }
                    100% { transform: translate(0px, 0px) scale(1); }
                }
                @keyframes float {
                    0%, 100% { transform: translateY(0px); }
                    50% { transform: translateY(-20px); }
                }
                .animate-fade-in-up {
                    animation: fadeInUp 1s ease-out forwards;
                }
                .animation-delay-300 { animation-delay: 0.3s; }
                .animation-delay-600 { animation-delay: 0.6s; }

                @keyframes fadeInUp {
                    from {
                        opacity: 0;
                        transform: translateY(30px);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0);
                    }
                }

                /* Custom Slick Slider Styles */
                /* Custom dots positioning */
                :global(.banner-slider .slick-dots) {
                    position: absolute;
                    bottom: 30px;
                    left: 50%;
                    transform: translateX(-50%);
                    z-index: 30;
                    display: flex !important;
                    gap: 8px;
                    list-style: none;
                    margin: 0;
                    padding: 0;
                }

                :global(.banner-slider .slick-dots li) {
                    width: 12px;
                    height: 12px;
                    margin: 0;
                }

                :global(.banner-slider .slick-dots li button) {
                    width: 12px;
                    height: 12px;
                    padding: 0;
                    border: none;
                    border-radius: 50%;
                    background: rgba(255, 255, 255, 0.5);
                    font-size: 0;
                    line-height: 0;
                    text-indent: -9999px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                }

                :global(.banner-slider .slick-dots li button:hover) {
                    background: rgba(255, 255, 255, 0.75);
                }

                :global(.banner-slider .slick-dots li.slick-active button) {
                    background: white;
                    transform: scale(1.25);
                }

                /* Hide default arrows */
                :global(.banner-slider .slick-prev),
                :global(.banner-slider .slick-next) {
                    display: none !important;
                }
            `}</style>
        </section>
    );
};

export default Banner;
