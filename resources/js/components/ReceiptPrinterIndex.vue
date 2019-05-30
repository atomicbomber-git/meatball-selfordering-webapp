<template>
    <div>
        <table class="table table-sm table-bordered table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Tipe</th>
                    <th>Alamat IP</th>
                    <th>Port</th>
                    <th class="t-a:c"> Status </th>
                    <th class="t-a:c">Kendali</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(receipt_printer, index) in receipt_printers" :key="receipt_printer.id">
                    <td>{{ index + 1 }}.</td>
                    <td>{{ receipt_printer.name }}</td>
                    <td>{{ receipt_printer.type }}</td>
                    <td>{{ receipt_printer.ipv4_address }}</td>
                    <td>{{ receipt_printer.port }}</td>
                    <td class="t-a:c">
                        <span
                            class="tag"
                            :class="{
                                'tag-success': receipt_printer.is_active,
                                'tag-danger': !receipt_printer.is_active
                            }"
                            >
                            {{ receipt_printer.is_active ? 'Aktif' : 'Tidak Aktif' }}
                        </span>
                    </td>
                    <td class="t-a:c">
                        <button @click="testPrint(receipt_printer)" class="btn btn-info btn-sm">
                            Test Print
                        </button>

                        <a
                            :href="`edit/${receipt_printer.id}`"
                            class="btn btn-info btn-sm"
                            >
                            Ubah
                        </a>

                        <a 
                            :href="`/receiptPrinter/activate/${receipt_printer.id}`"
                            v-if="!receipt_printer.is_active"
                            class="btn btn-success btn-sm">
                            Aktifkan
                        </a>
                        <button v-else disabled class="btn btn-success btn-sm">
                            Aktifkan
                        </button>

                        <button @click="deleteReceiptPrinter(receipt_printer)" class="btn btn-danger btn-sm">
                            Hapus
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="invisible">
            <div ref="loading_modal">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                </div>

                <h3 class="c m-t:3"> Tes Printer, Mohon Tunggu. </h3>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    props: ["print_server_url", "receipt_printers"],

    methods: {
        testPrint(receipt_printer) {
            let data = {
                address: receipt_printer.ipv4_address,
                port: receipt_printer.port,
                commands: [
                    { 
                        name: "setJustification",
                        arguments: [{ data: 1 /* Justify Center */, type: "integer" }],
                    },
                    {
                        name: "setTextSize",
                        arguments: [{ data: 2, type: "integer" }, { data: 2, type: "integer" }]
                    },
                    {
                        name: "text",
                        arguments: [
                            {
                                data: `${receipt_printer.name}\n\n\n`,
                                type: "text",
                            }
                        ]
                    },
                    { name: "cut", arguments: [] }
                ]
            };

            swal({
                content: this.$refs.loading_modal,
                closeModal: false,
                closeOnClickOutside: false,
                buttons: false,
            })

            $.post(`${this.print_server_url}/manual_print`, data)
                .done(response => {
                    this.error_data = null;

                    swal({
                        icon: "success",
                        text: "Pengujian printer berhasil.",
                    })
                })
                .fail((xhr, status, error) => {
                    if (xhr.status === 0 || xhr.status === 500) {
                        Sentry.captureException({xhr, status, error})
                    }

                    let error_text = `Pengujian printer gagal, mohon cek koneksi komputer ini ke alamat ${this.print_server_url}.`
                    if (xhr.status !== 0) {
                        error_text = xhr.responseJSON.message
                    }

                    swal({ icon: "error", text: error_text })
                });
        },

        deleteReceiptPrinter(receipt_printer) {
            $.post(`/receiptPrinter/delete/${receipt_printer.id}`, {token: window.token})
                .done(response => {
                    window.location.replace(response.redirect_url);
                })
                .fail((xhr, status, error) => {
                    let response = xhr.responseJSON;
                    this.error_data = response.data;
                });
        }
    },
};
</script>