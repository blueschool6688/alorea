import axiosIntance from '@/utils/api';

class AuthAPI {

    async loginForm(payload) {
        return await axiosIntance.post('user/login', payload)
    }
    async registerForm(payload) {
        return await axiosIntance.post('user/register', payload)
    }
    async logoutForm() {
        return await axiosIntance.post('user/logout')
    }
}


export default new AuthAPI();
