import ProductCard from '@/components/ProductCard';
import { useCart } from '@/hooks/useCart';
import { useEffect, useState } from 'react';
import Slider from "react-slick";

const ProductsLists = ({ categories }) => {
    const [activeTab, setActiveTab] = useState(null);
    const { addToCart } = useCart();

    const getAllProducts = () => {
        if (!categories) return [];

        const allProducts = [];
        categories.forEach(category => {
            if (category.products && category.products.length > 0) {
                category.products.forEach(product => {
                    allProducts.push({
                        ...product,
                        categoryName: category.name,
                        categorySlug: category.slug
                    });
                });
            }
        });
        return allProducts;
    };

    const getAvailableCategories = () => {
        if (!categories) return [];
        return categories.filter(category =>
            category.products && category.products.length > 0
        );
    };

    const availableCategories = getAvailableCategories();
    const allProducts = getAllProducts();

    useEffect(() => {
        if (availableCategories.length > 0 && !activeTab) {
            setActiveTab(availableCategories[0].slug);
        }
    }, [availableCategories, activeTab]);

    const filteredProducts = allProducts.filter(product => {
        if (!activeTab) return false;
        const category = availableCategories.find(cat => cat.slug === activeTab);
        return category && product.category_id === category.id;
    });

    // Settings cho Category Slider
    const categorySliderSettings = {
        dots: false,
        infinite: false,
        speed: 500,
        slidesToShow: 3,
        slidesToScroll: 1,
        arrows: true,
        swipeToSlide: true,
        focusOnSelect: false,
        responsive: [
            {
                breakpoint: 1024,
                settings: {
                    slidesToShow:2,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 768,
                settings: {
                    slidesToShow: 3,
                    slidesToScroll: 1,
                }
            },
            {
                breakpoint: 480,
                settings: {
                    slidesToShow: 2,
                    slidesToScroll: 1,
                }
            }
        ]
    };

    if (!categories || categories.length === 0) {
        return (
            <section className="py-16 bg-gradient-to-b from-gray-50 to-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="font-serif text-4xl text-gray-500 mb-4">
                        Chưa có sản phẩm nào
                    </h2>
                </div>
            </section>
        );
    }

    if (availableCategories.length === 0) {
        return (
            <section className="py-16 bg-gradient-to-b from-gray-50 to-white">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <h2 className="font-serif text-4xl text-gray-500 mb-4">
                        Các danh mục chưa có sản phẩm
                    </h2>
                </div>
            </section>
        );
    }

    return (
        <section className="py-16 bg-gradient-to-b from-gray-50 to-white">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="text-center mb-12">
                    <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-light text-gray-900 mb-4 tracking-wide">
                        BỘ SƯU TẬP NỔI BẬT
                    </h2>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto"></div>
                </div>
                {availableCategories.length > 5 ? (
                    <div className="mb-12 category-slider-wrapper">
                        <Slider {...categorySliderSettings}>
                            {availableCategories.map((category) => (
                                <div key={category.id} className="px-2">
                                    <button
                                        onClick={() => setActiveTab(category.slug)}
                                        className={`font-sans w-full px-4 py-3 text-sm sm:text-base transition-all duration-300 border-b-2 whitespace-nowrap ${
                                            activeTab === category.slug
                                                ? 'border-gray-900 text-black rounded-t-lg bg-white'
                                                : 'border-transparent text-black hover:text-gray-700 hover:border-black'
                                        }`}
                                    >
                                        {category.name}
                                    </button>
                                </div>
                            ))}
                        </Slider>
                    </div>
                ) : (
                    <div className="flex flex-wrap justify-center gap-2 sm:gap-4 md:gap-8 mb-12">
                        {availableCategories.map((category) => (
                            <button
                                key={category.id}
                                onClick={() => setActiveTab(category.slug)}
                                className={`font-sans px-4 py-2 sm:px-6 sm:py-3 text-sm sm:text-base transition-all duration-300 border-b-2 ${
                                    activeTab === category.slug
                                        ? 'border-gray-900 text-black rounded-t-lg'
                                        : 'border-transparent text-black hover:text-gray-700 hover:border-black'
                                }`}
                            >
                                {category.name}
                            </button>
                        ))}
                    </div>
                )}

                {/* Products Grid */}
                <div className="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 gap-6 lg:gap-8 mt-8">
                    {filteredProducts.map((product) => (
                        <ProductCard
                            key={product.id}
                            product={product}
                        />
                    ))}
                </div>

                {filteredProducts.length === 0 && activeTab && (
                    <div className="text-center py-12">
                        <p className="text-gray-500 text-lg">
                            Danh mục này chưa có sản phẩm nào.
                        </p>
                    </div>
                )}
            </div>

            <style jsx>{`
                .line-clamp-2 {
                    display: -webkit-box;
                    -webkit-line-clamp: 2;
                    -webkit-box-orient: vertical;
                    overflow: hidden;
                }

                .category-slider-wrapper {
                    margin: 0 -10px;
                }

                .category-slider-wrapper .slick-slide > div {
                    padding: 0 2px;
                }

                .category-slider-wrapper .slick-arrow {
                    width: 40px;
                    height: 40px;
                    background: rgba(0, 0, 0, 0.5);
                    border-radius: 50%;
                    z-index: 1;
                }

                .category-slider-wrapper .slick-arrow:hover {
                    background: rgba(0, 0, 0, 0.7);
                }

                .category-slider-wrapper .slick-arrow:before {
                    font-size: 20px;
                }

                .category-slider-wrapper .slick-prev {
                    left: -20px;
                }

                .category-slider-wrapper .slick-next {
                    right: -20px;
                }
            `}</style>
        </section>
    );
};

export default ProductsLists;
