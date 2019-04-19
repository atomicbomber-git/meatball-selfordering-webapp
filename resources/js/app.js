import "../scss/app.scss"
import Vue from 'vue/dist/vue.esm'

Vue.component("home", require("./components/Home.vue").default)

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('bootstrap');
} catch (e) {}

// Add SweetAlert
window.swal = require("sweetalert")

window.app = new Vue({
    el: '#app',
});