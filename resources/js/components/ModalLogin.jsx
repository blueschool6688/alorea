import showMessage from '@/utils/showMessage';
import { Button, Form, Input, Modal } from 'antd';
import { memo, useCallback, useMemo, useState } from 'react';
import { useSettings } from '@/lib/context/SettingContext';

const EMAIL_RULES = [
    { required: true, message: 'Email không được bỏ trống!' },
    { type: 'email', message: 'Email không hợp lệ!' }
];

const PASSWORD_RULES = [
    { required: true, message: 'Mật khẩu không được bỏ trống!' }
];

const ModalLogin = (props) => {
    const [form] = Form.useForm();
    const { isOpen, setOpenModal, dispatch, navigate, login, loading, location, searchParams } = props
    const {settings}  = useSettings();

    const [state, setState] = useState({
        showLogin: false
    });

    // Destructure state for cleaner code
    const { title, showLogin } = state;


    const resetState = useCallback(() => {
        setState(prev => ({
            ...prev,
            showLogin: false,
            isExists: false
        }));
        form.resetFields(['email', 'password']);
    }, [form]);

    const handleCloseModal = useCallback(() => {
        resetState();
        setOpenModal(false);
        const params = new URLSearchParams(window.location.search);
        if (params.has("login_show")) {
            params.delete("login_show");
            const newUrl = `${window.location.pathname}${params.toString() ? "?" + params.toString() : ""}`;
            window.history.replaceState({}, "", newUrl);
        }
    }, [resetState, setOpenModal]);


    const handleAuthentication = useCallback(async (values) => {
        try {
            const data = await login(values);
            if (data) {
                form.resetFields(['email', 'password']);
                setOpenModal(false)
                if (location.pathname === '/404' || location.pathname.includes('error')) {
                    navigate('/', { replace: true });
                }
            }

        } catch (error) {
            showMessage(error.message || 'Có lỗi xảy ra', 'error');
        }
    }, [login, location.pathname, form, setOpenModal]);

    const renderForm = useMemo(() => (
        <Form
            form={form}
            name="form-check-mail"
            onFinish={handleAuthentication}
            autoComplete="off"
            layout="vertical"
        >
            <Form.Item
                label="Email"
                className="text-gray-700 mb-3"
                name="email"
                rules={EMAIL_RULES}
            >
                <Input
                    type="email"
                    placeholder="Nhập Email của bạn"
                    className="w-full px-4 py-2 rounded"
                />
            </Form.Item>

            <Form.Item
                label="Mật khẩu"
                className="text-gray-700 mb-3"
                name="password"
                rules={PASSWORD_RULES}
            >
                <Input.Password
                    placeholder="Nhập mật khẩu"
                    className="w-full px-4 py-2 rounded"
                />
            </Form.Item>


            <Button
                htmlType="submit"
                type="primary"
                loading={loading}
                className="w-full py-3 mt-3 !bg-burgundy-primary rounded font-medium transition duration-200"
            >
                Đăng nhập
            </Button>
        </Form>
    ), [form, showLogin, loading, resetState]);


    if (!isOpen) return null;

    return (
        <Modal open={isOpen} onCancel={handleCloseModal} footer={null}>
            <div className="bg-white rounded-lg w-full p-6">
                <div className="flex justify-center">
                    <img src={settings?.logo_url} alt="Logo" className="h-25" />
                </div>
                {renderForm}
            </div>
            <div className="">
                <p className="text-center text-sm text-gray-600 mt-4">
                    Chưa có tài khoản?
                    <Button type="link" onClick={() => {
                        resetState();
                        navigate('/register');
                    }} className="!text-burgundy-primary font-medium hover:underline">
                        Đăng ký ngay
                    </Button>
                </p>
            </div>
        </Modal>
    );
};

export default memo(ModalLogin);
