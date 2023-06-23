import {inject} from "vue";
import {useAsyncState} from "@vueuse/core";
import axios from "axios";
import {getAccessToken} from "./auth0.js";
import VueAxios from "vue-axios";

const AXIOS_PUBLIC = 'axios-public';
const AXIOS_AUTHENTICATED = 'axios-authenticated';

/* Composition API Axios utilities */
export const getPublicResource = (config, initialState) => useAsyncState(
    inject(AXIOS_PUBLIC).request(config).then(r => r.data),
    initialState
);

export const getAuthenticatedResource = (config, initialState) => useAsyncState(
    inject(AXIOS_AUTHENTICATED).request(config).then(r => r.data),
    initialState
);

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
    app.provide(AXIOS_PUBLIC, app.config.globalProperties.axiosPublic)
    app.provide(AXIOS_AUTHENTICATED, app.config.globalProperties.axiosAuthenticated)
};
