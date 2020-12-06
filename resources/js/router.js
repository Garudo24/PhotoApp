import Vue from 'vue'
import VueRouter from 'vue-router'

import PhotoList from './pages/PhotoList.vue'
import Login from './pages/Login.vue'

Vue.use(VueRouter)

const routed = [
    {
        path: '/',
        component: PhotoList
    },
    {
        path: '/',
        component: Login
    }
]

const router = new VueRouter({
    routes
})

export default router