import axios from 'axios';
import { showErrorToast, showSuccessToast } from '@/utility/toastUtils';

const axiosClient = axios.create({
  baseURL: import.meta.env.VITE_API_URL,
  headers: {
    'X-Requested-With': 'XMLHttpRequest',
    'Access-Control-Allow-Origin': '*',
    'Access-Control-Allow-Methods': 'GET,PUT,POST,DELETE,PATCH,OPTIONS',
  },
});

axiosClient.interceptors.response.use(
  (response) => {
    if (response.status === 200 && response.data.message) {
      showSuccessToast(response.data.message);
    }
    return response;
  },
  (error) => {
    showErrorToast(error);
    return Promise.reject(error);
  }
);

export default axiosClient;
