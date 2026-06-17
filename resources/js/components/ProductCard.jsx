import HtmlContent from '@/components/HtmlContent';
import { useCart } from '@/hooks/useCart';
import { Badge } from 'antd';
import { Link, useNavigate } from 'react-router-dom';


const ProductCard = ({ product, viewMode = 'grid', index = 0 }) => {
    const navigate = useNavigate();
    const { addToCart } = useCart();
    const formatPrice = (price) => {
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    const getDiscountLabel = () => {
        if (!product.has_flash_sale) return null;

        if (product.flash_sale_discount_type === 'fixed') {
            const discountAmount = product.price - product.flash_sale_price;
            return `-${new Intl.NumberFormat('vi-VN').format(discountAmount)}đ`;
        } else {
            const discountPercentage = Math.round(((product.price - product.flash_sale_price) / product.price) * 100);
            return `-${discountPercentage}%`;
        }
    };

    const handleAddToCart = async (product) => {
        try {
            const cartItem = {
                id: product.id,
                name: product.name,
                stock: product.stock || 0,
                price: product.price,
                has_flash_sale: product.has_flash_sale,
                flash_sale_price: product.flash_sale_price,
                flash_sale_discount: product?.flash_sale_discount,
                image: product.main_image_url,
                quantity: 1,
                category: product?.category?.name,
            };
            await addToCart(cartItem, 1);
        } catch (error) {
            showMessage(error?.message || 'Không thể thêm sản phẩm vào giỏ hàng', 'error');
        }
    };



    const cardContent = (
        <div
            key={product.id}
            onClick={() => navigate(`/products/${product.slug}`)}
            className={`cursor-pointer group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-2 overflow-hidden ${viewMode === 'list' ? 'flex' : 'block'
                }`}
            style={{ animationDelay: `${index * 100}ms` }}
        >

            {/* Product Image */}
            <div className={`relative overflow-hidden ${viewMode === 'list' ? 'w-48 flex-shrink-0' : 'aspect-square'
                }`}>
                <img
                    src={product.main_image_url}
                    alt={product.name}
                    className="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700"
                />
            </div>

            {/* Product Info */}
            <div className={`p-6 ${viewMode === 'list' ? 'flex-1' : ''}`}>
                <div className="mb-3">
                    <Link to={`/products/${product.slug}`} className="font-serif text-lg font-bold !text-gray-900 mb-2 group-hover:text-burgundy-primary transition-colors duration-300">
                        {product.name}
                    </Link>
                    {/* <p className="font-sans text-gray-600 text-sm line-clamp-2"> */}
                    <HtmlContent
                        content={product.short_description}
                        className="font-sans text-gray-600 text-sm"
                        lineClamp={2}
                        maxLength={150}
                    />
                    {/* </p> */}
                </div>

                <div className="flex items-center gap-2 mb-4">
                    {product.has_flash_sale ? (
                        <>
                            <span className="text-xl font-bold text-burgundy-primary">
                                {formatPrice(product.flash_sale_price)}
                            </span>
                            <span className="text-gray-500 line-through text-sm">
                                {formatPrice(product.price)}
                            </span>
                        </>
                    ) : (
                        <>
                            <span className="text-xl font-bold text-burgundy-primary">
                                {formatPrice(product.price)}
                            </span>
                        </>
                    )}
                </div>


                {/* Actions */}
                <div className={`flex gap-2 ${viewMode === 'list' ? 'flex-col' : ''}`}>
                    <button
                        onClick={(e) => {
                            e.stopPropagation();
                            handleAddToCart && handleAddToCart(product)
                        }}
                        className="cursor-pointer flex-1 bg-burgundy-primary text-white py-2 px-4 rounded-lg hover:bg-burgundy-dark transition-all duration-300 transform hover:scale-105 flex items-center justify-center gap-2"
                    >
                        Thêm vào giỏ
                    </button>
                    <button onClick={async () => {
                        handleAddToCart && await handleAddToCart(product)
                        navigate('/checkout');
                    }}
                        className="cursor-pointer px-2 py-2 border border-burgundy-primary text-burgundy-primary rounded-lg hover:bg-burgundy-primary hover:text-white transition-all duration-300"
                    >
                        Mua nhanh
                    </button>
                </div>
            </div>
        </div>
    );

    if (product.has_flash_sale) {
        return (
            <Badge.Ribbon
                text={getDiscountLabel()}
                color={'red'}
            >
                {cardContent}
            </Badge.Ribbon>
        );
    }

    return cardContent;
};

export default ProductCard;
