import {createApp} from 'vue'
import App from './App.vue'
import Oruga from '@oruga-ui/oruga-next'
import {bulmaConfig} from '@oruga-ui/theme-bulma'
import {createRouter, createWebHashHistory} from 'vue-router'
import routes from './routes'
import auth0 from './auth0.js';

import './scss/style.scss'
import {setupApi} from "./api.js";

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})

const app = createApp(App)

app.use(Oruga, bulmaConfig)
app.use(auth0)
app.use(router)
setupApi(app)

app.mount('#app')
