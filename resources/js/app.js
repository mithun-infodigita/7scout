
require('./bootstrap');

window.Vue = require('vue').default;

import Vue from 'vue'

import router from "./router/index";
import store from './vuex'
import Vuetify from "../plugins/vuetify";

import localforage from 'localforage'

localforage.config({
    driver: localforage.LOCALSTORAGE,
    storeName: '7scout'
})

Vue.use(require('vue-cookies'))

Vue.use(require('vue-pusher'), {
    api_key: 'd1090739ece925aaf92b',
    options: {
        cluster: 'eu',
        encrypted: true,
    }
});

import InstantSearch from 'vue-instantsearch';

Vue.use(InstantSearch);

Vue.component('app', require('./components/App.vue').default);

import App from './components/App'

store.dispatch('auth/fetchMe').then(() => {
    new Vue({
        vuetify: Vuetify,
        router: router,
        store: store,
        render: h => h(App)
    }).$mount('#app')
})

