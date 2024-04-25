import axios, { AxiosRequestConfig, AxiosResponse } from 'axios';
import { HOST_API_URL } from '../config-global';

const TIMEOUT = 15 * 1000;

interface ApiCallOptions extends AxiosRequestConfig {
  handleError?: boolean;
}

const api = axios.create({
  baseURL: HOST_API_URL,
  timeout: TIMEOUT,
  timeoutErrorMessage: 'Request timeout',
  headers: { 'Content-Type': 'application/json' },
});

api.interceptors.response.use(
  (response: AxiosResponse) => response,
  (error) => Promise.reject((error.response && error.response.data) || 'Internal server error'),
);

const makeApiCall = async <T>(
  method: string,
  url: string,
  data: any = null,
  params: any = null,
  options: ApiCallOptions = {},
): Promise<T> => {
  try {
    const response = await api.request<T>({
      method,
      url,
      data,
      params,
      ...options,
    });
    return response.data;
  } catch (error) {
    throw new Error(error.response?.data.message || 'An error occurred');
  }
};

export const apiGet = <T>(
  url: string,
  query: any = {},
): Promise<T> => makeApiCall<T>('GET', url, null, query);

export const apiPost = <T>(
  url: string,
  data: any,
): Promise<T> => makeApiCall<T>('POST', url, data);

export const apiPut = <T>(
  url: string,
  data: any,
): Promise<T> => makeApiCall<T>('PUT', url, data);

export const apiDelete = <T>(url: string): Promise<T> =>
  makeApiCall<T>('DELETE', url);
