import {createApp} from 'vue'
import App from './App.vue'
import {createRouter, createWebHashHistory} from 'vue-router'
import routes from './routes'
import auth0 from './auth0';
import {setupApi} from "./api";

import './scss/style.scss';
import 'bootstrap';

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})

const app = createApp(App)

app.use(auth0)
app.use(router)
setupApi(app)

app.mount('#app')
