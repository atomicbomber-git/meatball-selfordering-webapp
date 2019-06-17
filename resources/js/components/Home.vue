<template>
    <div>
        <transition name="menu-fade" mode="out-in">
            <div key="if" v-if="!is_in_order_mode">
                <div class="card" style="margin-top: 700px">
                    <div class="card-body">
                        <button
                            @click="is_in_order_mode = true"
                            class="btn btn-primary btn-lg btn-block"
                        >Pesan</button>
                    </div>
                </div>
            </div>

            <div key="else" v-else>
                <div class="row" style="margin-top: 100px">
                    <div class="col-lg-9 pr-2">
                        <div class="card">
                            <div class="card-body">
                                <div class="t-a:r m-b:3">
                                    <button
                                        class="btn btn-warning"
                                        @click="is_in_order_mode = false"
                                    >
                                        <i class="fa fa-arrow-left"></i>
                                        Back
                                    </button>
                                </div>

                                <div class="row">
                                    <div class="col-md-6 pr-1">
                                        <table class="table table-sm table-striped">
                                            <tbody>
                                                <template
                                                    v-for="(menu_category, index) in first_column_menu_categories"
                                                >
                                                    <tr
                                                        v-if="index !== 0"
                                                        :key="'space_' + menu_category.id"
                                                        style="height: 30px"
                                                    ></tr>

                                                    <tr
                                                        class="table-primary"
                                                        :key="'category_' + menu_category.id"
                                                    >
                                                        <th>{{ menu_category.name }}</th>
                                                        <th class="t-a:r">Rp</th>
                                                        <th class="t-a:r">Diskon</th>
                                                        <th class="t-a:c">Jumlah</th>
                                                    </tr>

                                                    <tr
                                                        v-for="menu_item in menu_category.menu_items"
                                                        :key="menu_item.id"
                                                    >
                                                        <td
                                                            style="max-width: 150px"
                                                        >{{ menu_item.name }}</td>
                                                        <td class="t-a:r">
                                                            <!-- Real price -->
                                                            {{ number_format(menu_item.outlet_menu_item.price) }}
                                                        </td>
                                                        <td class="t-a:r">
                                                            <!-- Discount -->
                                                            {{ percent_format(get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0)) }}
                                                        </td>
                                                        <td class="t-a:c">
                                                            <button
                                                                @click="--menu_item.order_quantity"
                                                                class="btn btn-sm btn-danger"
                                                            >
                                                                <i class="fa fa-minus"></i>
                                                            </button>

                                                            <span class="font-weight-bold m-x:.5">
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
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>

                                    <div class="col-md-6 pl-1">
                                        <table class="table table-sm table-striped">
                                            <tbody>
                                                <template
                                                    v-for="(menu_category, i) in second_column_menu_categories"
                                                >
                                                    <tr
                                                        v-if="i !== 0"
                                                        :key="'space_' + menu_category.id"
                                                        style="height: 30px"
                                                    ></tr>

                                                    <tr
                                                        style="max-width: 150px"
                                                        class="table-primary"
                                                        :key="'category_' + menu_category.id"
                                                    >
                                                        <th>{{ menu_category.name }}</th>

                                                        <th class="t-a:r">Rp</th>
                                                        <th class="t-a:r">Diskon</th>
                                                        <th class="t-a:c">Jumlah</th>
                                                    </tr>

                                                    <tr
                                                        v-for="menu_item in menu_category.menu_items"
                                                        :key="menu_item.id"
                                                    >
                                                        <td>{{ menu_item.name }}</td>
                                                        <td class="t-a:r">
                                                            <!-- Real price -->
                                                            {{ number_format(menu_item.outlet_menu_item.price) }}
                                                        </td>
                                                        <td class="t-a:r">
                                                            <!-- Discount -->
                                                            {{ percent_format(get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0)) }}
                                                        </td>
                                                        <td class="t-a:c">
                                                            <button
                                                                @click="--menu_item.order_quantity"
                                                                class="btn btn-sm btn-danger"
                                                            >
                                                                <i class="fa fa-minus"></i>
                                                            </button>

                                                            <span class="font-weight-bold m-x:.5">
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
                                                </template>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="col-lg-3 pl-0">
                        <div class="card">
                            <div class="card-body">
                                <h1 class="h5 text-info">DAFTAR PESANAN</h1>

                                <div v-if="ordered_menu_items.length > 0">
                                    <table class="table table-sm">
                                        <tbody class="table-striped">
                                            <tr
                                                v-for="menu_item in ordered_menu_items"
                                                :key="menu_item.id"
                                            >
                                                <td colspan="3">
                                                    {{ menu_item.name }}
                                                    <br>
                                                    {{ menu_item.order_quantity }}x {{ number_format(
                                                    menu_item.outlet_menu_item.price *
                                                    (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                                    ) }}
                                                </td>

                                                <td class="t-a:r" style="vertical-align: bottom !important">
                                                    {{ number_format(
                                                    menu_item.outlet_menu_item.price *
                                                    menu_item.order_quantity *
                                                    (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                                    ) }}
                                                </td>
                                            </tr>
                                        </tbody>

                                        <tfoot class="t-a:r">
                                            <tr>
                                                <td></td>
                                                <td class="t-a:r"> Service Charge </td>
                                                <td class="t-a:r"> ({{ percent_format(outlet.service_charge) }})</td>
                                                <td>{{ number_format(this.service_charge) }}</td>
                                            </tr>

                                            <tr>
                                                <td></td>
                                                <td class="t-a:r"> PPN </td>
                                                <td class="t-a:r"> ({{ percent_format(outlet.pajak_pertambahan_nilai) }})</td>
                                                <td>{{ number_format(this.tax) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>

                                    <div class="total-price font-weight-bold t-a:r">
                                        TOTAL:
                                        <span
                                            class="text-danger"
                                        >Rp. {{ number_format(rounding) }}</span>
                                    </div>

                                    <div @click="onFinishOrderButtonClick" class="t-a:r m-t:3">
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
                                <h2>Silahkan memilih jenis pemesanan Anda</h2>
                            </div>
                            <div class="card-footer t-a:r">
                                <button
                                    @click="onChooseOrderTypeModalReturnButtonClick"
                                    class="btn btn-warning"
                                >
                                    <i class="fa fa-arrow-left"></i>
                                    Back
                                </button>

                                <button
                                    @click="onSelectOrderMode('TAKEAWAY')"
                                    class="btn btn-success"
                                >{{ order_types['TAKEAWAY'] }}</button>
                                <button
                                    @click="onSelectOrderMode('DINE_IN')"
                                    class="btn btn-primary"
                                >{{ order_types['DINE_IN'] }}</button>
                            </div>
                        </div>
                    </modal>

                    <modal name="order-confirmation" height="auto" width="800">
                        <div class="card">
                            <div class="card-header">
                                KONFIRMASI PEMESANAN
                            </div>
                            <div class="card-body">
                                <table class="table table-sm table-striped">
                                    <thead>
                                        <th>Item</th>
                                        <th class="t-a:r">Rp</th>
                                        <th class="t-a:r" style="width: 10rem">Jumlah</th>
                                        <th class="t-a:r">Subtotal (Rp)</th>
                                    </thead>

                                    <tbody>
                                        <tr
                                            v-for="menu_item in ordered_menu_items"
                                            :key="menu_item.id"
                                        >
                                            <td>{{ menu_item.name }}</td>
                                            <td
                                                class="t-a:r"
                                            >{{ number_format(menu_item.outlet_menu_item.price) }}</td>

                                            <td class="t-a:r">
                                                <span class="font-weight-bold m-x:.5">
                                                    <order-quantity
                                                        v-model="menu_item.order_quantity"
                                                    />
                                                </span>
                                            </td>

                                            <td class="t-a:r">
                                                {{ number_format(
                                                menu_item.outlet_menu_item.price *
                                                menu_item.order_quantity *
                                                (1 - get(outlet.discount_map[menu_item.outlet_menu_item.id], "percentage", 0))
                                                ) }}
                                            </td>
                                        </tr>
                                    </tbody>

                                    <tfoot class="t-a:r">
                                        <tr>
                                            <td colspan="3">Service Charge ({{ percent_format(outlet.service_charge) }})</td>
                                            <td>{{ number_format(this.service_charge) }}</td>
                                        </tr>

                                        <tr>
                                            <td colspan="3">PPN ({{ percent_format(outlet.pajak_pertambahan_nilai) }})</td>
                                            <td>{{ number_format(this.tax) }}</td>
                                        </tr>
                                    </tfoot>
                                </table>

                                <h3 class="t-a:r">
                                    <span class="badge badge-info">{{ order_types[order_type] }}</span>
                                </h3>

                                <div class="total-price font-weight-bold t-a:r">
                                    TOTAL:
                                    <span
                                        class="text-danger"
                                    >Rp. {{ number_format(rounding) }}</span>
                                </div>

                                <div class="alert alert-danger m-t:5">
                                    <i class="fa fa-warning"></i>
                                    Silahkan cek pesanan anda dg teliti sekali lagi. Makanan yg sdh dipesan tidak bisa dibatalkan ataupun ditukar.
                                </div>
                            </div>

                            <div class="card-footer t-a:r">
                                <button
                                    @click="onFinishOrderModalReturnButtonClick"
                                    class="btn btn-warning"
                                >
                                    <i class="fa fa-arrow-left"></i>
                                    Back
                                </button>

                                <button
                                    v-if="!is_submitting_sales_invoice"
                                    @click="onFinishOrderModalConfirmButtonClick"
                                    class="btn btn-primary"
                                >
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
                                <strong>Nomor pesanan Anda adalah</strong>:
                            </p>
                            <h1
                                class="text-danger"
                            >{{ sales_invoice && sprintf("%04d", sales_invoice.number) }}</h1>
                        </div>
                    </div>
                </div>
            </div>
        </transition>
    </div>
</template>

<script>
import { number_format, percent_format } from "../numeral_helpers.js";
import { get, partition } from "lodash";
import OrderQuantity from "./OrderQuantity.vue";
import order_types from "../order_types";
import { sprintf } from "sprintf-js";

export default {
    props: ["outlet", "menu_data", "submit_url"],

    components: { OrderQuantity },

    data() {
        return {
            p_menu_data: this.menu_data
                .map((menu_category, index) => ({
                    ...menu_category,
                    index: index,
                    menu_items: menu_category.menu_items.map(menu_item => ({
                        ...menu_item,
                        order_quantity: 0
                    }))
                }))
                .filter(menu_category => menu_category.menu_items.length > 0),

            selected_menu_category: null,

            is_in_order_mode: false,
            order_type: null,

            is_submitting_sales_invoice: false,
            sales_invoice: null
        };
    },

    computed: {
        partitioned_menu_categories() {
            return partition(this.p_menu_data, menu_category => {
                return menu_category.index < this.p_menu_data.length / 2;
            });
        },

        first_column_menu_categories() {
            return this.partitioned_menu_categories[0];
        },

        second_column_menu_categories() {
            return this.partitioned_menu_categories[1];
        },

        order_types() {
            return order_types;
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
                return prev + cur.outlet_menu_item.price * cur.order_quantity;
            }, 0);
        },

        pretax_total_price() {
            return this.ordered_menu_items.reduce((prev, cur) => {
                return (
                    prev +
                    cur.outlet_menu_item.price *
                        cur.order_quantity *
                        (1 -
                            get(
                                this.outlet.discount_map[
                                    cur.outlet_menu_item.id
                                ],
                                "percentage",
                                0
                            ))
                );
            }, 0);
        },

        tax() {
            return (
                this.prediscount_pretax_total_price *
                this.outlet.pajak_pertambahan_nilai
            );
        },

        service_charge() {
            return (
                this.prediscount_pretax_total_price * this.outlet.service_charge
            );
        },

        total_price() {
            return this.pretax_total_price + (this.tax + this.service_charge);
        },

        rounding() {
            return Math.round(this.total_price / 100) * 100;
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
                menu_items: ordereds
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

            this.order_type = order_type;
        },

        onChooseOrderTypeModalReturnButtonClick() {
            this.$modal.hide("order-type");
        },

        onFinishOrderModalReturnButtonClick() {
            this.$modal.show("order-type");
            this.$modal.hide("order-confirmation");
        },

        onFinishOrderModalConfirmButtonClick() {
            this.submitSalesInvoice();
        },

        submitSalesInvoice() {
            this.is_submitting_sales_invoice = true;
            $.post(this.submit_url, { token: window.token, ...this.form_data })
                .done(response => {
                    this.is_submitting_sales_invoice = false;
                    this.$modal.hide("order-confirmation");
                    this.error_data = null;

                    this.sales_invoice = response.sales_invoice;

                    response.print_requests.forEach(print_request => {
                        $.post(
                            `${this.outlet.print_server_url}/manual_print`,
                            print_request
                        )
                            .done(response => {})
                            .fail((xhr, status, error) => {
                                if (xhr.status === 500 || xhr.status === 0) {
                                    Sentry.captureException({
                                        xhr,
                                        status,
                                        error
                                    });
                                }
                                this.error_data = response.data;
                            });
                    });

                    swal({
                        icon: "success",
                        content: this.$refs.orderConfirmationStatusAlertContent
                    });

                    this.restoreToInitialState();
                })
                .fail((xhr, status, error) => {
                    this.is_submitting_sales_invoice = false;
                    this.$modal.hide("order-confirmation");

                    if (xhr.status === 500 || xhr.status === 0) {
                        Sentry.captureException({ xhr, status, error });
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
