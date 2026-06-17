import BlogPage from '@/components/Homepage/BlogPage';
import FlashSale from '@/components/Homepage/FlashSale';
import Introduce from '@/components/Homepage/Introduce';
import ProductCategory from '@/components/Homepage/ProductCategory';
import ProductsLists from '@/components/Homepage/ProductsLists';
import VideoReview from '@/components/Homepage/VideoReview';
import Banner from '@/layouts/components/Banner';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import { Skeleton } from 'antd';
import { useEffect, useRef, useState } from 'react';

const Homepage = () => {
    const [visibleSections, setVisibleSections] = useState(new Set());
    const [homepageData, setHomepageData] = useState(null);
    const [loading, setLoading] = useState(true);
    const [error, setError] = useState(null);
    const sectionRefs = useRef([]);

    // Chỉ setup IntersectionObserver khi data đã load xong
    useEffect(() => {
        // Chỉ chạy khi không loading và có data
        if (loading || !homepageData) return;

        const observer = new IntersectionObserver(
            (entries) => {
                entries.forEach((entry) => {
                    if (entry.isIntersecting) {
                        const sectionId = entry.target.getAttribute('data-section');
                        setVisibleSections(prev => new Set([...prev, sectionId]));
                    }
                });
            },
            {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            }
        );

        sectionRefs.current.forEach((ref) => {
            if (ref) observer.observe(ref);
        });

        return () => observer.disconnect();
    }, [loading, homepageData]); // Thêm dependencies

    useEffect(() => {
        const fetchHomepageData = async () => {
            try {
                setLoading(true);
                const response = await api.get(GeneralPath.GENERAL_DATA_HOMEPAGE_ENDPOINT);

                if (response && response.data) {
                    setHomepageData(response.data);
                } else {
                    throw new Error('Invalid response format');
                }
            } catch (err) {
                setError(err.response?.data?.message || err.message || 'Failed to load homepage data');
            } finally {
                setLoading(false);
            }
        };

        fetchHomepageData();
    }, []);

    const addToRefs = (el) => {
        if (el && !sectionRefs.current.includes(el)) {
            sectionRefs.current.push(el);
        }
    };

    const getAnimationClass = (sectionId, delay = 0) => {
        const baseClass = "transition-all duration-1000 ease-out";
        const visibleClass = "opacity-100 translate-y-0";
        const hiddenClass = "opacity-0 translate-y-12";

        return `${baseClass} ${visibleSections.has(sectionId) ? visibleClass : hiddenClass}`;
    };

    // Loading Skeleton Components
    const BannerSkeleton = () => (
        <div className="w-full h-[500px] mb-8 relative">
            <Skeleton.Image
                active
                style={{ width: '100vw', height: '500px' }}
            />
        </div>
    );

    const ProductsListSkeleton = () => (
        <div className="container mx-auto px-4 py-12">
            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                {[...Array(8)].map((_, index) => (
                    <div key={index} className="bg-white rounded-lg shadow-md p-4">
                        <Skeleton
                            active
                            title={{ width: '80%' }}
                            paragraph={{ rows: 2, width: ['100%', '60%'] }}
                        />
                        <div className="mt-4">
                            <Skeleton.Button active size="large" style={{ width: '100%' }} />
                        </div>
                    </div>
                ))}
            </div>
        </div>
    );

    const FlashSaleSkeleton = () => (
        <div className="bg-red-50 py-12">
            <div className="container mx-auto px-4">
                <div className="text-center mb-8">
                    <Skeleton
                        active
                        title={{ width: '30%' }}
                        paragraph={{ rows: 1, width: '50%' }}
                    />
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-5 gap-4">
                    {[...Array(5)].map((_, index) => (
                        <div key={index} className="bg-white rounded-lg p-4 shadow">
                            <div className="mt-4">
                                <Skeleton
                                    active
                                    title={{ width: '90%' }}
                                    paragraph={{ rows: 1, width: '70%' }}
                                />
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );

    const VideoReviewSkeleton = () => (
        <div className="py-12">
            <div className="container mx-auto px-4">
                <div className="text-center mb-8">
                    <Skeleton
                        active
                        title={{ width: '40%' }}
                        paragraph={{ rows: 1, width: '60%' }}
                    />
                </div>
                {/* <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                    {[...Array(4)].map((_, index) => (
                        <div key={index} className="aspect-video">
                            <Skeleton.Image
                                active
                                style={{ width: '100%', height: '100%' }}
                            />
                        </div>
                    ))}
                </div> */}
            </div>
        </div>
    );

    const ProductCategorySkeleton = () => (
        <div className="bg-gray-50 py-12">
            <div className="container mx-auto px-4">
                <div className="text-center mb-8">
                    <Skeleton
                        active
                        title={{ width: '50%' }}
                        paragraph={{ rows: 1, width: '70%' }}
                    />
                </div>
                <div className="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {[...Array(3)].map((_, index) => (
                        <div key={index} className="text-center">
                            <Skeleton
                                active
                                title={{ width: '60%' }}
                                paragraph={false}
                            />
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );

    const BlogSkeleton = () => (
        <div className="py-12">
            <div className="container mx-auto px-4">
                <div className="text-center mb-8">
                    <Skeleton
                        active
                        title={{ width: '35%' }}
                        paragraph={{ rows: 1, width: '55%' }}
                    />
                </div>
                <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    {[...Array(6)].map((_, index) => (
                        <div key={index} className="bg-white rounded-lg shadow-md overflow-hidden">
                            <div className="p-6">
                                <Skeleton
                                    active
                                    title={{ width: '90%' }}
                                    paragraph={{ rows: 3, width: ['100%', '80%', '60%'] }}
                                />
                                <div className="mt-4 flex justify-between items-center">
                                    <Skeleton.Button active size="small" />
                                    <Skeleton.Button active size="small" />
                                </div>
                            </div>
                        </div>
                    ))}
                </div>
            </div>
        </div>
    );

    const IntroduceSkeleton = () => (
        <div className="bg-gray-100 py-12">
            <div className="container mx-auto px-4">
                <div className="text-center mb-8">
                    <Skeleton
                        active
                        title={{ width: '45%' }}
                        paragraph={{ rows: 2, width: ['80%', '60%'] }}
                    />
                </div>
            </div>
        </div>
    );

    if (error) {
        return (
            <div className="min-h-screen flex items-center justify-center bg-gradient-to-br from-red-50 to-pink-100">
                <div className="text-center p-8 bg-white rounded-2xl shadow-xl max-w-md">
                    <div className="w-20 h-20 mx-auto mb-6 bg-red-100 rounded-full flex items-center justify-center">
                        <svg className="w-10 h-10 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.964-.833-2.732 0L3.732 16.5c-.77.833.192 2.5 1.732 2.5z" />
                        </svg>
                    </div>
                    <h2 className="text-2xl font-bold text-red-600 mb-4">Có lỗi xảy ra</h2>
                    <p className="text-gray-600 mb-6">{error}</p>
                    <button
                        onClick={() => window.location.reload()}
                        className="px-6 py-3 bg-red-500 text-white rounded-lg hover:bg-red-600 transition-colors duration-200 font-medium"
                    >
                        Thử lại
                    </button>
                </div>
            </div>
        );
    }
    if (loading || !homepageData) {
        return (
            <div className="min-h-screen">
                <BannerSkeleton />
                <ProductsListSkeleton />
                <FlashSaleSkeleton />
                <VideoReviewSkeleton />
                <ProductCategorySkeleton />
                <BlogSkeleton />
                <IntroduceSkeleton />
            </div>
        );
    }
    return (
        <>
            <Banner bannerData={homepageData?.settings} />
            <div
                ref={addToRefs}
                data-section="products"
                className={getAnimationClass('products')}
                style={{ transitionDelay: '0ms' }}
            >
                <ProductsLists categories={homepageData?.productCategory} />
            </div>
            {homepageData && homepageData?.flashSales?.length > 0 && (
                <div
                    ref={addToRefs}
                    data-section="flashsale"
                    className={getAnimationClass('flashsale')}
                    style={{ transitionDelay: '100ms' }}
                >
                    <FlashSale flashSales={homepageData?.flashSales} />
                </div>
            )}
            <div
                ref={addToRefs}
                data-section="video"
                className={getAnimationClass('video')}
                style={{ transitionDelay: '200ms' }}
            >
                <VideoReview />
            </div>
            <div
                ref={addToRefs}
                data-section="category"
                className={getAnimationClass('category')}
                style={{ transitionDelay: '300ms' }}
            >
                <ProductCategory collections={homepageData?.collections} />
            </div>
            <div
                ref={addToRefs}
                data-section="blog"
                className={getAnimationClass('blog')}
                style={{ transitionDelay: '400ms' }}
            >
                <BlogPage blogs={homepageData?.blogs} />
            </div>
            <div
                ref={addToRefs}
                data-section="introduce"
                className={getAnimationClass('introduce')}
                style={{ transitionDelay: '500ms' }}
            >
                <Introduce
                    introduceVideoManufacture={homepageData?.settings?.introduce_video_manufacture}
                    introduceVideoDesign={homepageData?.settings?.introduce_video_design}
                />
            </div>

            <style jsx>{`
                @keyframes slideUp {
                    from {
                        opacity: 0;
                        transform: translateY(60px) scale(0.95);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                @keyframes fadeInScale {
                    from {
                        opacity: 0;
                        transform: translateY(40px) scale(0.9);
                    }
                    to {
                        opacity: 1;
                        transform: translateY(0) scale(1);
                    }
                }

                /* Enhanced animations cho từng section */
                [data-section="products"] {
                    animation: ${visibleSections.has('products') ? 'slideUp 1s ease-out forwards' : 'none'};
                }

                [data-section="flashsale"] {
                    animation: ${visibleSections.has('flashsale') ? 'fadeInScale 1.2s ease-out forwards' : 'none'};
                }

                [data-section="video"] {
                    animation: ${visibleSections.has('video') ? 'slideUp 1s ease-out forwards' : 'none'};
                }

                [data-section="category"] {
                    animation: ${visibleSections.has('category') ? 'fadeInScale 1.1s ease-out forwards' : 'none'};
                }

                [data-section="blog"] {
                    animation: ${visibleSections.has('blog') ? 'slideUp 1s ease-out forwards' : 'none'};
                }

                [data-section="introduce"] {
                    animation: ${visibleSections.has('introduce') ? 'fadeInScale 1.3s ease-out forwards' : 'none'};
                }

                /* Smooth scroll behavior */
                html {
                    scroll-behavior: smooth;
                }

                /* Preload animation state */
                [data-section] {
                    will-change: transform, opacity;
                }
            `}</style>
        </>
    );
}

export default Homepage;
