<template>
    <div class="row">
        <div class="col-lg-8 pr-2">
            <div class="card">
                <div class="card-body">
                    <transition name="menu-fade" mode="out-in">
                        <div key="if" v-if="selected_menu_category === null">
                            <div class="row">
                                <div
                                    class="card col-md-4 d-inline-block mt-3"
                                    v-for="menu_category in p_menu_data"
                                    :key="menu_category.id"
                                >
                                    <img
                                        class="card-img-top"
                                        :src="`/menuCategory/image/${menu_category.id}`"
                                        :alt="menu_category.name"
                                    >
                                    <div class="card-body">
                                        <span
                                            class="font-weight-bold text-info"
                                        >{{ menu_category.name }}</span>

                                        <p class="text-muted">
                                            {{ menu_category.description }}
                                        </p>

                                        <div class="text-right">
                                            <button
                                                @click="onOrderMenuCategoryButtonClick(menu_category)"
                                                class="btn btn-info btn-sm"
                                            >
                                                Pesan
                                                <i class="fa fa-plus"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div key="else" v-else>
                            <div class="mb-3">
                                <div class="text-right">
                                    <button
                                        class="btn btn-warning"
                                        @click="selected_menu_category = null"
                                    >
                                        <i class="fa fa-arrow-left"></i>
                                        Kembali
                                    </button>
                                </div>
                            </div>

                            <div class="row">
                                <div class="col-md-4">
                                    <img
                                        class="img-detail"
                                        :src="`/menuCategory/image/${selected_menu_category.id}`"
                                        :alt="selected_menu_category.name"
                                    >
                                </div>

                                <div class="col-md-8">
                                    <table class="table table-sm table-striped">
                                        <thead>
                                            <th> Nama </th>
                                            <th class="text-right"> Harga (Rp) </th>
                                            <th class="text-right"> Diskon </th>
                                            <th class="text-center"> Jumlah </th>
                                        </thead>

                                        <tbody>
                                            <tr
                                                v-for="menu_item in selected_menu_category.menu_items"
                                                :key="menu_item.id"
                                            >
                                                <td>{{ menu_item.name }}</td>
                                                <td class="text-right">
                                                    <!-- Real price -->
                                                    {{ number_format(menu_item.outlet_menu_item.price) }}
                                                </td>
                                                <td class="text-right">
                                                    <!-- Discount -->

                                                    {{ percent_format(get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0)) }}
                                                </td>
                                                <td class="text-center">
                                                    <button
                                                        @click="--menu_item.order_quantity"
                                                        class="btn btn-sm btn-danger"
                                                    >
                                                        <i class="fa fa-minus"></i>
                                                    </button>

                                                    <span class="font-weight-bold mx-1">
                                                        <order-quantity
                                                            v-model="menu_item.order_quantity"
                                                        />
                                                    </span>

                                                    <button
                                                        @click="++menu_item.order_quantity"
                                                        class="btn btn-sm btn-success"
                                                    >
                                                        <i class="fa fa-plus"></i>
                                                    </button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </div>
                        </div>
                    </transition>
                </div>
            </div>
        </div>

        <div class="col-lg-4 pl-0">
            <div class="card">
                <div class="card-body">
                    <h1 class="h5 text-info">DAFTAR PESANAN</h1>

                    <div v-if="ordered_menu_items.length > 0">
                        <table class="table table-sm">
                            <thead>
                                <th>Item</th>
                                <th class="text-right">Harga (Rp)</th>
                                <th class="text-center" style="width: 10rem">Jumlah</th>
                                <th class="text-right"> Subtotal (Rp) </th>
                            </thead>

                            <tbody class="table-striped">
                                <tr v-for="menu_item in ordered_menu_items" :key="menu_item.id">
                                    <td>{{ menu_item.name }}</td>
                                    <td class="text-right">
                                        {{ number_format(
                                            menu_item.outlet_menu_item.price * 
                                            (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                        ) }}
                                    </td>
                                    <td class="text-center">
                                        <button
                                            @click="--menu_item.order_quantity"
                                            class="btn btn-sm btn-danger"
                                        >
                                            <i class="fa fa-minus"></i>
                                        </button>

                                        <span class="font-weight-bold mx-1">
                                            <order-quantity v-model="menu_item.order_quantity"/>
                                        </span>

                                        <button
                                            @click="++menu_item.order_quantity"
                                            class="btn btn-sm btn-success"
                                        >
                                            <i class="fa fa-plus"></i>
                                        </button>
                                    </td>

                                    <td class="text-right">
                                        {{ number_format(
                                            menu_item.outlet_menu_item.price *
                                            menu_item.order_quantity *
                                            (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                        ) }}
                                    </td>
                                </tr>
                            </tbody>

                            <tfoot class="text-right">
                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> Service Charge ({{ percent_format(outlet.service_charge) }}) </td>
                                    <td> {{ number_format(this.service_charge) }} </td>
                                </tr>

                                <tr>
                                    <td> </td>
                                    <td> </td>
                                    <td> PPN ({{ percent_format(outlet.pajak_pertambahan_nilai) }}) </td>
                                    <td> {{ number_format(this.tax) }} </td>
                                </tr>
                            </tfoot>
                        </table>

                        <div class="total-price font-weight-bold text-right">
                            TOTAL:
                            <span class="text-danger">Rp. {{ number_format(rounding) }}</span>
                        </div>

                        <div @click="onFinishOrderButtonClick" class="text-right mt-3">
                            <button class="btn btn-primary">
                                Selesaikan Pemesanan
                                <i class="fa fa-check"></i>
                            </button>
                        </div>
                    </div>

                    <div v-else class="alert alert-info">
                        <i class="fa fa-info"></i>
                        Belum terdapat pesanan sama sekali.
                    </div>
                </div>
            </div>
        </div>

        <modal name="order-type" height="auto">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-info"></i>
                    Jenis Pemesanan
                </div>
                <div class="card-body">
                    <h2> Silahkan memilih jenis pemesanan Anda </h2>
                </div>
                <div class="card-footer text-center">
                    <button @click="onChooseOrderTypeModalReturnButtonClick" class="btn btn-warning">
                        <i class="fa fa-arrow-left"></i>
                        Kembali
                    </button>

                    <button
                        @click="onSelectOrderMode('TAKEAWAY')"
                        class="btn btn-success">
                        {{ order_types['TAKEAWAY'] }}
                    </button>
                    <button
                        @click="onSelectOrderMode('DINE_IN')"
                        class="btn btn-primary">
                        {{ order_types['DINE_IN'] }}
                    </button>
                </div>
            </div>
        </modal>

        <modal name="order-confirmation" height="auto" width="800">
            <div class="card">
                <div class="card-header">
                    <i class="fa fa-question"></i>
                    Konfirmasi Pemesanan
                </div>
                <div class="card-body">
                    <table class="table">
                        <thead>
                            <th>Item</th>
                            <th class="text-right">
                                Harga (Rp)
                            </th>
                            <th class="text-right" style="width: 10rem">
                                Jumlah
                            </th>
                            <th class="text-right">
                                Subtotal (Rp)
                            </th>
                        </thead>

                        <tbody>
                            <tr v-for="menu_item in ordered_menu_items" :key="menu_item.id">
                                <td>
                                    {{ menu_item.name }}
                                </td>
                                <td class="text-right">
                                    {{ number_format(menu_item.outlet_menu_item.price) }}
                                </td>
                                
                                <td class="text-right">
                                    <span class="font-weight-bold mx-1">
                                        <order-quantity v-model="menu_item.order_quantity"/>
                                    </span>
                                </td>

                                <td class="text-right">
                                    {{ number_format(
                                        menu_item.outlet_menu_item.price * 
                                        menu_item.order_quantity *
                                        (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                    ) }}
                                </td>
                            </tr>
                        </tbody>
                        
                        <tfoot class="text-right">
                            <tr>
                                <td> </td>
                                <td> </td>
                                <td> Service Charge ({{ percent_format(outlet.service_charge) }}) </td>
                                <td> {{ number_format(this.service_charge) }} </td>
                            </tr>

                            <tr>
                                <td> </td>
                                <td> </td>
                                <td> PPN ({{ percent_format(outlet.pajak_pertambahan_nilai) }}) </td>
                                <td> {{ number_format(this.tax) }} </td>
                            </tr>
                        </tfoot>
                    </table>

                    <h3 class="text-right">
                        <span class="badge badge-info">
                            {{ order_types[order_type] }}
                        </span>
                    </h3>

                    <div class="total-price font-weight-bold text-right">
                        TOTAL:
                        <span class="text-danger">
                            Rp. {{ number_format(rounding) }}
                        </span>
                    </div>
                </div>

                <div class="card-footer text-right">
                    <button @click="onFinishOrderModalReturnButtonClick" class="btn btn-warning">
                        <i class="fa fa-arrow-left"></i>
                        Kembali
                    </button>

                    <button v-if="!is_submitting_sales_invoice" @click="onFinishOrderModalConfirmButtonClick" class="btn btn-primary">
                        Selesaikan Pemesanan
                        <i class="fa fa-check"></i>
                    </button>

                    <button v-else class="btn btn-primary" type="button" disabled>
                        <span class="spinner-border spinner-border-sm" role="status"></span>
                        Mohon tunggu...
                    </button>
                </div>

            </div>
        </modal>

        <div class="invisible">
            <div ref="orderConfirmationStatusAlertContent">
                <p>
                    Silahkan ambil struk nomor pesanan Anda pada printer disamping.
                    <strong> Nomor pesanan Anda adalah </strong>:
                </p>
                <h1 class="text-danger">
                    {{ sales_invoice && sprintf("%04d", sales_invoice.number) }}
                </h1>
            </div>
        </div>
    </div>
</template>

<script>
import { number_format, percent_format } from "../numeral_helpers.js";
import { get } from 'lodash';
import OrderQuantity from "./OrderQuantity.vue";
import order_types from "../order_types";
import { sprintf } from "sprintf-js";

export default {
    props: ["outlet", "menu_data", "submit_url"],

    components: { OrderQuantity },

    data() {
        return {
            p_menu_data: this.menu_data
                .map(menu_category => ({
                    ...menu_category,
                    menu_items: menu_category.menu_items
                        .map(menu_item => ({
                            ...menu_item,
                            order_quantity: 0
                        }))
                }))
                .filter(menu_category => menu_category.menu_items.length > 0 ),

            selected_menu_category: null,
            order_type: null,

            is_submitting_sales_invoice: false,
            sales_invoice: null,
        };
    },

    computed: {
        order_types() {
            return order_types
        },

        ordered_menu_items() {
            let menu_items = [];

            this.p_menu_data.forEach(menu_category => {
                menu_category.menu_items
                    .filter(menu_item => menu_item.order_quantity > 0)
                    .forEach(menu_item => {
                        menu_items.push(menu_item);
                    });
            });

            return menu_items;
        },

        prediscount_pretax_total_price() {
            return this.ordered_menu_items.reduce((prev, cur) => {
                return prev + (cur.outlet_menu_item.price * cur.order_quantity);
            }, 0);
        },

        pretax_total_price() {
            return this.ordered_menu_items.reduce((prev, cur) => {
                return prev + (cur.outlet_menu_item.price * cur.order_quantity * (1 - get(this.outlet.discount_map[cur.outlet_menu_item.id], "percentage", 0)));
            }, 0);
        },

        tax() {
            return this.prediscount_pretax_total_price * this.outlet.pajak_pertambahan_nilai
        },

        service_charge() {
            return this.prediscount_pretax_total_price * this.outlet.service_charge
        },

        total_price() {
            return this.pretax_total_price + (this.tax + this.service_charge)
        },

        rounding() {
            return Math.round(this.total_price / 100) * 100
        },

        form_data() {
            let ordereds = this.ordered_menu_items.map(menu_item => {
                return {
                    id: menu_item.id,
                    quantity: menu_item.order_quantity
                };
            });

            return {
                type: this.order_type,
                menu_items: ordereds,
            };
        }
    },

    methods: {
        get,
        sprintf,
        number_format,
        percent_format,

        onOrderMenuCategoryButtonClick(menu_category) {
            this.selected_menu_category = menu_category;
        },

        onFinishOrderButtonClick() {
            this.$modal.show("order-type");
        },

        onSelectOrderMode(order_type) {
            this.$modal.hide("order-type");
            this.$modal.show("order-confirmation");

            this.order_type = order_type
        },

        onChooseOrderTypeModalReturnButtonClick() {
            this.$modal.hide("order-type");
        },

        onFinishOrderModalReturnButtonClick() {
            this.$modal.show("order-type");
            this.$modal.hide("order-confirmation");
        },

        onFinishOrderModalConfirmButtonClick() {
            this.submitSalesInvoice()
        },

        submitSalesInvoice() {
            this.is_submitting_sales_invoice = true
            $.post(this.submit_url, { token: window.token, ...this.form_data })
                .done(response => {    
                    this.is_submitting_sales_invoice = false
                    this.$modal.hide("order-confirmation");
                    this.error_data = null;

                    this.sales_invoice = response.sales_invoice

                    response.print_requests.forEach(print_request => {
                        $.post(`${this.outlet.print_server_url}/manual_print`, print_request)
                            .done(response => {
                            })
                            .fail((xhr, status, error) => {
                                if (xhr.status === 500 || xhr.status === 0) {
                                    Sentry.captureException({ xhr, status, error })
                                }
                                this.error_data = response.data;
                            });
                    })

                    swal({
                        icon: "success",
                        content: this.$refs.orderConfirmationStatusAlertContent,
                    })

                    this.restoreToInitialState();
                })
                .fail((xhr, status, error) => {
                    this.is_submitting_sales_invoice = false
                    this.$modal.hide("order-confirmation");
                    
                    if (xhr.status === 500 || xhr.status === 0) {
                        Sentry.captureException({ xhr, status, error })
                    }

                    swal({
                        icon: "warning",
                        text: "Terjadi masalah.",
                        dangerMode: true
                    });
                });
        },

        restoreToInitialState() {
            this.selected_menu_category = null;

            this.p_menu_data.forEach(menu_category => {
                menu_category.menu_items.forEach(menu_item => {
                    menu_item.order_quantity = 0;
                });
            });
        }
    }
};
</script>

<style scoped>
img.card-img-top {
    width: 100%;
    height: 200px;
    object-fit: cover;
}

img.img-detail {
    width: 100%;
    height: auto;
}

.menu-fade-enter-active,
.menu-fade-leave-active {
    transition: opacity 0.1s ease;
}
.menu-fade-enter, .menu-fade-leave-to
    /* .menu-fade-leave-active below version 2.1.8 */ {
    opacity: 0;
}

div.total-price {
    font-size: 24pt;
}
</style>
