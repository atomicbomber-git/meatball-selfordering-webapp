<template>
    <div class="card">
        <div class="card-block">

            <div class='form-group'>
                <label for='name'> Nama Program Diskon: </label>
                <input
                    v-model='name'
                    class='form-control'
                    :class="{'is-invalid': get(this.error_data, 'errors.name', false)}"
                    type='text' id='name' placeholder='Nama Program Diskon'>
                <label class='error' v-if="get(this.error_data, 'errors.name', false)">{{ get(this.error_data, 'errors.name', false) }}</label>
            </div>

            <div class='form-group'>
                <label for='starts_at'> Waktu Mulai: </label>

                <datetime
                    :input-class="{'form-control': true, 'is-invalid': get(this.error_data, 'errors.starts_at', false)}"
                    placeholder="Waktu Mulai"
                    type="datetime" v-model="starts_at"></datetime>

                <label class='error' v-if="get(this.error_data, 'errors.starts_at', false)">{{ get(this.error_data, 'errors.starts_at', false) }}</label>
            </div>

            <div class='form-group'>
                <label for='ends_at'> Waktu Selesai: </label>

                <datetime
                    :input-class="{'form-control': true, 'is-invalid': get(this.error_data, 'errors.ends_at', false)}"
                    placeholder="Waktu Selesai"
                    type="datetime" v-model="ends_at"></datetime>
                <label class='error' v-if="get(this.error_data, 'errors.ends_at', false)">{{ get(this.error_data, 'errors.ends_at', false) }}</label>
            </div>

            <div class="form-group">
                <label for="selected_outlet_menu_item"> Pilih Menu: </label>
                <multiselect
                    selectLabel=""
                    deselectLabel=""
                    track-by="id"
                    :custom-label="({ menu_item }) => menu_item.name"
                    :options="unadded_outlet_menu_items"
                    v-model="selected_outlet_menu_item"
                    />
            </div>

            <div class="form-group">
                <label for="outlet_menu_items"> Daftar Menu Diskon: </label>
                
                <table class="table table-sm table-striped table-bordered">
                    <thead class="thead thead-dark">
                        <tr>
                            <th> Menu </th>
                            <th class="t-a:r"> Harga Asli </th>
                            <th class="t-a:r"> Diskon (%) </th>
                            <th class="t-a:r"> Harga Diskon </th>
                            <th class="t-a:r"> Kendali </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="outlet_menu_item in added_outlet_menu_items" :key="outlet_menu_item.id">
                            <td> {{ outlet_menu_item.menu_item.name }} </td>
                            <td class="t-a:r"> {{ currency_format(outlet_menu_item.price) }} </td>
                            <td class="t-a:r">
                                <cleave-range
                                    min="1"
                                    max="100"
                                    v-model="outlet_menu_item.percentage"
                                    />
                            </td>
                            <td class="t-a:r"> {{ currency_format(outlet_menu_item.price * ( 1 - outlet_menu_item.percentage / 100 )) }} </td>
                            <td class="t-a:r">
                                <button @click="outlet_menu_item.is_added = false" class="btn btn-danger btn-sm">
                                    Hapus
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>

                <div class="t-a:r m-t:5">
                    <button @click="submitForm" :disabled="!this.is_submittable" class="btn btn-primary">
                        Ubah Diskon
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Datetime } from 'vue-datetime';
import { Multiselect } from "vue-multiselect"
import VueCleave from "vue-cleave-component"
import CleaveRange from "./CleaveRange"
import { get } from 'lodash';
import { currency_format } from '../numeral_helpers'
import moment from 'moment'

export default {
    props: [
        "discount", "submit_url", "redirect_url",
    ],
    components: { Datetime, Multiselect, VueCleave, CleaveRange },

    data() {
        return {
            name: this.discount.name,
            starts_at: moment(this.discount.starts_at).toISOString(),
            ends_at: moment(this.discount.ends_at).toISOString(),
            error_data: null,
            selected_outlet_menu_item: null,

            m_outlet: {
                ...this.discount.outlet,
                outlet_menu_items: this.discount.outlet.outlet_menu_items.map(outlet_menu_item => {
                    
                    let discount_menu_item = this.discount.discount_menu_items.find(dm_item => dm_item.outlet_menu_item_id === outlet_menu_item.id)
                    
                    if (discount_menu_item === undefined) {
                        return {
                            ...outlet_menu_item,
                            is_added: false,
                            percentage: 0,
                        }
                    }
                    else {
                        return {
                            ...outlet_menu_item,
                            is_added: true,
                            percentage: discount_menu_item.percentage * 100,
                        }
                    }
                })
            }
        }
    },

    methods: {
        get,
        currency_format,
        moment,

        submitForm() {
            $.post(this.submit_url, {token: window.token, ...this.final_form})
                .done(response => {
                    this.error_data = null;
                    window.location.replace(this.redirect_url);
                })
                .fail((xhr, status, error) => {
                    let response = xhr.responseJSON;
                    this.error_data = response.data;
                });
        }
    },

    computed: {
        unadded_outlet_menu_items() {
            return this.m_outlet.outlet_menu_items.filter(({ is_added }) => !is_added)
        },
        
        added_outlet_menu_items() {
            return this.m_outlet.outlet_menu_items.filter(({ is_added }) => is_added)
        },

        is_submittable() {
            return (this.final_form.starts_at !== null) &&
                (this.final_form.ends_at !== null)
        },

        final_form() {
            return {
                name: this.name,
                starts_at: this.starts_at ? moment(this.starts_at).format("YYYY-MM-DD HH:mm:ss") : null,
                ends_at: this.ends_at ? moment(this.ends_at).format("YYYY-MM-DD HH:mm:ss") : null,
                outlet_menu_items: this.added_outlet_menu_items.map(({ id, percentage }) => ({ id, percentage }))
            }
        },
    },

    watch: {
        selected_outlet_menu_item(outlet_menu_item) {
            if (outlet_menu_item !== null) {
                outlet_menu_item.is_added = true
                this.selected_outlet_menu_item = null
            }
        }
    }
}
</script>