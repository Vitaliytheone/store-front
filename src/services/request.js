import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = '_csrf-frontend';

const instance = axios.create({
	baseURL: ' '
});

instance.interceptors.response.use((response) => response.data, (error) => Promise.reject(error.response));

export default instance;
