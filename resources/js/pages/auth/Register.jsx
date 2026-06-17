import useAuth from "@/hooks/useAuth";
import { useSettings } from '@/lib/context/SettingContext';
import { EyeInvisibleOutlined, EyeTwoTone, LockOutlined, MailOutlined, PhoneOutlined, UserOutlined } from '@ant-design/icons';
import { Button, Card, Form, Input, Skeleton, Space, Typography } from "antd";
import { useState } from "react";
import { useNavigate } from "react-router-dom";
const { Title, Text, Link: AntLink } = Typography;

export default function Register() {
    const navigate = useNavigate();
    const [loadingData, setLoading] = useState(false);
    const [form] = Form.useForm();
    const { register } = useAuth()
    const { settings, loading } = useSettings();

    const onFinish = async (values) => {
        setLoading(true);
        try {
            const res = await register(values);
            if (res) {
                navigate("/", { replace: true });
            }
        } catch (err) {
            if (err.errors) {
                const formErrors = {};
                Object.entries(err.errors).forEach(([field, msgs]) => {
                    formErrors[field] = {
                        errors: Array.isArray(msgs) ? msgs.map(msg => ({ message: msg })) : [{ message: msgs }]
                    };
                });
                form.setFields(Object.entries(formErrors).map(([name, obj]) => ({ name, ...obj })));
            }
        } finally {
            setLoading(false);
        }
    };

    return (

        loading ? (
            <div className="min-h-screen flex items-center justify-center bg-white p-4 !font-sans">
                <Card className="w-full max-w-md">
                    <Skeleton active avatar paragraph={{ rows: 6 }} />
                </Card>
            </div>
        ) : (
            <div className="min-h-screen flex items-center justify-center bg-white p-4 !font-sans">
                <Card
                    bordered={false}
                    className="w-full max-w-md"
                    bodyStyle={{ padding: '24px' }}
                >
                    <div className="text-center mb-6">
                        <img
                            src={settings?.logo_url}
                            alt="Logo"
                            className="h-16 w-16 mx-auto mb-4"
                            style={{ borderRadius: 0 }}
                        />
                        <Title level={2} style={{ marginBottom: 8 }} className="!font-serif">
                            {"Đăng ký"}
                        </Title>
                        <Text type="secondary">
                            {"Tạo tài khoản của bạn để tiếp tục."}
                        </Text>
                    </div>

                    <Form
                        form={form}
                        layout="vertical"
                        name="register_form"
                        onFinish={onFinish}
                        requiredMark={false}
                        validateTrigger="onBlur"
                    >
                        <Form.Item
                            name="name"
                            label={"Họ và tên"}
                            rules={[
                                { required: true, message: "Vui lòng nhập họ và tên!" }
                            ]}
                        >
                            <Input
                                prefix={<UserOutlined className="site-form-item-icon" />}
                                placeholder={"Họ và tên"}
                                size="large"
                                disabled={loadingData}
                                autoFocus
                            />
                        </Form.Item>

                        <Form.Item
                            name="email"
                            label={"Email"}
                            rules={[
                                { required: true, message: "Vui lòng nhập email!" },
                                { type: 'email', message: "Vui lòng nhập email hợp lệ!" }
                            ]}
                        >
                            <Input
                                prefix={<MailOutlined className="site-form-item-icon" />}
                                placeholder={"Email"}
                                size="large"
                                disabled={loadingData}
                            />
                        </Form.Item>


                        <Form.Item
                            name="phone"
                            label={"Số điện thoại"}
                            rules={[
                                { required: true, message: "Vui lòng nhập số điện thoại!" },
                                { pattern: /^[0-9]{10}$/, message: "Số điện thoại không hợp lệ!" }
                            ]}
                        >
                            <Input
                                prefix={<PhoneOutlined className="site-form-item-icon" />}
                                placeholder={"Số điện thoại"}
                                size="large"
                                disabled={loadingData}
                            />
                        </Form.Item>

                        <Form.Item
                            name="password"
                            label={"Mật khẩu"}
                            rules={[
                                { required: true, message: "Vui lòng nhập mật khẩu!" },
                                { min: 8, message: "Mật khẩu phải có ít nhất 8 ký tự!" }
                            ]}
                        >
                            <Input.Password
                                prefix={<LockOutlined className="site-form-item-icon" />}
                                placeholder={"Mật khẩu"}
                                size="large"
                                disabled={loadingData}
                                iconRender={visible => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}
                            />
                        </Form.Item>

                        <Form.Item
                            name="password_confirmation"
                            label={"Xác nhận mật khẩu"}
                            dependencies={['password']}
                            rules={[
                                { required: true, message: "Vui lòng xác nhận mật khẩu!" },
                                ({ getFieldValue }) => ({
                                    validator(_, value) {
                                        if (!value || getFieldValue('password') === value) {
                                            return Promise.resolve();
                                        }
                                        return Promise.reject(new Error("Hai mật khẩu không khớp!"));
                                    },
                                }),
                            ]}
                        >
                            <Input.Password
                                prefix={<LockOutlined className="site-form-item-icon" />}
                                placeholder={"Xác nhận mật khẩu"}
                                size="large"
                                disabled={loadingData}
                                iconRender={visible => (visible ? <EyeTwoTone /> : <EyeInvisibleOutlined />)}
                            />
                        </Form.Item>

                        <Form.Item style={{ marginTop: 24 }}>
                            <Button
                                type="primary"
                                htmlType="submit"
                                loading={loadingData}
                                block
                                size="large"
                                className="!bg-burgundy-primary !hover:!bg-burgundy-primary/80 rounded font-medium transition duration-200 mt-4"
                            >
                                {"Đăng ký"}
                            </Button>
                        </Form.Item>

                        <div className="text-center">
                            <Space>
                                <Text type="secondary">
                                    {"Bạn đã có tài khoản?"}
                                </Text>
                                <AntLink
                                    onClick={() => navigate("/?login_show=true")}
                                    className="hover:text-blue-600 !text-burgundy-primary font-sans"
                                >
                                    {"Đăng nhập ngay"}
                                </AntLink>
                            </Space>
                        </div>
                    </Form>
                </Card>
            </div>
        )

    );
}
