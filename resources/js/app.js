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

window.Popper = require('popper.js').default;
window.$ = window.jQuery = require('jquery');



window.JSZip = require( 'jszip' );

let pdfMake = require( 'pdfmake/build/pdfmake' );
let pdfFonts = require('pdfmake/build/vfs_fonts')
pdfMake.vfs = pdfFonts.pdfMake.vfs;

require( 'datatables.net-bs4')();
require( 'datatables.net-buttons-bs4')();

require( 'datatables.net-buttons/js/buttons.colVis.js')(window, window.$);
require( 'datatables.net-buttons/js/buttons.flash.js')(window, window.$);
require( 'datatables.net-buttons/js/buttons.html5.js')(window, window.$);
require( 'datatables.net-buttons/js/buttons.print.js')(window, window.$);


require('bootstrap');
require('datatables.net-bs4/css/dataTables.bootstrap4.css')
require('datatables.net-buttons-bs4/css/buttons.bootstrap4.css')

window.datatable_config = require('./datatable_config.js')
window.numeral = require('./numeral_helpers.js').numeral
window.currency_format = require('./numeral_helpers.js').currency_format

// Add SweetAlert
window.swal = require("sweetalert")

window.app = new Vue({
    el: '#app',
});