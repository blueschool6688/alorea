import GeneralPath from "@/routes/GeneralPath";
import api from "@/utils/api";
import showMessage from "@/utils/showMessage";
import { createContext, useContext, useEffect, useState } from "react";

const AuthContext = createContext(null);

export const AuthProvider = ({ children }) => {
    const [user, setUser] = useState(null);
    const [loading, setLoading] = useState(true);
    const [initialized, setInitialized] = useState(false);
    const [isAuthenticated, setIsAuthenticated] = useState(false);

    const loadUser = async () => {
        try {
            const response = await api.get(GeneralPath.USER_ENDPOINT);
            if (response && response.data) {
                setUser(response.data);
                setIsAuthenticated(true);
                localStorage.setItem(GeneralPath.USER_KEY, JSON.stringify(response.data));
                return response.data;
            } else {
                // Nếu không get được user, reset state
                setUser(null);
                setIsAuthenticated(false);
                localStorage.removeItem(GeneralPath.USER_KEY);
                api.clearAuthToken();
                return null;
            }
        } catch (error) {
            console.error("Load user error:", error);
            setUser(null);
            setIsAuthenticated(false);
            localStorage.removeItem(GeneralPath.USER_KEY);
            api.clearAuthToken();
            return null;
        }
    };

    // Khởi tạo auth state khi ứng dụng load
    useEffect(() => {
        const initAuth = async () => {
            setLoading(true);
            try {
                // Kiểm tra token trong localStorage
                const token = localStorage.getItem(GeneralPath.AUTH_TOKEN_KEY);
                if (token) {
                    api.setAuthToken(token);
                    const userData = await loadUser();
                    if (!userData) {
                        api.clearAuthToken();
                        localStorage.removeItem(GeneralPath.USER_KEY);
                    } else {
                        console.log('Auth initialized with user:', userData);
                    }
                } else {
                    // Không có token, clear state
                    setUser(null);
                    setIsAuthenticated(false);
                    localStorage.removeItem(GeneralPath.USER_KEY);
                    api.clearAuthToken();
                }
            } catch (error) {
                console.error("Authentication initialization error:", error);
                setUser(null);
                setIsAuthenticated(false);
                localStorage.removeItem(GeneralPath.USER_KEY);
                api.clearAuthToken();
            } finally {
                setLoading(false);
                setInitialized(true);
            }
        };

        initAuth();
    }, []);

    // Login function với token
    const login = async (credentials) => {
        setLoading(true);
        try {
            const response = await api.post(GeneralPath.LOGIN_ENDPOINT, credentials);
            console.log(response)
            if (response?.data) {
                const { token } = response.data;
                if (token) {
                    api.setAuthToken(token);
                } else {
                    throw new Error('Token not received from server');
                }
                await loadUser()
                setIsAuthenticated(true);
                showMessage("Đăng nhập thành công", "success");
                return response.data;
            }
            return null;
        } catch (error) {
            setUser(null);
            setIsAuthenticated(false);
            localStorage.removeItem(GeneralPath.USER_KEY);
            api.clearAuthToken();
            showMessage(error.response?.data?.errors?.data || error.response?.data?.errors?.title || "Đăng nhập không thành công", "error");
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Register function với token
    const register = async (userData) => {
        setLoading(true);
        try {
            const response = await api.post(GeneralPath.REGISTER_ENDPOINT, userData);
            if (response?.data) {
                const { token } = response.data;
                if (token) {
                    api.setAuthToken(token);
                } else {
                    throw new Error('Token not received from server');
                }
                await loadUser()
                setIsAuthenticated(true);
                showMessage("Đăng ký thành công", "success");
                return response.data;
            }
            return null;
        } catch (error) {
            setUser(null);
            setIsAuthenticated(false);
            localStorage.removeItem(GeneralPath.USER_KEY);
            api.clearAuthToken();
            showMessage(error.response?.data?.errors?.data || error.response?.data?.errors?.title || "Đăng ký không thành công", "error");
            return null;
        } finally {
            setLoading(false);
        }
    };

    // Logout function với token
    const logout = async () => {
        setLoading(true);
        try {
            // Gọi API logout để revoke token trên server
            await api.post(GeneralPath.LOGOUT_ENDPOINT);
        } catch (error) {
            console.error("Logout error:", error);
        } finally {
            // Luôn clear state và token
            setUser(null);
            setIsAuthenticated(false);
            localStorage.removeItem(GeneralPath.USER_KEY);
            api.clearAuthToken();
            setLoading(false);
        }
        return true;
    };

    // Hàm cập nhật thông tin user
    const updateUserData = async (newUserData) => {
        try {
            if (newUserData) {
                setUser(newUserData);
                setIsAuthenticated(true);
                localStorage.setItem(GeneralPath.USER_KEY, JSON.stringify(newUserData));
                return true;
            } else {
                await loadUser();
                return true;
            }
        } catch (error) {
            console.error("Error updating user data:", error);
            return false;
        }
    };

    return (
        <AuthContext.Provider
            value={{
                user,
                loading,
                initialized,
                isAuthenticated,
                login,
                register,
                logout,
                updateUserData
            }}
        >
            {children}
        </AuthContext.Provider>
    );
};

// Hook để sử dụng auth context
export const useAuth = () => {
    const context = useContext(AuthContext);
    if (!context) {
        throw new Error("useAuth must be used within an AuthProvider");
    }
    return context;
};

export default useAuth;
