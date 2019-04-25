<template>
    <div>
        <div class="text-right my-2">
            <button @click="addItem" class="btn btn-info btn-sm">
                Tambah Item
            </button>
        </div>

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
                    v-for="item in added_items"
                    :key="item.id">
                    
                    <td> 
                        <multiselect
                            track-by="id"
                            :custom-label="om_item => om_item.menu_item.name"
                            :options="item_options"
                            v-model="item.outlet_menu_item"
                            />
                    </td>

                    <td class="text-center">
                        <button
                            @click="--item.quantity"
                            class="btn btn-sm btn-danger">
                            <i class="fa fa-minus"></i>
                        </button>

                        <order-quantity
                            class="mx-2"
                            v-model="item.quantity"
                            >
                        </order-quantity>

                        <button
                            @click="++item.quantity"
                            class="btn btn-sm btn-success">
                            <i class="fa fa-plus"></i>
                        </button>
                    </td>
                    <td class="text-right"> Rp. {{ number_format(get(item, "outlet_menu_item.price", 0)) }} </td>
                    <td class="text-right"> Rp. {{ number_format(item.quantity * get(item.outlet_menu_item, "price", 0)) }} </td>
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
            <span class="text-danger">Rp.  </span>
        </div>
    </div>
</template>

<script>

import order_types from '../order_types.js'
import { number_format } from "../numeral_helpers.js"
import { get } from "lodash"
import OrderQuantity from "./OrderQuantity.vue"
import { Multiselect } from "vue-multiselect"

export default {
    props: ["outlet_menu_items", "sales_invoice", "submit_url", "redirect_url"],
    components: { OrderQuantity, Multiselect },

    data() {
        return {
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
            })
        }
    },

    computed: {
        order_types() {
            return order_types
        },

        added_items() {
            return this.items
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
        }
    },

    methods: {
        get,
        number_format,

        addItem() {
            this.items.push({ outlet_menu_item: null, quantity: 1 })
        }
    }
};
</script>

<style>
</style>
