<template>
    <div>
        <table class="table table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th>Nama Item</th>
                    <th class="text-center">Jumlah</th>
                    <th class="text-right">Harga</th>
                    <th class="text-right">Subtotal</th>
                </tr>
            </thead>

            <tbody>
                <tr
                    v-for="sales_invoice_item in p_sales_invoice.sales_invoice_items"
                    :key="sales_invoice_item.id"
                >
                    <td> {{ sales_invoice_item.name }} </td>
                    <td class="text-center">
                        <button
                            @click="--sales_invoice_item.quantity"
                            class="btn btn-sm btn-danger">
                            <i class="fa fa-minus"></i>
                        </button>

                        <order-quantity
                            class="mx-2"
                            v-model="sales_invoice_item.quantity"
                            >
                        </order-quantity>

                        <button
                            @click="++sales_invoice_item.quantity"
                            class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td class="text-right"> Rp. {{ number_format(sales_invoice_item.price) }} </td>
                    <td class="text-right"> Rp. {{ number_format((sales_invoice_item.quantity * sales_invoice_item.price)) }} </td>
                </tr>
            </tbody>
        </table>

        <div class="text-right">
            <div class="custom-control custom-radio custom-control-inline">
                <input v-model="p_sales_invoice.type" value="DINE_IN" class="custom-control-input" type="radio" id="order_type_dine_in" name="order_type">
                <label class="custom-control-label" for="order_type_dine_in"> {{ order_types["DINE_IN"] }} </label>
            </div>

            <div class="custom-control custom-radio custom-control-inline">
                <input v-model="p_sales_invoice.type" value="TAKEAWAY" class="custom-control-input" type="radio" id="order_type_takeaway" name="order_type">
                <label class="custom-control-label" for="order_type_takeaway"> {{ order_types["TAKEAWAY"] }} </label>
            </div>
        </div>
        
        <div class="total-price font-weight-bold text-right">
            TOTAL:
            <span class="text-danger">Rp. {{ number_format(total_price) }}</span>
        </div>
    </div>
</template>

<script>

import order_types from '../order_types.js'
import { number_format } from "../numeral_helpers.js"
import OrderQuantity from "./OrderQuantity.vue"

export default {
    props: ["sales_invoice", "submit_url", "redirect_url"],
    components: { OrderQuantity },

    data() {
        return {
            p_sales_invoice: {
                ...this.sales_invoice,
                sales_invoice_items: [ ...this.sales_invoice.sales_invoice_items ]
            }
        }
    },

    computed: {
        order_types() {
            return order_types
        },

        total_price() {
            return this.p_sales_invoice.sales_invoice_items.reduce((prev, curr) => {
                return prev + (curr.price + curr.quantity)
            }, 0)
        }
    },

    methods: {
        number_format,
    }
};
</script>

<style>
</style>
