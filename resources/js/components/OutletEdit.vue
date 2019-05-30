<template>
    <div class="card">
        <div class="card-block">
            <form @submit.prevent="onFormSubmit">

                 <div class="row">
                    <div class="col-md-6">
                        <div class='form-group'>
                            <label for='name'> Nama: </label>
                            <input
                                v-model='name'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.name', false)}"
                                type='text' id='name' placeholder='Nama'>
                            <label class='error' v-if="get(this.error_data, 'errors.name', false)">{{ get(this.error_data, 'errors.name', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='address'> Alamat: </label>
                            <textarea
                                v-model='address'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.address', false)}"
                                type='text' id='address' placeholder='Alamat'></textarea>
                            <label class='error' v-if="get(this.error_data, 'errors.address', false)">{{ get(this.error_data, 'errors.address', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='phone'> Nomor Telefon: </label>
                            <input
                                v-model='phone'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.phone', false)}"
                                type='text' id='phone' placeholder='Nomor Telefon'>
                            <label class='error' v-if="get(this.error_data, 'errors.phone', false)">{{ get(this.error_data, 'errors.phone', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='pajak_pertambahan_nilai'> Pajak Pertambahan Nilai (%): </label>
                            <input
                                v-model='pajak_pertambahan_nilai'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.pajak_pertambahan_nilai', false)}"
                                type='text' id='pajak_pertambahan_nilai' placeholder='Pajak Pertambahan Nilai (%)'>
                            <label class='error' v-if="get(this.error_data, 'errors.pajak_pertambahan_nilai', false)">{{ get(this.error_data, 'errors.pajak_pertambahan_nilai', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='service_charge'> Service Charge: </label>
                            <input
                                v-model='service_charge'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.service_charge', false)}"
                                type='text' id='service_charge' placeholder='Service Charge'>
                            <label class='error' v-if="get(this.error_data, 'errors.service_charge', false)">{{ get(this.error_data, 'errors.service_charge', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='npwpd'> NPWPD: </label>
                            <input
                                v-model='npwpd'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.npwpd', false)}"
                                type='text' id='npwpd' placeholder='NPWPD'>
                            <label class='error' v-if="get(this.error_data, 'errors.npwpd', false)">{{ get(this.error_data, 'errors.npwpd', false) }}</label>
                        </div>

                        <div class='form-group'>
                            <label for='print_server_url'> URL Print Server: </label>
                            <input
                                v-model='print_server_url'
                                class='form-control'
                                :class="{'is-invalid': get(this.error_data, 'errors.print_server_url', false)}"
                                type='text' id='print_server_url' placeholder='URL Print Server'>
                            <label class='error' v-if="get(this.error_data, 'errors.print_server_url', false)">{{ get(this.error_data, 'errors.print_server_url', false) }}</label>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class='form-group'>
                            <label for='supervisor_id'> Supervisor Outlet: </label>
                            <multiselect
                                placeholder="Supervisor Outlet"
                                selectLabel=""
                                deselectLabel=""
                                track-by="id"
                                label="name"
                                :options="supervisor_users"
                                v-model="supervisor_user"
                                :preselect-first="true"
                            ></multiselect>
                            <div v-if="get(this.error_data, 'errors.supervisor_id', false)" class='text-danger text-sm'>
                                {{ get(this.error_data, 'errors.supervisor_id', false) }}
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="outlet_users"> Waiter / Cashier Outlet: </label>
                            <multiselect
                                placeholder="Waiter / Cashier Outlet"
                                selectLabel=""
                                deselectLabel=""
                                track-by="id"
                                :custom-label="(user) => `${user.name} (${user.level})`"
                                :options="unadded_regular_users"
                                v-model="regular_user"
                            ></multiselect>

                            <div class="table-responsive m-t:3">
                                <table class="table table-sm table-striped table-bordered">
                                    <thead class="thead thead-dark">
                                        <tr>
                                            <th> Nama User </th>
                                            <th> Level </th>
                                            <th class="text-center"> Kendali </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <tr v-for="user in added_regular_users" :key="user.id">
                                            <td> {{ user.name }} </td>
                                            <td> {{ user.level }} </td>
                                            <td class="text-center">
                                                <button @click="onRemoveRegularUser(user)" type="button" class="btn btn-sm btn-danger">
                                                    Hapus
                                                </button>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end">
                    <button class="btn btn-primary">
                        Ubah Data Outlet
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>

import { get } from 'lodash'
import { Multiselect } from "vue-multiselect"

export default {
    props: [
        "submit_url", "redirect_url", "outlet", "users"
    ],

    components: { Multiselect },

    data() {
        return {
            /* Form data */
            name: this.outlet.name,
            address: this.outlet.address,
            phone: this.outlet.phone,
            pajak_pertambahan_nilai: this.outlet.pajak_pertambahan_nilai * 100,
            service_charge: this.outlet.service_charge * 100,
            npwpd: this.outlet.npwpd,
            print_server_url: this.outlet.print_server_url,
            m_users: this.users.map(user => {
                return {
                    ...user,
                    is_added: this.outlet.outlet_users
                        .find(outlet_user => outlet_user.user_id === user.id) !== undefined
                }
            }),

            /* vue-multiselect models */
            regular_user: null,
            supervisor_user: this.outlet.supervisor,

            error_data: null,
        }
    },

    methods: {
        get,

        onFormSubmit() {
            $.post(this.submit_url, {token: window.token, ...this.final_form})
                .done(response => {
                    this.error_data = null;
                    window.location.replace(this.redirect_url);
                })
                .fail((xhr, status, error) => {
                    let response = xhr.responseJSON;
                    this.error_data = response.data;
                });
        },

        onRemoveRegularUser(user) {
            user.is_added = false
        },
    },

    watch: {
        regular_user(user) {
            if (user !== null) {
                user.is_added = true
                this.regular_user = null
            }
        }
    },

    computed: {
        final_form() {
            return {
                name: this.name,
                address: this.address,
                phone: this.phone,
                pajak_pertambahan_nilai: this.pajak_pertambahan_nilai,
                service_charge: this.service_charge,
                npwpd: this.npwpd,
                print_server_url: this.print_server_url,
                supervisor_id: this.supervisor_user.id,
                outlet_users: this.added_regular_users.map(user => user.id)
            }
        },

        supervisor_users() {
            return this.m_users.filter(user => user.level === "SUPERVISOR")
        },

        regular_users() {
            return this.m_users.filter(user => user.level !== "SUPERVISOR")
        },

        unadded_regular_users() {
            return this.regular_users.filter(user => !user.is_added)
        },

        added_regular_users() {
            return this.regular_users.filter(user => user.is_added)
        }
    }
}
</script>

<style>

</style>
