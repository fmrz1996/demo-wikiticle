window.Vue = require('vue');
window.axios = require('axios');

import VueRouter from 'vue-router'
Vue.use(VueRouter)

const optionsPB = {
  color: '#bffaf3',
  failedColor: '#874b4b',
  thickness: '5px',
  transition: {
    speed: '0.8s',
    opacity: '0.8s',
    termination: 800
  },
  autoRevert: true,
  location: 'top',
  inverse: false
}

import VueProgressBar from 'vue-progressbar'
Vue.use(VueProgressBar, optionsPB)

import swal from 'sweetalert2'
window.swal = swal;

const toast = swal.mixin({
  toast: true,
  position: 'top-end',
  showConfirmButton: false,
  timer: 3000
});

window.toast = toast;

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
