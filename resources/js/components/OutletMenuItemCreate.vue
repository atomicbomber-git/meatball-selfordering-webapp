<template>
    <div class="card">
        <div class="card-block">

            <div class='form-group'>
                <label for='menu_item'> Menu Item: </label>
                
                <multiselect
                    placeholder="Nama Menu"
                    selectLabel=""
                    deselectLabel=""
                    track-by="id"
                    label="name"
                    :options="menu_category.menu_items"
                    v-model="selected_menu_item"
                    :preselect-first="false"
                >
                </multiselect>
                <div v-if="get(this.error_data, 'errors.menu_item_id', false)" class="text-danger small">
                    {{ get(this.error_data, 'errors.menu_item_id', false) }}
                </div>
            </div>

            <div class='form-group'>
                <label for='price'> Harga (Rp.): </label>
                <vue-cleave
                    class="form-control"
                    :class="{'is-invalid': get(this.error_data, 'errors.price', false)}"
                    v-model.number="price"
                    :options="{ numeral: true, numeralDecimalMark: ',', delimiter: '.' }">
                </vue-cleave>
                <label class='error' v-if="get(this.error_data, 'errors.price', false)">{{ get(this.error_data, 'errors.price', false) }}</label>
            </div>

            <div class="t-a:r">
                <button @click="submitForm" class="btn btn-primary">
                    Tambahkan Menu Baru
                </button>
            </div>
        </div>
    </div>
</template>

<script>
import { Multiselect } from "vue-multiselect"
import VueCleave from "vue-cleave-component"
import { get } from "lodash"

export default {
    props: [
        "menu_category", "submit_url", "redirect_url",
    ],

    components: { Multiselect, VueCleave },

    data() {
        return {
            selected_menu_item: null,
            price: null,
            error_data: null,
        }
    },

    computed: {
        final_form() {
            return {
                menu_item_id: get(this.selected_menu_item, "id", null),
                price: this.price,
            }
        }
    },

    methods: {
        get,

        submitForm() {
            $.post(this.submit_url, {token: window.token, ...this.final_form})
                .done(response => {
                    this.error_data = null;
                    window.location.replace(this.redirect_url);
                })
                .fail((xhr, status, error) => {
                    let response = JSON.parse(xhr.responseText);
                    this.error_data = response.data;
                });
        }
    }
}
</script>