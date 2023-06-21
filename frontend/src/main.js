import {createApp} from 'vue'
import App from './App.vue'
import Oruga from '@oruga-ui/oruga-next'
import {bulmaConfig} from '@oruga-ui/theme-bulma'
import {createRouter, createWebHashHistory} from 'vue-router'
import routes from './routes'

import './scss/style.scss'

const router = createRouter({
    history: createWebHashHistory(),
    routes,
})

createApp(App)
    .use(Oruga, bulmaConfig)
    .use(router)
    .mount('#app')
