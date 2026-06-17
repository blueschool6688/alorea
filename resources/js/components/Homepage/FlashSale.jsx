import { ClockCircleOutlined, GiftOutlined, PercentageOutlined } from '@ant-design/icons';
import { Badge } from 'antd';
import { useCallback, useEffect, useMemo, useState } from 'react';
import { useNavigate } from 'react-router-dom';

// Custom CSS for line-clamp
const customStyles = `
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
`;

const FlashSale = ({ flashSales = [] }) => {
    const [currentOfferIndex, setCurrentOfferIndex] = useState(0);
    const [allTimeLeft, setAllTimeLeft] = useState({});
    const navigate = useNavigate();

    const flashOffers = useMemo(() => {
        return flashSales.map((sale, index) => {
            // Format discount display based on type
            let discountDisplay = '';
            let discountIcon = <PercentageOutlined className="text-2xl" />;

            if (sale.discount_type === 'percent') {
                discountDisplay = `${sale.discount_value}%`;
                discountIcon = <PercentageOutlined className="text-2xl" />;
            } else if (sale.discount_type === 'fixed') {
                // Format fixed amount (VND)
                const formattedAmount = new Intl.NumberFormat('vi-VN').format(sale.discount_value);
                discountDisplay = `-${formattedAmount}đ`;
                discountIcon = <GiftOutlined className="text-2xl" />;
            } else {
                // Fallback for old data or special offers
                discountDisplay = sale.discount_percentage > 0 ? `${sale.discount_percentage}%` : "1+1";
                discountIcon = sale.discount_percentage > 0
                    ? <PercentageOutlined className="text-2xl" />
                    : <GiftOutlined className="text-2xl" />;
            }

            return {
                id: sale.id,
                title: sale.name,
                description: sale.description,
                bannerImage: sale?.banner_url || sale?.banner_image,
                hasBanner: !!(sale?.banner_url || sale?.banner_image),
                discount: discountDisplay,
                discountType: sale.discount_type,
                discountValue: sale.discount_value,
                icon: discountIcon,
                bgColor: index % 2 === 0 ? "from-pink-500 to-rose-600" : "from-orange-500 to-red-600",
                startTime: sale.start_time,
                endTime: sale.end_time,
                maxQuantity: sale.max_quantity,
                usedQuantity: sale.used_quantity,
                status: sale.status,
                slug: sale.slug
            };
        });
    }, [flashSales]);

    // Filter active offers - bỏ filter nghiêm ngặt để test
    const activeOffers = useMemo(() => {
        // Tạm thời return tất cả để test countdown
        return flashOffers.filter(offer => offer.status === 'active');
    }, [flashOffers]);

    // Reset currentOfferIndex if it's out of bounds
    useEffect(() => {
        if (activeOffers.length > 0 && currentOfferIndex >= activeOffers.length) {
            setCurrentOfferIndex(0);
        }
    }, [activeOffers.length, currentOfferIndex]);

    // Calculate time left function - Enhanced với debug
    const calculateTimeLeft = useCallback((endTime) => {
        if (!endTime) {
            return null;
        }

        try {
            // Thử nhiều cách parse thời gian
            let endDate;

            // Nếu là ISO string
            if (typeof endTime === 'string') {
                // Thử parse trực tiếp
                endDate = new Date(endTime);

                // Nếu không valid, thử thêm Z cho UTC
                if (isNaN(endDate.getTime())) {
                    endDate = new Date(endTime + 'Z');
                }

                // Nếu vẫn không valid, thử replace T bằng space
                if (isNaN(endDate.getTime())) {
                    endDate = new Date(endTime.replace('T', ' '));
                }
            } else {
                endDate = new Date(endTime);
            }

            if (isNaN(endDate.getTime())) {
                console.error('Invalid date format:', endTime);
                return null;
            }

            const now = new Date();
            const nowTime = now.getTime();
            const endDateTime = endDate.getTime();
            const difference = endDateTime - nowTime;

            if (difference > 0) {
                const result = {
                    hours: Math.floor(difference / (1000 * 60 * 60)),
                    minutes: Math.floor((difference % (1000 * 60 * 60)) / (1000 * 60)),
                    seconds: Math.floor((difference % (1000 * 60)) / 1000)
                };
                return result;
            }

            return { hours: 0, minutes: 0, seconds: 0 };
        } catch (error) {
            return null;
        }
    }, []);

    // Main countdown timer cho TẤT CẢ offers
    useEffect(() => {
        if (activeOffers.length === 0) return;

        // Initial calculation
        const initialTimeLeft = {};
        activeOffers.forEach(offer => {
            if (offer.endTime) {
                const timeLeft = calculateTimeLeft(offer.endTime);
                initialTimeLeft[offer.id] = timeLeft;
            } else {
                initialTimeLeft[offer.id] = null;
            }
        });
        setAllTimeLeft(initialTimeLeft);

        const timer = setInterval(() => {
            const newAllTimeLeft = {};
            let hasExpiredOffer = false;

            activeOffers.forEach((offer, index) => {
                if (offer.endTime) {
                    const timeLeft = calculateTimeLeft(offer.endTime);
                    newAllTimeLeft[offer.id] = timeLeft;

                    // Check if this offer has expired
                    if (timeLeft && timeLeft.hours === 0 && timeLeft.minutes === 0 && timeLeft.seconds === 0) {
                        if (index === currentOfferIndex) {
                            hasExpiredOffer = true;
                        }
                    }
                } else {
                    newAllTimeLeft[offer.id] = null;
                }
            });
            setAllTimeLeft(newAllTimeLeft);

            // Switch to next offer if current one expired
            if (hasExpiredOffer) {
                setCurrentOfferIndex(prev => (prev + 1) % activeOffers.length);
            }
        }, 1000);

        return () => {
            console.log('Clearing timer');
            clearInterval(timer);
        };
    }, [activeOffers, currentOfferIndex, calculateTimeLeft]);

    // Format time with leading zeros
    const formatTime = useCallback((time) => {
        return time?.toString().padStart(2, '0') || '00';
    }, []);

    // Format countdown display - Enhanced với better error handling
    const formatCountdownDisplay = useCallback((timeLeft) => {
        if (!timeLeft) {
            return "Đang tính...";
        }

        if (typeof timeLeft !== 'object' || timeLeft.hours === undefined) {
            return "Lỗi tính toán";
        }

        const { hours, minutes, seconds } = timeLeft;

        // If more than 24 hours, show days
        if (hours >= 24) {
            const days = Math.floor(hours / 24);
            const remainingHours = hours % 24;
            return `Còn ${days}d ${formatTime(remainingHours)}:${formatTime(minutes)}:${formatTime(seconds)}`;
        }

        // Standard format: HH:MM:SS
        const result = `Còn ${formatTime(hours)}:${formatTime(minutes)}:${formatTime(seconds)}`;
        return result;
    }, [formatTime]);

    // Get urgency level for styling
    const getUrgencyLevel = useCallback((timeLeft) => {
        if (!timeLeft || typeof timeLeft !== 'object') return 'normal';

        const totalMinutes = timeLeft.hours * 60 + timeLeft.minutes;

        if (totalMinutes <= 10) return 'critical'; // ≤ 10 phút
        if (totalMinutes <= 60) return 'urgent';   // ≤ 1 giờ
        if (totalMinutes <= 360) return 'warning'; // ≤ 6 giờ
        return 'normal';
    }, []);

    // Handler for clicking offers
    const handleOfferClick = useCallback((index) => {
        setCurrentOfferIndex(index);
    }, []);

    // Helper function để check xem offer có hết thời gian không
    const isOfferExpired = useCallback((offerId) => {
        const timeLeft = allTimeLeft[offerId];
        return timeLeft && timeLeft.hours === 0 && timeLeft.minutes === 0 && timeLeft.seconds === 0;
    }, [allTimeLeft]);

    // Render Flash Sale Item with Banner
    const renderFlashSaleWithBanner = (offer, index, offerTimeLeft, isExpired, urgencyLevel) => {
        // Determine ribbon color based on discount type
        const ribbonColor = offer.discountType === 'fixed' ? 'green' : 'volcano';

        return (
            <Badge.Ribbon
                key={offer.id}
                text={offer?.title?.length > 20 ? offer.title.substring(0, 20) + '...' : offer?.title}
                color="red"
                placement="start"
                className="text-xs sm:text-sm"
            >
                <Badge.Ribbon
                    text={offer.discount}
                    color={'volcano'}
                    className="text-xs sm:text-sm font-bold"
                >
                    <div
                        className={`relative w-full overflow-hidden rounded-lg sm:rounded-xl transition-all duration-700 ease-in-out transform cursor-pointer ${index === currentOfferIndex
                            ? 'ring-2 ring-yellow-400 shadow-lg'
                            : isExpired
                                ? 'opacity-60'
                                : 'hover:shadow-xl hover:scale-[1.02]'
                            } ${isExpired ? 'cursor-not-allowed' : ''}`}
                        onClick={() => !isExpired && handleOfferClick(index)}
                    >
                        <img
                            src={offer.bannerImage}
                            alt={offer.title}
                            className="w-full h-full object-cover"
                        />


                        {/* Ribbon Content - Responsive Design */}
                        <div className="absolute inset-0 flex items-center justify-end z-20 p-2 sm:p-4">
                            {offer.endTime && (
                                <div className="flex flex-col items-center justify-center">
                                    <div className={`px-2 py-1 sm:p-3 rounded-md sm:rounded-lg backdrop-blur-sm font-mono text-xs sm:text-sm font-bold transition-all duration-300 text-center ${urgencyLevel === 'critical'
                                        ? 'bg-red-500/90 text-white animate-pulse shadow-lg shadow-red-500/50'
                                        : urgencyLevel === 'urgent'
                                            ? 'bg-orange-500/90 text-white shadow-lg shadow-orange-500/50'
                                            : urgencyLevel === 'warning'
                                                ? 'bg-yellow-500/90 text-white shadow-lg shadow-yellow-500/50'
                                                : 'bg-black/50 text-white border border-white/30'
                                        }`}>
                                        <div className="flex items-center justify-center gap-1 mb-1">
                                            <ClockCircleOutlined className={
                                                urgencyLevel === 'critical'
                                                    ? 'animate-bounce text-white text-xs'
                                                    : 'animate-pulse text-white text-xs'
                                            } />
                                            <span className="text-xs font-normal hidden sm:inline">Còn lại</span>
                                        </div>
                                        <div className="text-xs sm:text-sm font-bold whitespace-nowrap">
                                            {formatCountdownDisplay(offerTimeLeft).replace('Còn ', '')}
                                        </div>
                                    </div>
                                </div>
                            )}
                        </div>
                    </div>
                </Badge.Ribbon>
            </Badge.Ribbon>
        );
    };

    // Render Flash Sale Item without Banner
    const renderFlashSaleWithoutBanner = (offer, index, offerTimeLeft, isExpired, urgencyLevel) => {
        // Determine ribbon color based on discount type
        const ribbonColor = offer.discountType === 'fixed' ? 'green' : 'volcano';

        return (
            <Badge.Ribbon
                text={offer?.title?.length > 20 ? offer.title.substring(0, 20) + '...' : offer?.title}
                color="volcano"
                placement="start"
                className="text-xs sm:text-sm"
            >
                <Badge.Ribbon
                    text={offer.discount}
                    color={ribbonColor}
                    className="text-xs sm:text-sm font-bold"
                >
                    <div
                        key={offer.id}
                        className={`relative w-full rounded-lg sm:rounded-xl transition-all duration-500 ${index === currentOfferIndex
                            ? 'ring-2 ring-yellow-400 shadow-lg'
                            : isExpired
                                ? 'opacity-60'
                                : 'hover:shadow-xl'
                            } ${isExpired ? 'cursor-not-allowed' : 'cursor-pointer'}`}
                        onClick={() => !isExpired && handleOfferClick(index)}
                    >
                        <div className={`bg-gradient-to-r ${offer.bgColor} p-4 sm:p-6 rounded-lg sm:rounded-xl`}>
                            <div className="flex flex-col sm:flex-row items-center justify-between mt-7">
                                <div className="flex items-center mb-3 sm:mb-0">
                                    <div className="flex items-center justify-center w-10 h-10 sm:w-12 sm:h-12 bg-white/20 backdrop-blur-sm rounded-full mr-3">
                                        {offer.icon}
                                    </div>
                                    <div className="text-left">
                                        <h3 className="text-lg sm:text-xl font-bold text-white">{offer.title}</h3>
                                        {offer.description && (
                                            <p className="text-white/80 text-xs sm:text-sm line-clamp-2">{offer.description}</p>
                                        )}
                                        {/* Discount Value Display */}
                                        <div className="mt-1">
                                            <span className="text-yellow-200 text-sm font-bold">
                                                {offer.discountType === 'fixed' ? 'Giảm ' : 'Giảm '}
                                                <span className="text-lg">{offer.discount}</span>
                                            </span>
                                        </div>
                                    </div>
                                </div>

                                <div className="flex flex-col items-center">
                                    {offer.endTime && (
                                        <div className={`px-2 py-1 rounded-md text-xs sm:text-sm font-mono font-bold ${urgencyLevel === 'critical'
                                            ? 'bg-white/90 text-red-600 animate-pulse'
                                            : 'bg-white/75 text-gray-800'
                                            }`}>
                                            <div className="flex items-center justify-center gap-1">
                                                <ClockCircleOutlined className={
                                                    urgencyLevel === 'critical'
                                                        ? 'animate-bounce'
                                                        : ''
                                                } />
                                                {formatCountdownDisplay(offerTimeLeft)}
                                            </div>
                                        </div>
                                    )}
                                </div>
                            </div>

                            {offer.maxQuantity && (
                                <div className="mt-4 bg-white/20 backdrop-blur-sm rounded-lg p-2 text-xs sm:text-sm text-white text-center">
                                    Giới hạn: {offer.maxQuantity} sản phẩm / đơn hàng
                                </div>
                            )}
                        </div>
                    </div>
                </Badge.Ribbon>
            </Badge.Ribbon>
        );
    };
    // Nếu không có activeOffers, hiển thị tất cả flashOffers
    const offersToShow = activeOffers.length > 0 ? activeOffers : flashOffers;

    return (
        <>
            <style>{customStyles}</style>
            <section className="relative bg-white py-12 overflow-hidden">
                <div className="text-center mb-12">
                    <h2 className="font-serif text-2xl md:text-5xl lg:text-6xl font-light text-gray-900 mb-4 tracking-wide">
                        FLASH SALE
                    </h2>
                    <div className="w-24 h-1 bg-burgundy-primary mx-auto"></div>
                </div>

                <div className="relative z-10 mx-auto px-4 sm:px-6 lg:px-8">
                    {/* Main Content */}
                    <div className="text-center mb-8">
                        {/* Title */}
                        <h2 className="text-2xl sm:text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 leading-tight px-2">
                            <span className="bg-gradient-to-r from-yellow-400 to-orange-500 bg-clip-text text-transparent">
                                Ưu đãi đặc biệt
                            </span>
                            <span className="block sm:inline sm:ml-2 text-lg sm:text-2xl md:text-3xl lg:text-4xl xl:text-5xl">
                                – chỉ trong hôm nay
                            </span>
                        </h2>

                        {/* Dynamic Flash Sale Offers - Based on banner availability */}
                        <div className="mb-8 px-2 sm:px-4">
                            <div className="rounded-2xl p-2 sm:p-4 lg:p-6">
                                <div className="grid grid-cols-1 md:grid-cols-1 xl:grid-cols-1 gap-3 sm:gap-4 lg:gap-6">
                                    {offersToShow.map((offer, index) => {
                                        const offerTimeLeft = allTimeLeft[offer.id];
                                        const isExpired = isOfferExpired(offer.id);
                                        const urgencyLevel = getUrgencyLevel(offerTimeLeft);

                                        return offer.hasBanner
                                            ? renderFlashSaleWithBanner(offer, index, offerTimeLeft, isExpired, urgencyLevel)
                                            : renderFlashSaleWithoutBanner(offer, index, offerTimeLeft, isExpired, urgencyLevel);
                                    })}
                                </div>
                            </div>
                        </div>
                        <div className="animate-bounce px-4">
                            <button
                                className="cursor-pointer group w-full sm:w-auto max-w-xs mx-auto px-4 sm:px-6 lg:px-8 py-2 sm:py-3 lg:py-4 border-2 border-black text-black font-semibold text-sm sm:text-base rounded-lg transition-all duration-500 hover:bg-black hover:text-white hover:scale-105 sm:hover:scale-110 hover:shadow-xl hover:border-transparent active:scale-95 transform relative overflow-hidden"
                                onClick={() => navigate('/products')}
                            >
                                <span className="relative z-10">Sở hữu ngay</span>
                            </button>
                        </div>
                    </div>
                </div>
            </section>
        </>
    );
};

export default FlashSale;
