import Vue from 'vue'
import router from './router'
import App from './App.vue'
import './bootstrap'

new Vue({
    el: '#app',
    router,
    store,
    components: { App },
    template: '<App />'
})