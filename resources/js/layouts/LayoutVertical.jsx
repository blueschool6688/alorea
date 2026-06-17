import Footer from '@/layouts/components/Footer';
import HeaderVertical from '@/layouts/components/HeaderVertical';
import { useCart } from '@/lib/context/CartContext';
import { useSettings } from '@/lib/context/SettingContext';
import { Layout, Skeleton } from 'antd';
import { useEffect, useRef } from 'react';
import { useDispatch } from 'react-redux';
import { Outlet, useLocation, useNavigate } from 'react-router-dom';

const LayoutHorizontal = () => {
    const dispatch = useDispatch();
    const navigate = useNavigate();
    const location = useLocation();
    const { cartCount } = useCart();
    const { logo, loading, isLoaded } = useSettings();

    const contactRef = useRef(null);


    const scrollToContact = () => {
        contactRef.current?.scrollIntoView({ behavior: "smooth" });
    };


    useEffect(() => {
        window.scrollTo(0, 0);
        const timer = setTimeout(() => {
            window.scrollTo({
                top: 0,
                left: 0,
                behavior: 'smooth'
            });
        }, 100);

        return () => clearTimeout(timer);
    }, [location.pathname]);
    useEffect(() => {
        if ('scrollRestoration' in history) {
            history.scrollRestoration = 'manual';
        }
    }, []);

    // Show skeleton while loading settings
    if (loading && !isLoaded) {
        return (
            <div id='vertical-layout'>
                {/* Header Skeleton */}
                <div style={{
                    padding: '16px 24px',
                    background: '#fff',
                    borderBottom: '1px solid #f0f0f0',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center'
                }}>
                    {/* Logo skeleton */}

                </div>
            </div>
        );
    }

    return (
        <div id='vertical-layout' className='overflow-hidden'>
            {loading && !isLoaded ? (
                <div style={{
                    padding: '16px 24px',
                    background: '#fff',
                    borderBottom: '1px solid #f0f0f0',
                    display: 'flex',
                    justifyContent: 'space-between',
                    alignItems: 'center'
                }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 16 }}>
                        <Skeleton.Avatar size={40} shape="square" active />
                        <Skeleton.Input style={{ width: 120, height: 24 }} active />
                    </div>

                    {/* Navigation skeleton */}
                    <div style={{ display: 'flex', gap: 24 }}>
                        <Skeleton.Input style={{ width: 60, height: 20 }} active />
                        <Skeleton.Input style={{ width: 60, height: 20 }} active />
                        <Skeleton.Input style={{ width: 60, height: 20 }} active />
                        <Skeleton.Input style={{ width: 60, height: 20 }} active />
                    </div>

                    {/* Cart/User skeleton */}
                    <div style={{ display: 'flex', gap: 12 }}>
                        <Skeleton.Avatar size={32} active />
                        <Skeleton.Avatar size={32} active />
                    </div>

                </div>
            ) : (
                <HeaderVertical
                    logo={logo}
                    dispatch={dispatch}
                    navigate={navigate}
                    cartCount={cartCount}
                    scrollToContact={scrollToContact}
                />
            )
            }
            <Layout>
                <Outlet />
            </Layout>

            <Footer contactRef={contactRef} />
        </div >
    );
}

export default LayoutHorizontal
