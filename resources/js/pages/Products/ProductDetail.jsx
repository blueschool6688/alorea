import HtmlContent from '@/components/HtmlContent';
import useAuth from "@/hooks/useAuth";
import useSEO from '@/hooks/useSEO';
import { useCart } from '@/lib/context/CartContext';
import GeneralPath from '@/routes/GeneralPath';
import api from '@/utils/api';
import { generateProductSEO } from '@/utils/seoUtils';
import showMessage from '@/utils/showMessage';
import {
    LoadingOutlined,
    ShoppingCartOutlined,
    StarOutlined
} from '@ant-design/icons';
import {
    Avatar,
    Breadcrumb,
    Button,
    Card,
    Col,
    Divider,
    Form,
    Input,
    Rate,
    Row,
    Spin,
    Tag,
    Typography
} from 'antd';
import { useEffect, useState } from 'react';
import { Link, useNavigate, useParams } from 'react-router-dom';


const { Title, Text, Paragraph } = Typography;
const { TextArea } = Input;

const ProductDetail = () => {
    const { code } = useParams();
    const [product, setProduct] = useState(null);
    const [selectedImage, setSelectedImage] = useState(0);
    const [quantity, setQuantity] = useState(1);
    const [loadingData, setLoading] = useState(true);
    const { isAuthenticated, user, loading } = useAuth();
    const [error, setError] = useState(null);
    const [submittingReview, setSubmittingReview] = useState(false);
    const { addToCart } = useCart();
    const [reviewForm] = Form.useForm();
    const [seoData, setSeoData] = useState(null);
    const navigate = useNavigate();

    useSEO(seoData);

    useEffect(() => {
        const fetchProductDetail = async () => {
            try {
                setLoading(true);
                setError(null);
                const response = await api.get(GeneralPath.GENERAL_DATA_PRODUCT_DETAIL_ENDPOINT + `/${code}`);
                if (response && response.data) {
                    const apiData = response.data;
                    const formattedProduct = {
                        id: apiData.id,
                        name: apiData.name,
                        slug: apiData.slug,
                        image: apiData.main_image_url,
                        price: apiData.price,
                        rating: apiData.rating || 0,
                        totalReviews: apiData.review_count || 0,
                        stock: apiData.stock || 0,
                        has_flash_sale: apiData.has_flash_sale || false,
                        flash_sale_price: apiData.flash_sale_price || null,
                        flash_sale_discount: apiData.flash_sale_discount || null,
                        flash_sale_discount_type: apiData.flash_sale_discount_type || 0,
                        category: apiData.category?.name || 'Nước hoa',
                        volume: apiData.volume || '50ml',
                        concentration: apiData.concentration || 'EDP (15-20%)',
                        sku: apiData.sku,
                        technical_arr: apiData?.technical_arr || [],
                        images: apiData.gallery_urls,
                        description: apiData.description || 'Chưa có mô tả sản phẩm.',
                        short_description: apiData.short_description,
                        fragrance: {
                            top: apiData.fragrance_notes?.top || apiData.top_notes || ['Bergamot', 'Hoa cam', 'Quả lê'],
                            heart: apiData.fragrance_notes?.heart || apiData.heart_notes || ['Hoa hồng Bulgaria', 'Hoa nhài', 'Peony'],
                            base: apiData.fragrance_notes?.base || apiData.base_notes || ['Gỗ đàn hương', 'Xạ hương trắng', 'Vani Madagascar']
                        },
                        suggestions: apiData.suggestions || 'Phù hợp cho mọi dịp sử dụng.',
                        reviews: apiData.reviews && Array.isArray(apiData.reviews)
                            ? apiData.reviews.map(review => ({
                                id: review.id,
                                name: typeof review.name === 'string' ? review.name :
                                    (review.user?.name || 'Khách hàng'),
                                review_name: typeof review.name === 'string' ? review.name :
                                    (review.user?.name || 'Khách hàng'),
                                reviewer_name: typeof review.reviewer_name === 'string' ? review.reviewer_name :
                                    (typeof review.name === 'string' ? review.name :
                                        (review.user?.name || 'Khách hàng')),

                                time_at: review.time_at || '',
                                rating: review.rating || 5,
                                date: review.created_at ? new Date(review.created_at).toLocaleDateString('vi-VN') : review.date,
                                comment: review.comment || review.review || review.content || '',
                                verified: review.verified || false
                            }))
                            : []
                    };
                    setProduct(formattedProduct);
                    const productSEO = generateProductSEO(formattedProduct);
                    setSeoData(productSEO);

                } else {
                    throw new Error('Invalid API response format');
                }

            } catch (err) {
                console.error('Error fetching product detail:', err);
                setError(err.response?.data?.message || err.message || 'Failed to load product details');
                showMessage(
                    err.response?.data?.message || 'Không thể tải thông tin sản phẩm',
                    'error'
                );
            } finally {
                setLoading(false);
            }
        };

        fetchProductDetail();
    }, []);
    useEffect(() => {
        if (product && product.images && product.images.length > 0) {
            setSelectedImage(0);
        }
    }, [product]);

    const formatPrice = (price) => {
        if (!price) return '0₫';
        return new Intl.NumberFormat('vi-VN', {
            style: 'currency',
            currency: 'VND'
        }).format(price);
    };

    // Trong phần formatPrice, thêm hàm tính giá flash sale
    const getFlashSalePrice = (product) => {
        if (!product.has_flash_sale || !product.flash_sale_discount) return product.price;

        if (product.flash_sale_discount_type === 1) {
            // Giảm theo phần trăm
            return product.price - (product.price * product.flash_sale_discount / 100);
        } else {
            // Giảm theo số tiền cố định
            return product.price - product.flash_sale_discount;
        }
    };

    const handleAddToCart = async (product) => {
        try {
            const cartItem = {
                id: product.id,
                name: product.name,
                price: product.price,
                has_flash_sale: product.has_flash_sale,
                flash_sale_price: product.flash_sale_price,
                flash_sale_discount: product?.flash_sale_discount,
                image: product.image,
                quantity: quantity,
                category: product?.category
            };
            await addToCart(cartItem, quantity || 1);
        } catch (error) {
            showMessage(error?.message || 'Không thể thêm sản phẩm vào giỏ hàng', 'error');
        }
    };

    const handleSubmitReview = async (values) => {
        try {
            setSubmittingReview(true);

            const reviewData = {
                product_id: product.id,
                rating: values.rating,
                comment: values.comment,
            };

            const response = await api.post(`/products/${product.id}/reviews`, reviewData);

            if (response && response.data) {
                const newReview = {
                    id: response.data.id || Date.now(),
                    name: typeof response.data?.user?.name === 'string' ? response.data.user.name : 'Khách hàng',
                    reviewer_name: typeof response.data?.user?.name === 'string' ? response.data.user.name : 'Khách hàng',
                    rating: response.data.rating,
                    date: new Date().toLocaleDateString('vi-VN'),
                    time_at: response.data.time_at || new Date().toLocaleTimeString('vi-VN'),
                    comment: response.data.comment || '',
                    verified: true
                };

                setProduct(prev => ({
                    ...prev,
                    reviews: [newReview, ...prev.reviews],
                    totalReviews: prev.totalReviews + 1
                }));
                reviewForm.resetFields();
                showMessage('Đánh giá của bạn đã được gửi thành công!', 'success');
            }

        } catch (error) {
            console.error('Error submitting review:', error);
            showMessage(
                error.response?.data?.message || 'Không thể gửi đánh giá. Vui lòng thử lại!',
                'error'
            );
        } finally {
            setSubmittingReview(false);
        }
    };
    if (loadingData) {
        return (
            <div className="bg-white min-h-screen">
                <div className="flex items-center justify-center min-h-screen">
                    <Spin size="large" indicator={<LoadingOutlined style={{ fontSize: 48 }} spin />} />
                </div>
            </div>
        );
    }

    if (!product || error) {
        return (
            <div className="bg-white min-h-screen">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
                    <div className="text-center">
                        <Title level={2}>Không tìm thấy sản phẩm</Title>
                        <Paragraph>Sản phẩm bạn đang tìm không tồn tại hoặc đã bị xóa.</Paragraph>
                        <Button type="primary" size="large">
                            <Link to="/products">Quay về danh sách sản phẩm</Link>
                        </Button>
                    </div>
                </div>
            </div>
        );
    }

    return (
        <div className="bg-white min-h-screen">
            <div className="bg-gray-50 py-4">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <Breadcrumb
                        items={[
                            { title: <Link to="/">Trang chủ</Link> },
                            { title: <Link to="/products">Sản phẩm</Link> },
                            { title: product.name }
                        ]}
                    />
                </div>
            </div>

            <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                <Row gutter={[32, 32]}>
                    <Col xs={24} lg={12}>
                        <div className="sticky top-8 h-full">
                            {/* Main Image */}
                            <div className="mb-4 aspect-square w-full bg-gray-100 rounded-2xl overflow-hidden shadow-lg">
                                <img
                                    src={product.images && product.images[selectedImage] ? product.images[selectedImage] : product.image}
                                    alt={product.name}
                                    className="w-full h-full object-cover object-center hover:scale-105 transition-transform duration-300"
                                />
                            </div>
                            {product.images && product.images.length > 1 && (
                                <div className="grid grid-cols-4 gap-2">
                                    {product.images.map((image, index) => (
                                        <div
                                            key={index}
                                            className={`cursor-pointer rounded-lg overflow-hidden border-2 transition-all duration-300 aspect-square ${selectedImage === index
                                                ? 'border-burgundy-primary shadow-lg'
                                                : 'border-gray-200 hover:border-gray-300'
                                                }`}
                                            onClick={() => setSelectedImage(index)}
                                        >
                                            <img
                                                src={image}
                                                alt={`${product.name} ${index + 1}`}
                                                className="w-full h-full object-cover object-center"
                                            />
                                        </div>
                                    ))}
                                </div>
                            )}
                        </div>
                    </Col>

                    {/* Product Info */}
                    <Col xs={24} lg={12}>
                        <div className="space-y-6">
                            {/* Header */}
                            <div>
                                <div className="flex items-center gap-2 mb-2">
                                    <Tag color='default' className='text-burgundy-primary'>
                                        {product.category}
                                    </Tag>
                                    {product.has_flash_sale && (
                                        <Tag color="red" className="animate-pulse">
                                            🔥 FLASH SALE -{product.flash_sale_discount}{product.flash_sale_discount_type === 'percent' ? '%' : '₫'}
                                        </Tag>
                                    )}
                                </div>

                                <Title level={1} className="!mb-2 !text-black !font-serif">
                                    {product.name}
                                </Title>

                                <Text type="secondary" className="text-lg">
                                    <Text strong>{product.volume}</Text>
                                </Text>

                                {/* Rating */}
                                {product.rating > 0 && (
                                    <div className="flex items-center gap-2 mt-2">
                                        <Rate disabled defaultValue={product.rating} />
                                        <Text type="secondary">
                                            ({product.totalReviews} đánh giá)
                                        </Text>
                                    </div>
                                )}
                            </div>

                            {product.has_flash_sale ? (
                                <>
                                    <div className="mb-2">
                                        <Text className="text-3xl font-bold !text-red-600 me-2">
                                            {formatPrice(product.flash_sale_price || getFlashSalePrice(product))}
                                        </Text>
                                        <Text delete className="text-xl text-gray-500">
                                            {formatPrice(product.price)}
                                        </Text>
                                    </div>
                                    <Tag color="success"> Tiết kiệm {formatPrice(product.price - (product.flash_sale_price || getFlashSalePrice(product)))}</Tag>
                                </>
                            ) : (
                                <Text className="text-3xl font-bold text-burgundy-primary">
                                    {formatPrice(product.price)}
                                </Text>
                            )}

                            <Card title="Thông số kỹ thuật" className="border-gray-200 !mb-5 !mt-3">
                                {Object.entries(product?.technical_arr).length > 0 ? (
                                    <Row gutter={[16, 16]}>
                                        {Object.entries(product?.technical_arr).map(([key, spec]) => (
                                            <Col span={12} key={key}>
                                                <Text strong>{spec.label}:</Text>
                                                <br />
                                                <Text>{spec.value}</Text>
                                            </Col>
                                        ))}
                                    </Row>

                                ) : (
                                    <Row gutter={[16, 16]}>
                                        <Text strong>Đang cập nhật</Text>
                                    </Row>
                                )}
                            </Card>

                            {/* Quantity Selector */}
                            <div className="flex items-center gap-4">
                                <Text strong>Số lượng:</Text>
                                <div className="flex items-center border border-gray-300 rounded-lg">
                                    <Button
                                        type="text"
                                        onClick={() => setQuantity(Math.max(1, quantity - 1))}
                                        disabled={quantity <= 1}
                                        className="px-3"
                                    >
                                        -
                                    </Button>
                                    <span className="px-4 py-2 min-w-[50px] text-center">
                                        {quantity}
                                    </span>
                                    <Button
                                        type="text"
                                        onClick={() => setQuantity(Math.min(product.stock, quantity + 1))}
                                        disabled={quantity >= product.stock}
                                        className="px-3"
                                    >
                                        +
                                    </Button>
                                </div>
                            </div>

                            {/* Action Buttons */}
                            <div className="space-y-4">
                                <div className="grid grid-cols-2 gap-4">
                                    <Button
                                        type="primary"
                                        size="large"
                                        block
                                        icon={<ShoppingCartOutlined />}
                                        onClick={async () => {
                                            await handleAddToCart(product)
                                            navigate('/checkout')
                                        }}
                                        disabled={product.stock <= 0}
                                        className="h-14 text-lg font-bold !bg-burgundy-primary hover:bg-burgundy-dark border-burgundy-primary hover:border-burgundy-dark"
                                    >
                                        MUA NGAY
                                    </Button>
                                    <Button
                                        size="large"
                                        block
                                        onClick={() => handleAddToCart(product)}
                                        disabled={product.stock <= 0}
                                        className="h-12 border-black text-black hover:bg-black hover:text-white"
                                    >
                                        Thêm vào giỏ
                                    </Button>
                                </div>
                            </div>
                        </div>
                    </Col>
                </Row>

                {/* Product Details Sections */}
                <div className="mt-16">
                    <Row gutter={[32, 32]}>
                        <Col lg={24}>
                            <Card title="Mô tả sản phẩm" className="mb-8 border-2">
                                <Paragraph className="text-lg leading-relaxed">
                                    <HtmlContent content={product.description} />
                                </Paragraph>
                                {product.short_description && (
                                    <Paragraph className="text-base text-gray-600 leading-relaxed">
                                        {product.short_description}
                                    </Paragraph>
                                )}
                            </Card>

                            {/* Fragrance Pyramid */}
                            {/* <Card title="Tháp hương 3 tầng" className="!mb-8 !mt-4">
                                <div className="space-y-4">
                                    <div className="bg-white border-2 border-gray-200 p-6 rounded-xl hover:shadow-md transition-shadow duration-300">
                                        <div className="flex items-center justify-between mb-4">
                                            <Title level={4} className="!mb-0 flex items-center gap-3">
                                                <div className="w-3 h-3 bg-black rounded-full"></div>
                                                <span className="text-gray-800">Hương đầu</span>
                                            </Title>
                                        </div>

                                        <div className="flex flex-wrap gap-2 mb-3">
                                            {product.fragrance.top.map((note, index) => (
                                                <Tag
                                                    key={index}
                                                    className="text-sm px-3 py-1 bg-gray-100 text-gray-700 border-gray-300 rounded-md"
                                                >
                                                    {note}
                                                </Tag>
                                            ))}
                                        </div>

                                        <Text className="text-gray-600 text-sm leading-relaxed">
                                            Những note hương đầu tiên bạn cảm nhận được, thường tồn tại 15-30 phút
                                        </Text>
                                    </div>
                                    <div className="bg-white border-2 border-gray-200 p-6 rounded-xl hover:shadow-md transition-shadow duration-300">
                                        <div className="flex items-center justify-between mb-4">
                                            <Title level={4} className="!mb-0 flex items-center gap-3">
                                                <div className="w-3 h-3 bg-black rounded-full"></div>
                                                <span className="text-gray-800">Hương giữa</span>
                                            </Title>
                                        </div>

                                        <div className="flex flex-wrap gap-2 mb-3">
                                            {product.fragrance.heart.map((note, index) => (
                                                <Tag
                                                    key={index}
                                                    className="text-sm px-3 py-1 bg-gray-200 text-gray-800 border-gray-400 rounded-md"
                                                >
                                                    {note}
                                                </Tag>
                                            ))}
                                        </div>

                                        <Text className="text-gray-600 text-sm leading-relaxed">
                                            Hương chính của nước hoa, thể hiện cá tính và phong cách, tồn tại 2-4 giờ
                                        </Text>
                                    </div>
                                    <div className="bg-white border-2 border-gray-200 p-6 rounded-xl hover:shadow-lg transition-shadow duration-300">
                                        <div className="flex items-center justify-between mb-4">
                                            <Title level={4} className="!mb-0 flex items-center gap-3">
                                                <div className="w-3 h-3 bg-black rounded-full"></div>
                                                <span className="text-black">Hương cuối</span>
                                            </Title>
                                        </div>

                                        <div className="flex flex-wrap gap-2 mb-3">
                                            {product.fragrance.base.map((note, index) => (
                                                <Tag
                                                    key={index}
                                                    className="text-sm px-3 py-1 bg-gray-300 text-gray-900 border-gray-400 rounded-md"
                                                >
                                                    {note}
                                                </Tag>
                                            ))}
                                        </div>

                                        <Text className="text-gray-600 text-sm leading-relaxed">
                                            Hương lưu lại lâu nhất, tạo dư âm và ấn tượng sâu sắc, tồn tại 4-8 giờ
                                        </Text>
                                    </div>
                                </div>
                            </Card> */}

                            {/* Suggestions */}
                            {product.suggestions && (
                                <Card title="Gợi ý sử dụng" className="mb-8">
                                    <Paragraph className="text-lg leading-relaxed">
                                        {product.suggestions}
                                    </Paragraph>
                                </Card>
                            )}


                            <Card
                                title={
                                    <div className="flex items-center gap-2">
                                        <StarOutlined className="text-yellow-500" />
                                        <span>Đánh giá sản phẩm ({product.totalReviews})</span>
                                    </div>
                                }
                                className="mb-8"
                            >

                                {!loading && user && isAuthenticated && (
                                    <div className="mb-8 p-6 bg-gray-50 rounded-lg">
                                        <Title level={4} className="!mb-4">Viết đánh giá của bạn</Title>

                                        <Form
                                            form={reviewForm}
                                            layout="vertical"
                                            onFinish={handleSubmitReview}
                                            className="space-y-4"
                                        >
                                            <Row gutter={16}>
                                                <Col xs={24} sm={12}>
                                                    <Form.Item
                                                        name="rating"
                                                        label="Đánh giá"
                                                        rules={[
                                                            { required: true, message: 'Vui lòng chọn số sao!' }
                                                        ]}
                                                    >
                                                        <Rate
                                                            style={{ fontSize: '24px' }}
                                                            character="★"
                                                            tooltips={['Rất tệ', 'Tệ', 'Bình thường', 'Tốt', 'Rất tốt']}
                                                        />
                                                    </Form.Item>
                                                </Col>
                                            </Row>

                                            <Form.Item
                                                name="comment"
                                                label="Nội dung đánh giá"
                                                rules={[
                                                    { required: true, message: 'Vui lòng nhập nội dung đánh giá!' },
                                                    { min: 10, message: 'Đánh giá phải có ít nhất 10 ký tự!' },
                                                    { max: 1000, message: 'Đánh giá không được quá 1000 ký tự!' }
                                                ]}
                                            >
                                                <TextArea
                                                    rows={4}
                                                    placeholder="Chia sẻ trải nghiệm của bạn về sản phẩm này..."
                                                    showCount
                                                    maxLength={1000}
                                                />
                                            </Form.Item>

                                            <Form.Item className="!mb-0">
                                                <Button
                                                    type="primary"
                                                    htmlType="submit"
                                                    loading={submittingReview}
                                                    size="large"
                                                    className="!bg-burgundy-primary hover:!bg-burgundy-dark"
                                                    icon={<StarOutlined />}
                                                >
                                                    {submittingReview ? 'Đang gửi...' : 'Gửi đánh giá'}
                                                </Button>
                                            </Form.Item>
                                        </Form>
                                    </div>

                                )}

                                <Divider />

                                {/* Existing Reviews */}
                                {product.reviews.length > 0 ? (
                                    <div className="space-y-6">
                                        <Title level={4}>Các đánh giá từ khách hàng</Title>
                                        {product.reviews.map((review, index) => (

                                            <div key={review.id || index} className="border-b border-gray-100 pb-6 last:border-b-0">
                                                <div className="flex items-start gap-4">
                                                    <Avatar size={48} className="bg-burgundy-primary">
                                                        {(review?.reviewer_name || review?.name || 'K')?.toString().charAt(0).toUpperCase()}
                                                    </Avatar>
                                                    <div className="flex-1">
                                                        <Text strong>
                                                            {review?.reviewer_name ?
                                                                (typeof review.reviewer_name === 'string' ? review.reviewer_name : 'Khách hàng') :
                                                                (typeof review.name === 'string' ? review.name : 'Khách hàng')
                                                            }
                                                        </Text>
                                                        <div className="flex items-center gap-3 mb-3">
                                                            <Rate
                                                                disabled
                                                                defaultValue={review.rating}
                                                                className="text-sm"
                                                                style={{ fontSize: '16px' }}
                                                            />
                                                            <Text type="secondary">
                                                                {review.date} {review.time_at && `| ${review.time_at}`}
                                                            </Text>
                                                        </div>
                                                        <Paragraph className="mb-0 text-gray-700 leading-relaxed">
                                                            {review.comment || 'Không có nội dung'}
                                                        </Paragraph>
                                                    </div>
                                                </div>
                                            </div>
                                        ))}
                                    </div>
                                ) : (
                                    <div className="text-center py-8">
                                        <Text type="secondary" className="text-lg">
                                            Chưa có đánh giá nào cho sản phẩm này.
                                        </Text>
                                        <br />
                                        {!loading && user && isAuthenticated && (
                                            <Text type="secondary">
                                                Hãy là người đầu tiên đánh giá sản phẩm!
                                            </Text>
                                        )}
                                    </div>
                                )}
                            </Card>
                        </Col>
                    </Row>
                </div>
            </div>
        </div>
    );
};

export default ProductDetail;
