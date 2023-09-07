import {createApp} from 'vue'
import App from './App.vue'
import {createRouter, createWebHashHistory} from 'vue-router'
import routes from './routes'
import auth0 from './vendor/auth0';
import {setupApi} from "./vendor/api";

import './scss/style.scss';
import 'bootstrap';
import {installCurrentVueInstance} from "~/vendor/vueInstance.ts";

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})

const app = createApp(App)

app.use(auth0)
app.use(router)
setupApi(app)
installCurrentVueInstance(app)

app.mount('#app')
