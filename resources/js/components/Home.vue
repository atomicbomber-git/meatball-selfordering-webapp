<template>
    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body">
                    <transition name="menu-fade" mode="out-in">
                        <div key="if" v-if="selected_menu_category === null">
                            <div class="row d-flex justify-content-left">
                                <div
                                    class="card d-inline-block mt-3 mr-3"
                                    style="width: 200px"
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

                                        <p
                                            class="text-muted"
                                        >Lorem ipsum dolor, sit amet consectetur adipisicing elit. Quidem saepe ab, molestiae obcaecati nisi praesentium repellat.</p>

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
                                    <table class="table table-striped">
                                        <thead>
                                            <th>Nama</th>
                                            <th>Harga</th>
                                            <th>Jumlah Pemesanan</th>
                                        </thead>

                                        <tbody>
                                            <tr
                                                v-for="menu_item in selected_menu_category.menu_items"
                                                :key="menu_item.id"
                                            >
                                                <td>{{ menu_item.name }}</td>
                                                <td>Rp. {{ number_format(menu_item.price) }}</td>
                                                <td>
                                                    <button
                                                        @click="--menu_item.order_quantity"
                                                        class="btn btn-sm btn-danger"
                                                    >
                                                        <i class="fa fa-minus"></i>
                                                    </button>

                                                    <span class="font-weight-bold mx-3">
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

        <div class="col-lg-4">
            <div class="card">
                <div class="card-body">
                    <h1 class="h5 text-info">DAFTAR PESANAN</h1>

                    <div v-if="ordered_menu_items.length > 0">
                        <table class="table table-sm table-striped">
                            <thead>
                                <th>Item</th>
                                <th>Harga (Rp.)</th>
                                <th style="width: 10rem">Jumlah</th>
                                <th class="text-right"> Subtotal (Rp.) </th>
                            </thead>

                            <tbody>
                                <tr v-for="menu_item in ordered_menu_items" :key="menu_item.id">
                                    <td>{{ menu_item.name }}</td>
                                    <td>{{ number_format(menu_item.price) }}</td>
                                    <td>
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

                                    <td
                                        class="text-right"
                                    >{{ number_format(menu_item.price * menu_item.order_quantity) }}</td>
                                </tr>
                            </tbody>
                        </table>

                        <div class="total-price font-weight-bold text-right">
                            TOTAL:
                            <span class="text-danger">Rp. {{ number_format(total_price) }}</span>
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
                            <th>Harga (Rp.)</th>
                            <th style="width: 10rem text-right">Jumlah</th>
                            <th class="text-right">Subtotal (Rp.)</th>
                        </thead>

                        <tbody>
                            <tr v-for="menu_item in ordered_menu_items" :key="menu_item.id">
                                <td>{{ menu_item.name }}</td>
                                <td>{{ number_format(menu_item.price) }}</td>
                                
                                <td class="text-right">
                                    <span class="font-weight-bold mx-1">
                                        <order-quantity v-model="menu_item.order_quantity"/>
                                    </span>
                                </td>

                                <td
                                    class="text-right"
                                >{{ number_format(menu_item.price * menu_item.order_quantity) }}</td>
                            </tr>
                        </tbody>
                    </table>

                    <h3 class="text-right">
                        <span class="badge badge-info">
                            {{ order_types[order_type] }}
                        </span>
                    </h3>

                    <div class="total-price font-weight-bold text-right">
                        TOTAL:
                        <span class="text-danger">Rp. {{ number_format(total_price) }}</span>
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
                    {{ sales_invoice && sales_invoice.number }}
                </h1>
            </div>
        </div>
    </div>
</template>

<script>
import { number_format } from "../numeral_helpers.js";
import OrderQuantity from "./OrderQuantity.vue";
import  order_types from "../order_types";

export default {
    props: ["menu_data", "submit_url"],

    components: { OrderQuantity },

    data() {
        return {
            p_menu_data: this.menu_data.map(menu_categories => ({
                ...menu_categories,
                menu_items: menu_categories.menu_items.map(menu_item => ({
                    ...menu_item,
                    order_quantity: 0
                }))
            })),

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

        total_price() {
            return this.ordered_menu_items.reduce((prev, cur) => {
                return prev + cur.price * cur.order_quantity;
            }, 0);
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
        number_format,

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

                    this.sales_invoice = JSON.parse(response)

                    swal({
                        icon: "success",
                        content: this.$refs.orderConfirmationStatusAlertContent,
                    })

                    this.restoreToInitialState();
                })
                .fail((xhr, status, error) => {
                    this.is_submitting_sales_invoice = false
                    this.$modal.hide("order-confirmation");
                    
                    if (xhr.status === 500) {
                        Sentry.captureException(xhr)
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
    width: 200px;
    height: 200px;
    object-fit: cover;
}

img.img-detail {
    width: 100%;
    height: auto;
    /* width: auto;
        height: 480px; */
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
