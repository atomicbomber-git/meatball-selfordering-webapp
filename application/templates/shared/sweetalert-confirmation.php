<script>
    /* This script requires SweetAlert 2 and JQuery to be loaded beforehand */
    $(document).ready(function() {
        $("form.confirmed").submit(function(e) {
            e.preventDefault();
            swal({
                'icon': 'warning',
                'dangerMode': true,
                'text': 'Apakah Anda yakin Anda ingin melakukan tindakan ini?',
                'buttons': ['Tidak', 'Ya']
            })
            .then(will_submit => {
                if (will_submit) {
                    $(this).off("submit").submit()
                }
            })
        });
    });
</script>