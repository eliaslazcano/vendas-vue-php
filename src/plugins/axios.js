import axios from 'axios';
import store from '@/store';

/**
 * @param {function(string, number): void} errorCallback
 * @param {string} baseURL
 * @param {boolean} withCredentials
 * @param {number} timeout
 * @return AxiosInstance
 */
export const createAxiosInstance = (errorCallback, baseURL = 'http://localhost:3000', withCredentials = true, timeout = 30000) => {
  const axiosInstance = axios.create({
    headers: {'Cache-Control': 'no-cache'},
    baseURL,
    timeout,
    withCredentials,
  });
  const beforeSend = (conf) => {
    if (store.state.session.token) conf.headers.Authorization = store.state.session.token; //Insert Authorization Header
    return conf;
  };
  const onError = async (error) => {
    if (error.response) {
      if (error.response.data) {
        if (typeof error.response.data === 'object' && error.response.data instanceof Blob && error.response.data.type === 'application/json') {
          const fileReader = new FileReader();
          fileReader.onload = () => {
            const data = JSON.parse(fileReader.result);
            errorCallback(data.mensagem, error.response.status);
          };
          fileReader.readAsText(error.response.data);
        } else if (error.response.data.mensagem) errorCallback(error.response.data.mensagem, error.response.status);
        else errorCallback('Ocorreu um erro na conexão', error.response.status);
      }
      if (error.response.status === 410) await store.dispatch('session/signout');
    } else if (error.request) {
      if (error.code === 'ECONNABORTED') errorCallback('A conexão excedeu o tempo limite');
      else if (error.request.status === 0) errorCallback('Sem conexão com o servidor, verifique a internet ou tente mais tarde');
      else errorCallback('ERRO ' + error.request.status);
    }
    // eslint-disable-next-line no-console
    console.error('HTTP Error: ' + error.message);
    return Promise.reject(error);
  };
  axiosInstance.interceptors.request.use(beforeSend, error => Promise.reject(error));
  axiosInstance.interceptors.response.use(res => res, onError);
  return axiosInstance;
};
export default createAxiosInstance((message) => store.commit('dialog/show', message));