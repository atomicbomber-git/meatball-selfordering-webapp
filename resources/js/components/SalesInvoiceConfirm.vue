<template>
    <div>
        <table class="table table-sm table-striped">
            <thead class="thead thead-dark table-bordered">
                <tr>
                    <th> Item </th>
                    <th class="text-right"> Jumlah </th>
                    <th class="text-right"> Harga Satuan </th>
                    <th class="text-right"> Diskon Item </th>
                    <th class="text-right"> </th>
                </tr>
            </thead>
            <tbody class="table-bordered">
                <tr v-for="planned_sales_invoice_item in sales_invoice.sorted_planned_sales_invoice_items"
                    :key="planned_sales_invoice_item.id"
                    >
                    <td> {{ planned_sales_invoice_item.menu_item.name }} </td>
                    <td class="text-right"> {{ planned_sales_invoice_item.quantity }} </td>
                    <td class="text-right"> {{ currency_format(planned_sales_invoice_item.menu_item.outlet_menu_item.price) }} </td>
                    <td class="text-right">
                        {{ percent_format( get(sales_invoice.discount_map[planned_sales_invoice_item.menu_item.outlet_menu_item.id], "percentage", 0) ) }}
                    </td>
                    <td class="text-right">
                        {{ currency_format(
                            planned_sales_invoice_item.quantity *
                            planned_sales_invoice_item.menu_item.outlet_menu_item.price *
                            (1 - get(sales_invoice.discount_map[planned_sales_invoice_item.menu_item.outlet_menu_item.id], "percentage", 0))) }}
                    </td>
                </tr>
            </tbody>

            <tfoot class="table-borderless">
                <tr>
                    <th> </th>
                    <th> </th>
                    <th> </th>
                    <th class="text-right"> Sub Total </th>
                    <th class="text-right"> {{ currency_format(pretax_sum) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Tax {{ percent_format(sales_invoice.outlet.pajak_pertambahan_nilai) }} </th>
                    <th class="text-right"> {{ currency_format(tax) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Service Charge {{ percent_format(sales_invoice.outlet.service_charge) }} </th>
                    <th class="text-right"> {{ currency_format(service_charge) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Diskon Khusus {{ percent_format(special_discount_percentage) }} </th>
                    <th class="text-right">
                        -{{ currency_format(special_discount) }}
                    </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Total </th>
                    <th class="text-right"> {{ currency_format(total) }} </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Cash </th>
                    <th class="text-right"> {{ currency_format(cash) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Rounding </th>
                    <th class="text-right"> {{ currency_format(rounding) }} </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Total Change </th>
                    <th class="text-right"> {{ currency_format(total_change) }} </th>
                </tr>
            </tfoot>
        </table>

        <div class="text-right">
            <!-- Dine In / Takeaway -->
            <h4>
                <span class="badge badge-info">
                    {{ order_types[sales_invoice.type] }}
                </span>
            </h4>

            <table class="table-sm table-borderless d-inline-block" style="witdh: 300px">
                <tbody>
                    <tr>
                        <td> Jumlah Dibayar </td>
                        <td>
                            <vue-cleave
                                class="form-control text-right"
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

        <div class="text-right m-t:5">

            <a :href="this.update_and_confirm_url" class="btn btn-warning">
                Revisi Transaksi
            </a>

            <button
                @click="confirmTransaction"
                class="btn btn-primary">
                Konfirmasi Transaksi
            </button>
        <div>
    </div>
</template>

<script>

import { vsprintf } from "sprintf-js"
import { currency_format, percent_format } from "../numeral_helpers"
import special_discount_percentages from "../special_discount_percentages"
import order_types from "../order_types"
import { Multiselect } from "vue-multiselect"
import { get } from "lodash"
import VueCleave from "vue-cleave-component"

export default {
    props: [
        "sales_invoice", "submit_url", "redirect_url", "update_and_confirm_url",
    ],

    components: { VueCleave, Multiselect },

    data() {
        return {
            cash: null,
            special_discount_percentage: null,
            error_data: null,
        }
    },

    methods: {
        get,
        currency_format,
        percent_format,
        vsprintf,

        confirmTransaction() {
            swal({
                icon: 'warning',
                text: 'Apakah Anda yakin Anda hendak mengkonfirmasi transaksi ini?',
                buttons: ['Tidak', 'Ya'],
            })
            .then(is_ok => {
                if (is_ok) {
                    $.post(this.submit_url, {...this.final_form, token: window.token})
                        .done(response => {
                            this.error_data = null;

                            /* Send request to the print server */
                            this.sendPrintRequest(response)
                                .done(response => {
                                    swal({ icon: "success", text: "Pembayaran berhasil" })
                                        .then(is_ok => { window.location.replace(this.redirect_url) })
                                })
                                .fail((xhr, status, error) => {
                                    Sentry.captureException({xhr, status, error})
                                    swal({ icon: "error", text: "Terjadi masalah pada saat menghubungi printing server / printer. Mohon periksa koneksi dengan printer Anda." });
                                })
                        })
                        .fail((xhr, status, error) => {
                            if (status !== 422) {
                                Sentry.captureException(xhr)
                            }

                            let response = xhr.responseJSON;
                            this.error_data = response.data;
                        });
                }
            })
        },

        sendPrintRequest(request) {
            return $.post(`${this.sales_invoice.outlet.print_server_url}/manual_print`, request)
        },
    },

    computed: {
        final_form() {
            return {
                cash: this.cash,
                special_discount: this.special_discount_percentage,
            }
        },

        special_discount_percentages() {
            return special_discount_percentages
        },
        
        order_types() {
            return order_types
        },

        undiscounted_sales_invoice_items() {
            return this.sales_invoice.sorted_planned_sales_invoice_items
                .filter(item => get(this.sales_invoice.discount_map[item.menu_item.outlet_menu_item.id], "percentage", 0) == 0) 
        },

        undiscounted_total() {
            return this.undiscounted_sales_invoice_items.reduce((prev, curr) => {
                return prev + (curr.quantity * curr.menu_item.outlet_menu_item.price)
            }, 0)
        },

        /* Pretax sum with item discount included */
        pretax_sum() {
            return this.sales_invoice.sorted_planned_sales_invoice_items.reduce((prev, curr) => {
                return prev + (
                    curr.quantity * curr.menu_item.outlet_menu_item.price * (1 - get(this.sales_invoice.discount_map[curr.menu_item.outlet_menu_item.id], "percentage", 0))
                )
            }, 0)
        },

        prediscount_pretax_sum() {
            return this.sales_invoice.sorted_planned_sales_invoice_items.reduce((prev, curr) => {
                return prev + (curr.quantity * curr.menu_item.outlet_menu_item.price)
            }, 0)
        },

        special_discount() {
            return this.undiscounted_total * this.special_discount_percentage
        },

        tax() {
            return this.prediscount_pretax_sum * this.sales_invoice.outlet.pajak_pertambahan_nilai
        },

        service_charge() {
            return this.prediscount_pretax_sum * this.sales_invoice.outlet.service_charge
        },

        total() {
            return this.pretax_sum + (this.tax + this.service_charge - this.special_discount)
        },

        rounding() {
            return Math.round(this.total / 100) * 100
        },

        total_change() {
            return this.cash - this.rounding
        }
    },
}
</script>