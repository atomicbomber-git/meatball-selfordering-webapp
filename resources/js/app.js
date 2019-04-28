import "../scss/app.scss"
import Vue from 'vue/dist/vue.esm'
import VModal from 'vue-js-modal'

Vue.use(VModal)

// Get CSRF token from the header
window.token = document.head.querySelector('meta[name="csrf-token"]').content

Vue.component("home", require("./components/Home.vue").default)
Vue.component("receipt-printer-index", require("./components/ReceiptPrinterIndex.vue").default)
Vue.component("sales-invoice-update-and-confirm", require("./components/SalesInvoiceUpdateAndConfirm.vue").default)

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('datatables.net-bs4')()
    require('datatables.net-bs4/css/dataTables.bootstrap4.css')

    require('bootstrap');
} catch (e) {}


// Add SweetAlert
window.swal = require("sweetalert")

window.app = new Vue({
    el: '#app',
});