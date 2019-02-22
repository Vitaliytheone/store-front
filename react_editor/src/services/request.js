import axios from 'axios';

axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
axios.defaults.xsrfCookieName = '_csrf-frontend';

const instance = axios.create({
  baseURL: ' '
});

instance.interceptors.response.use(
  response => response.data,
  error => {
    if (error.response && error.response.data && error.response.data.url) {
      window.location = error.response.data.url;
    } else {
      return Promise.reject(error);
    }
  }
);
export default instance;
