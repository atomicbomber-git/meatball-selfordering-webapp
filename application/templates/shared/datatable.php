<script>
    $(document).ready(function() {
        $("table.datatable").DataTable({
            "language": {
                "url": "<?= base_url("assets/indonesian-datatables.json") ?>"
            }
        })
    })
</script>