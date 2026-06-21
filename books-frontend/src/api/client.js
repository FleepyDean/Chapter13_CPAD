import axios from 'axios';
import { useAuth } from '../stores/auth';
const api = axios.create({
 baseURL: import.meta.env.VITE_API_BASE_URL,
 timeout: 10_000,
});
api.interceptors.request.use((cfg) => {
 const auth = useAuth();
 if (auth.token) cfg.headers.Authorization = `Bearer ${auth.token}`;
 return cfg;
});
api.interceptors.response.use(
 (res) => res,
 (err) => {
 if (err.response?.status === 401) useAuth().logout();
 return Promise.reject(err);
 }
);
export default api;