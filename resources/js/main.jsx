import { AuthProvider } from '@/hooks/useAuth';
import { CartProvider } from '@/lib/context/CartContext';
import { store } from '@/slices/store';
import { ConfigProvider } from 'antd';
import ReactDOM from 'react-dom/client';
import { Provider } from 'react-redux';
import App from './App';

ConfigProvider.config({
    compatible: true
});

const root = ReactDOM.createRoot(document.getElementById('root'));
root.render(
    <Provider store={store}>
        <CartProvider>
            <AuthProvider>
                <App />
            </AuthProvider>
        </CartProvider>
    </Provider>
);
