<template>
    <div>
        <table class="table table-sm table-striped">
            <thead class="thead thead-dark">
                <tr>
                    <th>#</th>
                    <th>Nama</th>
                    <th>Alamat IP</th>
                    <th>Port</th>
                    <th>Kendali</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(receipt_printer, index) in receipt_printers" :key="receipt_printer.id">
                    <td>{{ index + 1 }}</td>
                    <td>{{ receipt_printer.name }}</td>
                    <td>{{ receipt_printer.ipv4_address }}</td>
                    <td>{{ receipt_printer.port }}</td>
                    <td>
                        <button @click="testPrint(receipt_printer)" class="btn btn-dark btn-sm">
                            Test Print
                            <i class="fa fa-print"></i>
                        </button>

                        <a
                            :href="`edit/${receipt_printer.id}`"
                            class="btn btn-dark btn-sm"
                            >
                            Ubah
                        </a>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="invisible">
            <div ref="loading_modal">
                <div class="spinner-border" style="width: 3rem; height: 3rem;" role="status">
                </div>

                <h3 class="text-center mt-3"> Tes Printer, Mohon Tunggu. </h3>
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
                        arguments: [{ data: 3, type: "integer" }, { data: 3, type: "integer" }]
                    },
                    {
                        name: "text",
                        arguments: [
                            {
                                data: "001\n\n\n",
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

            $.post(`${this.print_server_url}/manual_print`, { token: window.token, ...data })
                .done(response => {
                    this.error_data = null;

                    swal({
                        icon: "success",
                        text: "Pengujian printer berhasil.",
                    })
                })
                .fail((xhr, status, error) => {
                    swal({
                        icon: "error",
                        text: `Pengujian printer gagal, mohon cek koneksi komputer ini ke alamat ${this.print_server_url}.`,
                    })
                });
        }
    }
};
</script>