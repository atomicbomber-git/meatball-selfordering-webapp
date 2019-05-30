<template>
    <div>
        <div class="t-a:r m-y:2">
            <button @click="addItem" class="btn btn-info btn-sm">
                Tambah Item
            </button>
        </div>

        <table class="table table-sm table-striped">
            <thead class="thead thead-dark table-bordered">
                <tr>
                    <th>Nama Item</th>
                    <th class="t-a:c"> Jumlah </th>
                    <th class="t-a:r"> Harga Satuan </th>
                    <th class="t-a:r"> Diskon </th>
                    <th class="t-a:r"> </th>
                    <th class="t-a:r"> Kendali </th>
                </tr>
            </thead>

            <tbody class="table-bordered">
                <tr
                    v-for="item in added_items"
                    :key="item.id">
                    
                    <!-- Nama Item -->
                    <td> 
                        <multiselect
                            track-by="id"
                            :custom-label="om_item => `${om_item.menu_item.name} - Rp. ${currency_format(om_item.price)}`"
                            :options="item_options"
                            v-model="item.outlet_menu_item"
                            />
                    </td>

                    <!-- Jumlah -->
                    <td class="t-a:c">
                        <button
                            @click="--item.quantity"
                            class="btn btn-sm btn-danger">
                            <i class="fa fa-minus"></i>
                        </button>

                        <order-quantity
                            class=m-x:1
                            v-model="item.quantity"
                            >
                        </order-quantity>

                        <button
                            @click="++item.quantity"
                            class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>

                    <!-- Harga Satuan -->
                    <td class="t-a:r"> Rp. {{ currency_format(get(item, "outlet_menu_item.price", 0)) }} </td>

                    <td class="t-a:r">
                        {{ percent_format( get(sales_invoice.discount_map[item.outlet_menu_item.id], "percentage", 0) ) }}
                    </td>

                    <td class="t-a:r">
                        Rp. {{
                            currency_format(
                                item.quantity *
                                get(item.outlet_menu_item, "price", 0) *
                                (1 - get(sales_invoice.discount_map[item.outlet_menu_item.id], "percentage", 0))
                            )
                        }}
                    </td>
                    <td class="t-a:r">
                        <button @click="removeItem(item)" class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </td>
                </tr>
            </tbody>

            <tfoot class="table-borderless">
                <tr>
                    <th>  </th>
                    <th>  </th>
                    <th>  </th>
                    <th class="t-a:r"> Sub Total </th>
                    <th class="t-a:r"> {{ currency_format(pretax_sum) }} </th>
                    <th></th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Tax {{ percent_format(sales_invoice.outlet.pajak_pertambahan_nilai) }} </th>
                    <th class="t-a:r"> {{ currency_format(tax) }} </th>
                    <th></th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Service Charge {{ percent_format(sales_invoice.outlet.service_charge) }} </th>
                    <th class="t-a:r"> {{ currency_format(service_charge) }} </th>
                    <th></th>
                </tr>


                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Diskon Khusus {{ percent_format(special_discount_percentage) }} </th>
                    <th class="t-a:r">
                        -{{ currency_format(special_discount_total) }}
                    </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Total </th>
                    <th class="t-a:r"> {{ currency_format(total) }} </th>
                    <th></th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Cash </th>
                    <th class="t-a:r"> {{ currency_format(cash) }} </th>
                    <th></th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Rounding </th>
                    <th class="t-a:r"> {{ currency_format(rounding) }} </th>
                    <th></th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="t-a:r"> Total Change </th>
                    <th class="t-a:r"> {{ currency_format(this.total_change) }} </th>
                    <th></th>
                </tr>
            </tfoot>
        </table>

        <div class="t-a:r">
            <div class="custom-control custom-radio custom-control-inline">
                <input v-model="p_sales_invoice.type" value="DINE_IN" class="custom-control-input" type="radio" id="order_type_dine_in" name="order_type">
                <label class="custom-control-label" for="order_type_dine_in"> {{ order_types["DINE_IN"] }} </label>
            </div>

            <div class="custom-control custom-radio custom-control-inline">
                <input v-model="p_sales_invoice.type" value="TAKEAWAY" class="custom-control-input" type="radio" id="order_type_takeaway" name="order_type">
                <label class="custom-control-label" for="order_type_takeaway"> {{ order_types["TAKEAWAY"] }} </label>
            </div>
        </div>

        <div class="t-a:r m-t:5">
            <table class="table-sm table-borderless d-inline-block" style="witdh: 300px">
                <tbody>
                    <tr>
                        <td> Jumlah Dibayar </td>
                        <td>
                            <vue-cleave
                                class="form-control t-a:r"
                                :class="{'is-invalid': get(this.error_data, 'errors.cash', false)}"
                                v-model.number="cash"
                                placeholder="Jumlah"
                                :options="{ numeral: true, numeralDecimalScale: 2, stripLeadingZeroes: false, numeralDecimalMark: ',', delimiter: '.' }">
                            </vue-cleave>
                            <label class='error' v-if="get(this.error_data, 'errors.cash', false)">{{ get(this.error_data, 'errors.cash', false) }}</label>
                        </td>
                    </tr>

                    <tr>
                        <td> Diskon Khusus </td>
                        <td>
                            <multiselect
                                placheholder="Diskon Khusus"
                                selectLabel=""
                                deselectLabel=""
                                :custom-label="val => percent_format(val)"
                                :options="special_discount_percentages"
                                v-model="special_discount_percentage"
                                :preselect-first="true"
                                :allow-empty="false"
                            />
                        </td>
                    </tr>

                    <tr>
                        <td> Jumlah yang Harus Dibayar </td>
                        <td class="text-danger">
                            Rp. {{ currency_format(this.rounding) }}
                        </td>
                    </tr>

                    <tr>
                        <td> Jumlah Kembalian </td>
                        <td class="text-danger">
                            Rp. {{ this.total_change < 0 ? "-,00" : currency_format(this.total_change) }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div @click="confirmTransaction" class="t-a:r m-t:3">
            <button class="btn btn-primary">
                Konfirmasi Transaksi
            </button>
        </div>

        <div class="invisible">
            <div ref="auth_form" class="text-left">
                <div class='form-group'>
                    <label for='password'> Kata Sandi Supervisor: </label>
                    <input
                        v-model='password'
                        class='form-control'
                        :class="{'is-invalid': get(this.error_data, 'errors.password', false)}"
                        type='password' id='password' placeholder='Kata Sandi'>
                    <label class='error' v-if="get(this.error_data, 'errors.password', false)">{{ get(this.error_data, 'errors.password', false) }}</label>
                </div>

                <div v-if="get(error_data, 'message', false)" class="alert alert-danger">
                    <i class="fa fa-warning"></i>
                    {{ get(error_data, "message") }}
                </div>
            </div>
        </div>

    </div>
</template>

<script>

import order_types from '../order_types.js'
import { currency_format, percent_format } from "../numeral_helpers"
import special_discount_percentages from "../special_discount_percentages"
import { get } from "lodash"
import OrderQuantity from "./OrderQuantity.vue"
import { Multiselect } from "vue-multiselect"
import VueCleave from "vue-cleave-component"

export default {
    props: ["outlet_menu_items", "sales_invoice", "submit_url", "redirect_url"],
    components: { OrderQuantity, Multiselect, VueCleave },

    data() {
        return {
            cash: null,
            password: null,
            special_discount_percentage: null,

            p_sales_invoice: {
                ...this.sales_invoice,
                planned_sales_invoice_items: [ ...this.sales_invoice.planned_sales_invoice_items ],
            },

            items: this.outlet_menu_items.map(om_item => {
                let psi_item = this.sales_invoice.planned_sales_invoice_items
                    .find(item => item.menu_item_id === om_item.menu_item_id)

                if (psi_item !== undefined) {
                    return { outlet_menu_item: om_item, quantity: psi_item.quantity }
                }
                else {
                    return { outlet_menu_item: om_item, quantity: 0 }
                }
            }),

            error_data: null,
        }
    },

    computed: {
        special_discount_percentages() {
            return special_discount_percentages
        },

        order_types() {
            return order_types
        },

        added_items() {
            return this.items
                .sort((a, b) => a.outlet_menu_item.menu_item.name.localeCompare(b.outlet_menu_item.menu_item.name))
                .filter(({ quantity }) => quantity > 0)
        },

        item_options() {
            return this.items
                .filter(om_item => {
                    if (om_item.outlet_menu_item === null) {
                        return false
                    }

                    return this.added_items
                        .filter(aom_item => aom_item.outlet_menu_item !== null)
                        .find(aom_item => aom_item.outlet_menu_item.menu_item_id === om_item.outlet_menu_item.menu_item_id)
                            === undefined
                })
                .map(om_item => ({...om_item.outlet_menu_item}))
        },

        total_price() {
            return this.added_items.reduce((prev, curr) => {
                return prev + (curr.quantity * get(curr, "outlet_menu_item.price", 0))
            }, 0)
        },

        form_data() {
            return {
                menu_items: this.added_items
                    .filter(added_item => added_item.outlet_menu_item !== null)
                    .map(added_item => ({
                        id: added_item.outlet_menu_item.menu_item_id,
                        quantity: added_item.quantity,
                    })),

                cash: this.cash,
                type: this.p_sales_invoice.type,
                special_discount: this.special_discount_percentage,
                password: this.password,
            }
        },

        undiscounted_sales_invoice_items() {
            return this.added_items
                .filter(added_item => added_item.outlet_menu_item !== null)
                .filter(item => get(this.sales_invoice.discount_map[item.outlet_menu_item.id], "percentage", 0) == 0) 
        },

        undiscounted_total() {
            return this.undiscounted_sales_invoice_items.reduce((prev, curr) => {
                return prev + (curr.quantity * curr.outlet_menu_item.price)
            }, 0)
        },

        pretax_sum() {
            return this.added_items
                .filter(added_item => added_item.outlet_menu_item !== null)
                .reduce((prev, curr) => {
                    return prev + (curr.quantity * curr.outlet_menu_item.price *
                        (1 - get(this.sales_invoice.discount_map[curr.outlet_menu_item.id], "percentage", 0)))
                }, 0)
        },

        prediscount_pretax_sum() {
            return this.added_items
                .filter(added_item => added_item.outlet_menu_item !== null)
                .reduce((prev, curr) => {
                    return prev + (curr.quantity * curr.outlet_menu_item.price)
                }, 0)
        },

        tax() {
            return this.prediscount_pretax_sum * this.sales_invoice.outlet.pajak_pertambahan_nilai
        },

        service_charge() {
            return this.prediscount_pretax_sum * this.sales_invoice.outlet.service_charge
        },

        special_discount_total() {
            return this.undiscounted_total * this.special_discount_percentage
        },

        total() {
            return this.pretax_sum + (this.tax + this.service_charge - this.special_discount_total)
        },

        rounding() {
            return Math.round(this.total / 100) * 100
        },

        total_change() {
            return this.cash - this.rounding
        }
    },

    methods: {
        get,
        currency_format,
        percent_format,

        addItem() {
            this.items.push({ outlet_menu_item: null, quantity: 1 })
        },

        removeItem(item) {
            item.outlet_menu_item = null
            item.quantity = 0
        },

        confirmTransaction() {
            swal({
                icon: "warning",
                content: this.$refs.auth_form,
                closeModal: false,
                closeOnClickOutside: false,
                buttons: ["Kembali", "Konfirmasi"]
            })
            .then(will_submit => {
                if (will_submit) {
                    $.post(this.submit_url, {token: window.token, ...this.form_data})
                        .done(response => {
                            this.error_data = null;

                            response.forEach(print_request_data => {
                                $.post(`${this.sales_invoice.outlet.print_server_url}/manual_print`, print_request_data)
                                    .done(response => {
                                        swal({ icon: 'success', text: 'Konfirmasi Berhasil' })
                                            .then(is_ok => { window.location.replace(this.redirect_url) })
                                    })
                                    .fail((xhr, status, error) => {
                                        if (xhr.status === 0 || xhr.status === 500) {
                                            Sentry.captureException({ xhr, status, error });
                                        }
                                    })
                            });
                        })
                        .fail((xhr, status, error) => {
                            let response = xhr.responseJSON;
                            this.error_data = response.data;
                            this.confirmTransaction()
                        });
                }
            })
        },
    },
};
</script>

<style>
</style>
