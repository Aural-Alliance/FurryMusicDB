import {inject, InjectionKey} from "vue";
import axios, {AxiosStatic} from "axios";
import VueAxios from "vue-axios";

const AxiosKey = Symbol() as InjectionKey<AxiosStatic>;

/* Composition API Axios utilities */
export const useInjectAxios = (): AxiosStatic => inject(AxiosKey);

export const setupApi = (app) => {
    const axiosInstance = axios.create({
        baseURL: '/api',
        headers: {
            "Content-Type": "application/json",
        }
    });

    app.use(VueAxios, axiosInstance);
    app.provide(AxiosKey, axiosInstance);
};
