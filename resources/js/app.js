window.Vue = require('vue');
window.axios = require('axios');

import VueRouter from 'vue-router'
Vue.use(VueRouter)

let routes = [
  { path: '/articles', component: require('./components/Articles.vue').default },
  { path: '/users', component: require('./components/Users.vue').default }
];

const router = new VueRouter({
  mode: 'history',
  routes,
  linkActiveClass: 'active'
});
const app = new Vue({
    el: '#app',
    router
});
