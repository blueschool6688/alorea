import { ArrowRightOutlined, GiftOutlined, ManOutlined, WomanOutlined } from '@ant-design/icons';
import { useState } from 'react';
import { useNavigate } from 'react-router-dom';

const ProductCategory = ({ collections }) => {
    const [hoveredCategory, setHoveredCategory] = useState(null);
    const navigate = useNavigate();

    return (
        <section className="py-20 bg-gradient-to-b from-white to-gray-50">
            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header */}
                <div className="text-center mb-16">
                    <h2 className="font-serif text-4xl md:text-5xl lg:text-6xl font-light text-gray-900 mb-4 tracking-wide">
                        BỘ SƯU TẬP THEO DÒNG
                    </h2>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto mb-6"></div>
                    <p className="text-lg md:text-xl text-gray-600 max-w-3xl mx-auto">
                        Khám phá những dòng sản phẩm được chế tác tinh tế, phù hợp với từng phong cách và cá tính riêng biệt
                    </p>
                </div>

                {/* Categories Grid */}
                <div className="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-12">
                    {collections.map((category, index) => (
                        <div
                            key={category.id}
                            className="group cursor-pointer relative overflow-hidden rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-4"
                            style={{ minHeight: '600px' }}
                            onMouseEnter={() => setHoveredCategory(category.id)}
                            onMouseLeave={() => setHoveredCategory(null)}
                        >
                            {/* Background Image */}
                            <div className="absolute inset-0">
                                <video
                                    src={category.video_stream_url}
                                    className="w-full h-full object-cover transition-transform duration-700 group-hover:scale-110"
                                    autoPlay
                                    preload='metadata'
                                    muted
                                    loop
                                    playsInline
                                />

                            </div>
                            {/* Content */}
                            <div className="relative z-10 h-full flex flex-col justify-between p-8">
                                {/* Top Content */}
                                <div>
                                    <h3 className={`text-3xl text-white md:text-4xl font-bold text-black mb-3 transition-colors duration-300`}>
                                        {category.title}
                                    </h3>
                                </div>

                                {/* CTA Button */}
                                <button onClick={() => navigate(`/collections`)} className={`font-sans w-full bg-black cursor-pointer text-white font-semibold py-4 px-6 rounded-xl transition-all duration-300 transform hover:scale-105 active:scale-95 shadow-lg hover:shadow-xl flex items-center justify-center gap-3 group/btn`}>
                                    <span>Khám phá bộ sưu tập</span>
                                    <ArrowRightOutlined className="transition-transform duration-300 group-hover/btn:translate-x-1" />
                                </button>
                            </div>

                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default ProductCategory;
