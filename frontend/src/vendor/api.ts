import {inject, InjectionKey} from "vue";
import {useAsyncState} from "@vueuse/core";
import axios, {AxiosRequestConfig, AxiosStatic} from "axios";
import {getAccessToken} from "./auth0.js";
import VueAxios from "vue-axios";

const AxiosPublicKey = Symbol() as InjectionKey<AxiosStatic>;
const AxiosAuthenticatedKey = Symbol() as InjectionKey<AxiosStatic>;

/* Composition API Axios utilities */
export const useInjectAxiosPublic = (): AxiosStatic => inject(AxiosPublicKey);

export const getPublicResource = (
    config: AxiosRequestConfig,
    initialState
) => {
    const axios = useInjectAxiosPublic();
    return useAsyncState(
        () => axios.request(config).then(r => r.data),
        initialState
    );
};

export const useInjectAxiosAuthenticated = (): AxiosStatic => inject(AxiosAuthenticatedKey);

export const getAuthenticatedResource = (
    config: AxiosRequestConfig,
    initialState
) => {
    const axios = useInjectAxiosAuthenticated();
    return useAsyncState(
        () => axios.request(config).then(r => r.data),
        initialState
    );
};

export const setupApi = (app) => {
    const axiosPublic = axios.create({
        baseURL: '/api',
        headers: {
            "Content-Type": "application/json",
        }
    });

    const axiosAuthenticated = axiosPublic.create({});

    axiosAuthenticated.interceptors.request.use(
        async (config) => {
            const accessToken = await getAccessToken();

            config.headers['Authorization'] = `Bearer ${accessToken}`
            return config
        },
        (error) => {
            return Promise.reject(error)
        }
    );

    app.use(VueAxios, {
        'axiosPublic': axiosPublic,
        'axiosAuthenticated': axiosAuthenticated
    })
    app.provide(AxiosPublicKey, app.config.globalProperties.axiosPublic)
    app.provide(AxiosAuthenticatedKey, app.config.globalProperties.axiosAuthenticated)
};
