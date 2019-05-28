import "../scss/app.scss"
import Vue from 'vue/dist/vue.esm'
import VModal from 'vue-js-modal'

Vue.use(VModal)

// Get CSRF token from the header
window.token = document.head.querySelector('meta[name="csrf-token"]').content

Vue.component("home", require("./components/Home.vue").default)
Vue.component("receipt-printer-index", require("./components/ReceiptPrinterIndex.vue").default)
Vue.component("sales-invoice-confirm", require("./components/SalesInvoiceConfirm.vue").default)
Vue.component("sales-invoice-update-and-confirm", require("./components/SalesInvoiceUpdateAndConfirm.vue").default)
Vue.component("discount-create", require("./components/DiscountCreate.vue").default)
Vue.component("discount-edit", require("./components/DiscountEdit.vue").default)
Vue.component("outlet-create", require("./components/OutletCreate.vue").default)
Vue.component("outlet-edit", require("./components/OutletEdit.vue").default)
Vue.component("outlet-menu-item-create", require("./components/OutletMenuItemCreate.vue").default)
Vue.component("outlet-menu-item-edit", require("./components/OutletMenuItemEdit.vue").default)

// Load cleave.js
require('cleave.js/dist/cleave')

try {
    window.Popper = require('popper.js').default;
    window.$ = window.jQuery = require('jquery');
    require('datatables.net-bs4')()
    require('datatables.net-bs4/css/dataTables.bootstrap4.css')
    
    require('jszip' );
    require('pdfmake' );
    require('datatables.net-buttons-bs4')();
    require('datatables.net-buttons/js/buttons.colVis.js' )();
    require('datatables.net-buttons/js/buttons.flash.js' )();
    require('datatables.net-buttons/js/buttons.html5.js' )();
    require('datatables.net-buttons/js/buttons.print.js' )();

    require('bootstrap');
} catch (e) {}


// Add SweetAlert
window.swal = require("sweetalert")

window.app = new Vue({
    el: '#app',
});