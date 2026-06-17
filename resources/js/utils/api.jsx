import GeneralPath from "@/routes/GeneralPath";
import axios from "axios";

class ApiClient {
    constructor() {
        this.client = axios.create({
            baseURL: "/api/v1",
            headers: {
                "Content-Type": "application/json",
                "Accept": "application/json",
                "X-Requested-With": "XMLHttpRequest",
            },
        });

        this.setAuthToken();

        this.client.interceptors.request.use(
            (config) => {
                const token = localStorage.getItem(GeneralPath.AUTH_TOKEN_KEY);
                if (token) {
                    config.headers.Authorization = `Bearer ${token}`;
                }
                return config;
            },
            (error) => {
                return Promise.reject(error);
            }
        );

        // Interceptor để handle 401 errors (token expired/invalid)
        this.client.interceptors.response.use(
            (response) => response,
            (error) => {
                if (error.response?.status === 401) {
                    this.clearAuthToken();
                    localStorage.removeItem('user');
                    window.location.href = '/';
                }
                return Promise.reject(error);
            }
        );
    }

    // Set token vào header và localStorage
    setAuthToken(token = null) {
        const authToken = token || localStorage.getItem(GeneralPath.AUTH_TOKEN_KEY);
        if (authToken) {
            this.client.defaults.headers.common['Authorization'] = `Bearer ${authToken}`;
            if (token) {
                localStorage.setItem(GeneralPath.AUTH_TOKEN_KEY, token);
            }
        }
    }

    clearAuthToken() {
        delete this.client.defaults.headers.common['Authorization'];
        localStorage.removeItem(GeneralPath.AUTH_TOKEN_KEY);
    }

    async request(endpoint, options = {}) {
        try {
            const response = await this.client.request({
                url: endpoint,
                ...options,
            });
            return response.data;
        } catch (error) {
            const errorMessage = error.response?.data?.errors?.title || error.response?.data?.errors?.data;
            throw errorMessage;
        }
    }

    get(endpoint, config = {}) {
        return this.request(endpoint, { method: "GET", ...config });
    }

    post(endpoint, data, config = {}) {
        return this.request(endpoint, { method: "POST", data, ...config });
    }

    put(endpoint, data, config = {}) {
        return this.request(endpoint, { method: "PUT", data, ...config });
    }

    delete(endpoint, config = {}) {
        return this.request(endpoint, { method: "DELETE", ...config });
    }
}

export default new ApiClient();
