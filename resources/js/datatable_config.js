module.exports = {
	dom:
		'<<"row mb-4"<"col-sm-12 col-md-3"l><"col-sm-12 col-md-6 text-center"B><"col-sm-12 col-md-3"f>>rtip>',
	language: { url: "/assets/indonesian-datatables.json" },
	buttons: [
		{
            extend: "excel",
            footer: true,
			exportOptions: {
				columns: ".printable"
			}
		},
		{
            extend: "pdfHtml5",
            footer: true,
			exportOptions: {
				columns: ".printable"
			}
		},
		{
            extend: "print",
            footer: true,
			exportOptions: {
				columns: ".printable"
            },
		},
		"colvis"
	]
};
