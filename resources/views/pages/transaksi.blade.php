@extends('layouts.app')

@section('title', 'Transaksi')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Transaksi</h1>
            </div>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>Riwayat Transaksi</h4>
                        </div>
                        <div class="card-body">
                            <div class="buttons">
                                <button class="btn btn-primary" id="modal-7">Tambah</button>
                            </div>
                        </div>
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table-striped table" id="table-transaksi">
                                    <tr>
                                        <th>Nomor</th>
                                        <th>Tanggal</th>
                                        <th>Tipe</th>
                                        <th>Sumber Dana</th>
                                        <th style="width: 30%">Keterangan</th>
                                        <th>Jumlah</th>
                                        <th>Action</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <form class="modal-part" id="form-transaksi">
            <div class="form-group">
                <label>Tipe</label>
                <div class="input-group">
                    <select class="custom-select" id="select-tipe-option" onchange={updateDropdownSource()}>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Sumber Dana</label>
                <div class="input-group">
                    <select class="custom-select" id="select-sumber-dana-option">
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Tanggal Transaksi</label>
                <div class="input-group">
                    <input type="date" class="form-control" placeholder="tanggal" name="tanggal" id="tanggal">
                </div>
            </div>
            <div class="form-group">
                <label>Jumlah</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Jumlah Uang" name="jumlah-uang"
                        id="jumlah-uang">
                </div>
            </div>
            <div class="form-group">
                <label>Keterangan</label>
                <div class="input-group">
                    <input type="text" class="form-control" placeholder="Input keterangan transaksi" name="keterangan"
                        id="keterangan">
                </div>
            </div>
        </form>

        <div class="modal" id="modal-edit-transaction" tabindex="-1" role="dialog">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Transaksi</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form id="modal-edit-transaction">
                            <div class="form-group">
                                <label>Tipe</label>
                                <div class="input-group">
                                    <select class="custom-select" id="select-tipe" onchange={updateDropdownSourceEdit()}>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Sumber Dana</label>
                                <div class="input-group">
                                    <select class="custom-select" id="select-sumber-dana">
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Tanggal Transaksi</label>
                                <div class="input-group">
                                    <input type="date" class="form-control" placeholder="tanggal" name="tanggal"
                                        id="transaction_date">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Jumlah</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Jumlah Uang" name="jumlah-uang"
                                        id="transaction_total">
                                </div>
                            </div>
                            <div class="form-group">
                                <label>Keterangan</label>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Input keterangan transaksi"
                                        name="keterangan" id="transaction_description">
                                </div>
                            </div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                        <button type="button" class="btn btn-primary" id='update-transaction'>Update Data</button>
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>
    <script src="{{ asset('library/prismjs/prism.js') }}"></script>
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
    <script src="{{ asset('js/page/bootstrap-modal.js') }}"></script>

    <script>
        let checkEndingBalance = 0;
        let idTipeUpdate;
        let idSumberDanaUpdate;

        $(document).ready(function() {
            showTransaction()
            showDropdownSumberDana(checkEndingBalance)
            showDropdownTipe()
            showDropdownUpdateTipe()
            showDropdownUpdateSumberDana(checkEndingBalance)
        })

        function updateDropdownSource() {
            const typeName = $('#select-tipe-option option:selected').text();

            if (typeName == 'Pengeluaran') {
                checkEndingBalance = 1;
            } else {
                checkEndingBalance = 0;
            }

            showDropdownSumberDana(checkEndingBalance)

        }

        function updateDropdownSourceEdit() {
            const typeName = $('#select-tipe option:selected').text();

            if (typeName == 'Pengeluaran') {
                checkEndingBalance = 1;
            } else {
                checkEndingBalance = 0;
            }

            showDropdownUpdateSumberDana(checkEndingBalance)

        }

        function showTransaction() {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');

            $.ajax({
                url: `/api/transaction?transaction_is_cancelled=0&transaction_user_id=${user_id}`,
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(index, data) {
                        $("#table-transaksi").append(`
                                <tr>
                                    <td>${ index + 1 }</td>
                                    <td>${ data.transaction_date }</td>
                                    <td>${ data.type_name }</td>
                                    <td>${ data.source_name }</td>
                                    <td>${ data.transaction_description }</td>
                                    <td>Rp ${ data.transaction_total }</td>
                                    <td>
                                        <button type="button" class="btn btn-success" data-id=${data.transaction_id} id='transaction-edit'>Edit</button>
                                        <a href="#" class="btn btn-danger" data-id=${data.transaction_id} onClick='deleteTransaction(${data.transaction_id})'>Hapus</a>
                                    </td>
                                </tr>
                            `);
                    });
                }
            })
        }

        function showDropdownSumberDana(checkEndingBalance) {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            const user_id = localStorage.getItem('user_id');

            $.ajax({
                url: `/api/source?source_is_cancelled=0&check_ending_balance=${checkEndingBalance}&source_user_id=${user_id}`,
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $("#select-sumber-dana-option").empty();

                    $.each(res, function(index, data) {
                        $("#select-sumber-dana-option").append(`
                                <option value=${data.source_id}>${data.source_name}</option>
                            `);
                    });
                }
            })
        }

        function showDropdownTipe() {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');

            $.ajax({
                url: "/api/type",
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(index, data) {
                        $("#select-tipe-option").append(`
                                <option id="option-tipe-select" value=${data.type_id}>${data.type_name}</option>
                            `);
                    });
                }
            })
        }

        function showDropdownUpdateTipe() {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');

            $.ajax({
                url: "/api/type",
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $("#select-tipe").empty();

                    $.each(res, function(index, data) {
                        if (data.type_id == idTipeUpdate) {
                            $("#select-tipe").append(`
                                <option id="option-tipe-select" selected value=${data.type_id}>${data.type_name}</option>
                            `);
                        } else {
                            $("#select-tipe").append(`
                                <option id="option-tipe-select" value=${data.type_id}>${data.type_name}</option>
                            `);
                        }

                    });
                }
            })
        }

        function showDropdownUpdateSumberDana(checkEndingBalance) {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            const user_id = localStorage.getItem('user_id');

            $.ajax({
                url: `/api/source?source_is_cancelled=0&check_ending_balance=${checkEndingBalance}&source_user_id=${user_id}`,
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $("#select-sumber-dana").empty();

                    $.each(res, function(index, data) {
                        if (data.source_id == idSumberDanaUpdate) {
                            $("#select-sumber-dana").append(`
                                <option selected value=${data.source_id}>${data.source_name}</option>
                            `);

                        } else {
                            $("#select-sumber-dana").append(`
                                <option value=${data.source_id}>${data.source_name}</option>
                            `);
                        }

                    });
                }
            })
        }

        function deleteTransaction(id) {
            console.log(id);
            console.log('masuk')
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            swal({
                    title: 'Anda Akan Menghapus Data Ini, Apakah Yakin?',
                    text: 'Sekali dihapus, data tidak akan bisa dikembalikan.',
                    icon: 'warning',
                    buttons: true,
                    dangerMode: true,
                })
                .then((willDelete) => {
                    if (willDelete) {
                        $.ajax({
                            url: `http://127.0.0.1:8000/api/transaction/${id}`,
                            headers: {
                                'X-XSRF-TOKEN': csrfToken,
                                'Authorization': 'Bearer ' + token,
                            },
                            method: 'DELETE',
                            success: function(res) {
                                console.log(res)

                                swal('Data berhasil di hapus', {
                                    icon: 'success',
                                });
                                window.location.reload();
                            },
                            error: function(res) {
                                console.log(res)

                                swal('Data tidak dapat di hapus', {
                                    icon: 'error',
                                });
                            }
                        })
                    } else {
                        swal('Data batal dihapus!');
                    }
                });
        }

        $(document).on('click', '#transaction-edit', function(e) {
            e.preventDefault();

            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');

            let transaction_id = $(this).data("id");

            $('#modal-edit-transaction').modal('show');

            $.ajax({
                url: `/api/transaction?transaction_id=${transaction_id}`,
                method: 'GET',
                dataType: 'json',
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                success: function(res) {
                    console.log(res)
                    $('#transaction_total').val(res[0].transaction_total);
                    $('#transaction_description').val(res[0].transaction_description);
                    $('#transaction_date').val(res[0].transaction_date);

                    $('#update-transaction').attr('data-id', res[0].transaction_id)


                    idTipeUpdate = res[0].transaction_type_id;
                    idSumberDanaUpdate = res[0].transaction_source_id;
                    if (res[0].type_name == 'Pengeluaran') {
                        checkEndingBalance = 1;
                    } else {
                        checkEndingBalance = 0;
                    }


                    showDropdownUpdateTipe()
                    showDropdownUpdateSumberDana(checkEndingBalance)
                }
            })
        })

        $(document).on('click', '#update-transaction', function(e) {
            e.preventDefault();

            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            const user_id = localStorage.getItem('user_id');

            let updated_transaction_id = $(this).data("id");

            $.ajax({
                url: `api/transaction/${updated_transaction_id}`,
                method: 'PUT',
                dataType: 'json',
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                data: {
                    'transaction_user_id': user_id,
                    'transaction_source_id': $('#select-sumber-dana').val(),
                    'transaction_type_id': $('#select-tipe').val(),
                    'transaction_date': $('#transaction_date').val(),
                    'transaction_total': $('#transaction_total').val(),
                    'transaction_description': $('#transaction_description').val()
                },
                success: function(res) {
                    $('#modal-edit-transaction').modal('hide');

                    swal('Data berhasil tersimpan', {
                        icon: 'success',
                    });
                    window.location.reload();
                },
                error: function() {
                    $('#modal-edit-transaction').modal('hide');

                    swal('Data tidak bisa tersimpan', {
                        icon: 'error',
                    });
                }
            })
        })
    </script>
@endpush
