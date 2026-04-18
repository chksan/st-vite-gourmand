import { ref } from 'vue';
import axios from 'axios';

const user = ref(null);

export function useAuth() {
    const login = async (email, password) => {
        const res = await axios.post('/v1/login', { email, password });
        user.value = res.data.user;
        return res.data;
    };

    const register = async (formData) => {
        const res = await axios.post('/v1/register', formData);
        user.value = res.data.user;
        return res.data;
    };

    const logout = async () => {
        await axios.post('/v1/logout');
        user.value = null;
    };

    const getMe = async () => {
        try {
            const res = await axios.get('/v1/me');
            user.value = res.data;
            return res.data;
        } catch {
            user.value = null;
        }
    };

    return { user, login, register, logout, getMe };
}