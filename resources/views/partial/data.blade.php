@stack('script')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<!-- Load jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<!-- Load DataTables -->
<script src="https://cdn.datatables.net/1.10.24/js/jquery.dataTables.min.js"></script>
<script>
    function openModal(imageSrc) {
        document.getElementById('modalImageContent').src = imageSrc;
        var modal = new bootstrap.Modal(document.getElementById('modalImage'));
        modal.show();
    }
    // data table
    var tabel;
    // read data pengguna
    $(document).ready(function () {
        // console.log("AJAX URL: ", "{{ route('form.index') }}");
        tabel = $('#tbSiswa').DataTable({
            processing: true,
            serverSide: true,
            responsive: true,
            scrollX: true,
            fixedHeader: {
                header: true,
                footer: true
            },
            ajax: "{{ route('form.index') }}",
            columns: [
                {
                    data: null,
                    name: 'id',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row, meta) {
                        var pageInfo = $('#tbSiswa').DataTable().page.info();
                        return pageInfo.start + meta.row + 1;
                    }
                },
                {
                    data: 'nis'
                },
                {
                    data: 'nama'
                },
                {
                    data: 'ttl'
                },
                {
                    data: 'gender'
                },
                {
                    data: 'alamat'
                },
                {
                    data: 'wa',
                    render: function (data, type, row, meta) {
                        return '<a href="https://wa.me/' + data + '" target="_blank">' + data +
                            '</a>';
                    },
                },
                {
                    data: 'kelas'
                },
                {
                    data: 'foto',
                    render: function(data, type, row, meta) {
                            if (data !== '---') {
                                return '<button type="button" class="btn btn-secondary btn-sm btn-icon-text" onclick="openModal(\'/storage/' +
                                    data + '\')">Lihat Foto</button>';
                            } else {
                                return '---';
                            }
                        }
                    // render: function (data, type, row, meta) {
                    //     if (data) {
                    //         return '<img src="/storage/' + data + '" width="50" height="75"/>';
                    //     }
                    //     return '';
                    // }
                }
            ],
            aLengthMenu: [
                [5, 10, 15, -1],
                [5, 10, 15, "All"]
            ],
            iDisplayLength: 10,
            language: {
                paginate: {
                    previous: "Sebelumnya",
                    next: "Selanjutnya"
                },
                info: "Menampilkan _START_ sampai _END_ dari _TOTAL_ entri",
                search: "Cari:",
                lengthMenu: "Tampilkan _MENU_ entri",
                zeroRecords: "Tidak ada data pegawai!",
                infoEmpty: "Menampilkan 0 sampai 0 dari 0 entri",
                infoFiltered: "(disaring dari _MAX_ entri keseluruhan)"
            },
            responsive: true,
            columnDefs: [
                {
                    orderable: false,
                    targets: 0
                }
            ]
        });

        $('#tbSiswa').each(function () {
            var datatable = $(this);
            var search_input = datatable.closest('.dataTables_wrapper').find('div[id$=_filter] input');
            search_input.attr('placeholder', 'Cari');
            search_input.removeClass('form-control-sm');
            var length_sel = datatable.closest('.dataTables_wrapper').find('div[id$=_length] select');
            length_sel.removeClass('form-control-sm');
        });
    });
</script>