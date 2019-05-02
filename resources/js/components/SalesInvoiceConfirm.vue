<template>
    <div>
        <table class="table table-sm table-striped">
            <thead class="thead thead-dark table-bordered">
                <tr>
                    <th> Item </th>
                    <th class="text-right"> Jumlah </th>
                    <th class="text-right"> Harga Satuan </th>
                    <th class="text-right"> Jumlah x Harga Satuan </th>
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
                        {{ currency_format(
                            planned_sales_invoice_item.quantity *
                            planned_sales_invoice_item.menu_item.outlet_menu_item.price) }}
                    </td>
                </tr>
            </tbody>

            <tfoot class="table-borderless">
                <tr>
                    <th>  </th>
                    <th>  </th>
                    <th class="text-right"> Sub Total </th>
                    <th class="text-right"> {{ currency_format(pretax_sum) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Tax {{ sales_invoice.outlet.pajak_pertambahan_nilai }}% </th>
                    <th class="text-right"> {{ currency_format(tax) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Service Charge {{ sales_invoice.outlet.service_charge }}% </th>
                    <th class="text-right"> {{ currency_format(service_charge) }} </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th class="text-right"> Total </th>
                    <th class="text-right"> {{ currency_format(total) }} </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th class="text-right"> Cash </th>
                    <th class="text-right"> {{ currency_format(cash) }} </th>
                </tr>

                <tr>
                    <th></th>
                    <th></th>
                    <th class="text-right"> Rounding </th>
                    <th class="text-right"> {{ currency_format(rounding) }} </th>
                </tr>

                <tr class="border-top">
                    <th></th>
                    <th></th>
                    <th class="text-right"> Total Change </th>
                    <th class="text-right"> {{ currency_format(this.total_change) }} </th>
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
                            <div class='invalid-feedback'>{{ get(this.error_data, 'errors.cash', false) }}</div>
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

        <div class="text-right mt-5">

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
import { currency_format } from "../numeral_helpers"
import { get } from "lodash"
import order_types from "../order_types"
import VueCleave from "vue-cleave-component"

export default {
    props: [
        "sales_invoice", "submit_url", "redirect_url", "update_and_confirm_url",
    ],

    components: { VueCleave },

    data() {
        return {
            cash: null,
            error_data: null,
        }
    },

    methods: {
        get,
        currency_format,
        vsprintf,

        confirmTransaction() {
            swal({
                icon: 'warning',
                text: 'Apakah Anda yakin Anda hendak mengkonfirmasi transaksi ini?',
                buttons: ['Tidak', 'Ya'],
            })
            .then(is_ok => {
                if (is_ok) {
                    $.post(this.submit_url, {cash: this.cash, token: window.token})
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
        order_types() {
            return order_types
        },

        pretax_sum() {
            return this.sales_invoice.sorted_planned_sales_invoice_items.reduce((prev, curr) => {
                return prev + (curr.quantity * curr.menu_item.outlet_menu_item.price)
            }, 0)
        },

        tax() {
            return this.pretax_sum * this.sales_invoice.outlet.pajak_pertambahan_nilai / 100
        },

        service_charge() {
            return this.pretax_sum * this.sales_invoice.outlet.service_charge / 100
        },

        total() {
            return this.pretax_sum + (this.tax + this.service_charge)
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