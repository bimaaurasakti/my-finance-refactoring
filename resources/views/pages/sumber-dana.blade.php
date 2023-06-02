@extends('layouts.app')

@section('title', 'Sumber Dana')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
<div class="main-content">
    <section class="section">
        <div class="section-header">
            <h1>Sumber Dana</h1>
        </div>

        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4>Kategori Sumber Dana</h4>
                        <div class="card-header-form">
                            <form>
                                <div class="input-group">
                                    <input type="text" class="form-control" placeholder="Search">
                                    <div class="input-group-btn">
                                        <button class="btn btn-primary"><i class="fas fa-search"></i></button>
                                    </div>
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="buttons">
                            <button class="btn btn-primary" id="modal-5">Tambah</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table id="table-source" class="table-striped table text-center">
                                <tr>
                                    <th style="width: 5%">Nomor</th>
                                    <th style="width: 20%">Sumber Dana</th>
                                    <th style="width: 20%">Dana Awal</th>
                                    <th style="width: 20%">Dana Akhir</th>
                                    <th style="width: 25%">Action</th>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <form class="modal-part" id="modal-source">
        <div class="form-group">
            <label>Sumber Dana</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                    </div>
                </div>
                <input type="text" class="form-control" placeholder="Sumber Dana" name="source_name_create" id="source_name_create">
            </div>
        </div>
        <div class="form-group">
            <label>Dana Awal</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <div class="input-group-text">
                        <i class="fa-solid fa-money-bill-1-wave"></i>
                    </div>
                </div>
                <input type="text" class="form-control" placeholder="Dana Awal" name="beginning_balance" id="beginning_balance">
            </div>
        </div>
    </form>

    <div class="modal" id="modal-edit-source" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Edit Sumber Dana</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form id="modal-edit-source">
                        <div class="form-group">
                            <label>Sumber Dana</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-circle-dollar-to-slot"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="Sumber Dana" name="source_name" id="source_name">
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Saldo</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <div class="input-group-text">
                                        <i class="fa-solid fa-money-bill-1-wave"></i>
                                    </div>
                                </div>
                                <input type="text" class="form-control" placeholder="Rp 150000" name="source_ending_balance" id="source_ending_balance" disabled>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Batal</button>
                    <button type="button" class="btn btn-primary" id='update-btn'>Update Data</button>
                </div>
            </div>
        </div>
    </div>

    <div id='success-modal'></div>

</div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="{{ asset('js/page/index-0.js') }}"></script>
<script src="{{ asset('js/page/bootstrap-modal.js') }}"></script>

<script>
    $(document).ready(function() {
        showSource()
    })

    function showSource() {
        const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
        const user_id = localStorage.getItem('user_id');
        const token = localStorage.getItem('token');

        $.ajax({
            url: `/api/source?source_is_cancelled=0&source_user_id=${user_id}`,
            headers: {
                'X-XSRF-TOKEN': csrfToken,
                'Authorization': 'Bearer ' + token,
            },
            method: 'GET',
            dataType: 'json',
            success: function(res) {
                console.log(res)

                $.each(res, function(index, data) {
                    $("#table-source").append(`
                        <tr>
                            <td>${index + 1}</td>
                            <td class="align-middle">
                                <div>${data.source_name}</div>
                            </td>
                            <td>Rp ${data.beginning_balance}</td>
                            <td>Rp ${data.source_ending_balance}</td>
                            <td>
                                <button type="button" class="btn btn-success" data-id=${data.source_id} id='edit-modal'>Edit</button>
                                <a class="btn btn-danger" id='delete-data' onClick='deleteSource(${data.source_id})'>Hapus</a>
                            </td>
                        </tr>
                    `)
                })
            }
        })
    }

    function deleteSource(id) {

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
                        url: `http://127.0.0.1:8000/api/source/${id}`,
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
                            // window.location.reload();
                        }
                    })
                } else {
                    swal('Data batal dihapus!');
                }
            });
    }

    $(document).on('click', '#edit-modal', function(e) {
        e.preventDefault();

        const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
        const token = localStorage.getItem('token');

        let source_id = $(this).data("id");

        $('#modal-edit-source').modal('show');

        $.ajax({
            url: `/api/source?source_id=${source_id}`,
            method: 'GET',
            dataType: 'json',
            headers: {
                'X-XSRF-TOKEN': csrfToken,
                'Authorization': 'Bearer ' + token,
            },
            success: function(res) {
                $('#source_name').val(res[0].source_name);
                $('#source_ending_balance').val("Rp " + res[0].source_ending_balance);
                $('#update-btn').attr('data-id', res[0].source_id)
            }
        })
    })

    $(document).on('click', '#update-btn', function(e) {
        e.preventDefault();

        const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
        const token = localStorage.getItem('token');

        let updated_id = $(this).data("id");

        $.ajax({
            url: `api/source/${updated_id}`,
            method: 'PUT',
            dataType: 'json',
            headers: {
                'X-XSRF-TOKEN': csrfToken,
                'Authorization': 'Bearer ' + token,
            },
            data: {
                'source_name': $('#source_name').val()
            },
            success: function(res) {
                // window.location.reload();
                swal('Data Berhasil Diperbarui!', 'success');
            },
            error: function(errorThrown) {
                console.error('Error:', errorThrown);
            }
        })
    })
</script>
@endpush