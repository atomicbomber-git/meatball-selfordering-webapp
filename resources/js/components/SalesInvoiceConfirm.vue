<template>
    <div>
        <table class="table table-sm table-striped table-bordered">
            <thead class="thead thead-dark">
                <tr>
                    <th> Item </th>
                    <th class="text-right"> Jumlah </th>
                    <th class="text-right"> Harga </th>
                    <th class="text-right"> Subtotal </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="planned_sales_invoice_item in sales_invoice.sorted_planned_sales_invoice_items"
                    :key="planned_sales_invoice_item.id"
                    >
                    <td> {{ planned_sales_invoice_item.menu_item.name }} </td>
                    <td class="text-right"> {{ planned_sales_invoice_item.quantity }} </td>
                    <td class="text-right"> {{ number_format(planned_sales_invoice_item.menu_item.outlet_menu_item.price) }} </td>
                    <td class="text-right">
                        {{ number_format(
                            planned_sales_invoice_item.quantity *
                            planned_sales_invoice_item.menu_item.outlet_menu_item.price) }}
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="text-right">
            <!-- Dine In / Takeaway -->
            <h4>
                <span class="badge badge-info">
                    {{ order_types[sales_invoice.type] }}
                </span>
            </h4>

            <!-- Total Price -->
            <h3>
                Total: 
                <span class="text-danger">
                    Rp. {{ number_format(this.pretax_sum) }}
                </span>
            </h3>
        </div>

        <div class="text-right mt-5">
            <button @click="confirmTransaction" class="btn btn-primary">
                Konfirmasi Transaksi
            </button>
        <div>

        <div class="text-left">
            <div class="text-center">
                <pre>{{ this.receipt_header }}</pre>
            </div>

            <pre>{{ this.receipt_body }}</pre>
        </div>
    </div>
</template>

<script>

import { vsprintf } from "sprintf-js"
import { number_format, currency_format } from "../numeral_helpers"
import order_types from "../order_types"

export default {
    props: [
        "sales_invoice", "submit_url", "redirect_url",
    ],

    data() {
        return {
            cash: 1000000,
        }
    },

    methods: {
        number_format,
        vsprintf,

        confirmTransaction() {
            swal({
                icon: 'warning',
                text: 'Apakah Anda yakin Anda hendak mengkonfirmasi transaksi ini?',
                buttons: ['Tidak', 'Ya'],
            })
            .then(will_confirm => {
                if (will_confirm) {
                    
                    let url = `${this.sales_invoice.outlet.print_server_url}/manual_print`;
                    
                    let commands = [
                        { name: "setJustification", arguments: [{ data: 1 /* Justify Center */, type: "integer"}] },
                        { name: "text", arguments: [{ data: this.receipt_text, type: "text" }]},
                        { name: "cut", arguments: [] },
                    ]
                    
                    let data = {
                        address: this.sales_invoice.outlet.cashier_printer.ipv4_address,
                        port: this.sales_invoice.outlet.cashier_printer.port,
                        commands: commands,
                    }

                    $.post(url, data)
                        .done(response => {
                            this.error_data = null;
                            // window.location.replace(this.redirect_url);
                        })
                        .fail((xhr, status, error) => {
                            // let response = JSON.parse(xhr.responseText);
                            // this.error_data = response.data;
                        });

                }
            })
        },

        printReceipt() {
            let receipt_text = ""
        },

        receiptSeparatorLine() {
            let text = ""

            for (let index = 0; index < 45; index++) {
                text += "-"
            }

            return text + "\n"
        }
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
            return (this.sales_invoice.outlet.pajak_pertambahan_nilai / 100) * this.pretax_sum
        },

        aftertax_sum() {
            return this.pretax_sum + this.tax
        },

        change() {
            return this.cash - this.aftertax_sum
        },

        receipt_header() {
            let text = "";
            
            text += (this.sales_invoice.outlet.name + "\n") 
            text += (this.sales_invoice.outlet.address + "\n") 
            text += (this.sales_invoice.outlet.phone + "\n") 
            text += ("Tax Invoice" + "\n") 
            text += vsprintf("No. %08d\n", this.sales_invoice.id)
            text += this.receiptSeparatorLine()

            return text
        },

        receipt_body() {
            let text = ""
            let column_01_padding = 30
            let column_02_padding = 14

            const format = `%-${column_01_padding}.${column_01_padding}s%${column_02_padding}.${column_02_padding}s\n`

            /* Sales Invoice Items */
            this.sales_invoice.sorted_planned_sales_invoice_items.forEach(psi_item => {
                text += vsprintf(format, [
                    `${psi_item.quantity}x ${psi_item.menu_item.name}`,
                    currency_format(psi_item.menu_item.outlet_menu_item.price * psi_item.quantity)
                ])
            });

            /* Separator Line */
            text += this.receiptSeparatorLine()
            
            /* Sub Total / Pre-tax price sum */
            text += vsprintf(format, [
                "Sub Total",
                currency_format(this.pretax_sum)
            ])

            /* Tax */
            text += vsprintf(format, [
                `Tax ${this.sales_invoice.outlet.pajak_pertambahan_nilai}%`,
                currency_format(this.tax),
            ])

            /* Service Charge */
            text += vsprintf(format, [
                `Service ${this.sales_invoice.outlet.service_charge}%`,
                currency_format((this.sales_invoice.outlet.service_charge / 100) * this.pretax_sum),
            ])

            /* Separator Line */
            text += this.receiptSeparatorLine()

            /* Cash Paid */
            text += vsprintf(format, [
                `Cash`,
                currency_format(this.cash),
            ])

            /* Separator Line */
            text += this.receiptSeparatorLine()

            /* Total Change */
            text += vsprintf(format, [
                `Total Change`,
                currency_format(this.change),
            ])

            return text
        },

        receipt_text() {
            return this.receipt_header + this.receipt_body
        }
    },
}
</script>