@extends('layouts.app')

@section('title', 'Home')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">
@endpush

@section('main')
    <div class="main-content">
        <section class="section">
            <div class="section-header">
                <h1>Dashboard</h1>
            </div>
            <div class="row">
                <div class="col-lg-6 col-md-12 col-sm-12 col-24">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-primary">
                            <i class="fa-solid fa-money-bill-wave fa-2xl" style="color: #ffffff;"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Pendapatan</h4>
                            </div>
                            <div class="card-body" id="income">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 col-md-12 col-sm-12 col-24">
                    <div class="card card-statistic-1">
                        <div class="card-icon bg-warning">
                            <!-- <i class="far fa-file"></i> -->
                            <i class="fa-solid fa-money-bill-trend-up fa-2xl" style="color: #ffffff;"></i>
                        </div>
                        <div class="card-wrap">
                            <div class="card-header">
                                <h4>Total Pengeluaran</h4>
                            </div>
                            <div class="card-body" id="expense">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header">
                            <h4>List Transaksi</h4>
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
                        <div class="card-body p-0">
                            <div class="table-responsive">
                                <table class="table-striped table" id="tabel-histori">
                                    <tr>
                                        <th>Tanggal</th>
                                        <th>Status Transaksi</th>
                                        <th>E-Wallet</th>
                                        <th>Tipe</th>
                                        <th>Jumlah</th>
                                        <th>Keterangan</th>
                                        <th>Saldo Akhir</th>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
    <script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
    <script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
    <script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

    <!-- Page Specific JS File -->
    <script src="{{ asset('js/page/index-0.js') }}"></script>
    <script>
        $(function() {
            showIncome()
            showHistory()
        })

        function showIncome() {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            const user_id = localStorage.getItem('user_id');

            $.ajax({
                url: `http://127.0.0.1:8000/api/histories_income_expense?history_user_id=${user_id}`,
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    const income = res.income ? formatRupiah(res.income, 'Rp. ') : 'Rp. 0';
                    const expense = res.expense ? formatRupiah(res.expense, 'Rp. ') : 'Rp. 0';

                    $("#income").append(`  
                        ${income}
                    `);
                    $("#expense").append(`  
                        ${expense}
                    `);
                }
            })
        }

        function showHistory() {
            const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
            const token = localStorage.getItem('token');
            const user_id = localStorage.getItem('user_id');
            $.ajax({
                url: `http://127.0.0.1:8000/api/history?history_user_id=${user_id}`,
                headers: {
                    'X-XSRF-TOKEN': csrfToken,
                    'Authorization': 'Bearer ' + token,
                },
                method: 'GET',
                dataType: 'json',
                success: function(res) {
                    $.each(res, function(index, data) {
                        let statusTransaksi;
                        if (data.transaction_is_cancelled == 0) {
                            statusTransaksi = '<div class="badge badge-success">Success</div>'
                        } else {
                            statusTransaksi = '<div class="badge badge-danger">Canceled</div>'
                        }
                        $("#tabel-histori").append(`
                            <tr>
                                <td>${ data.transaction_date }</td>
                                <td>${ statusTransaksi }
                                </td>
                                <td class="align-middle">
                                    <div class="badge badge-success">${ data.source_transaction_name ? data.source_transaction_name : data.source_history_name  }</div>
                                </td>
                                <td>${ data.type_name }</td>
                                <td>${ data.history_transaction_total }</td>
                                <td>${ data.transaction_description }</td>
                                <td>${ data.history_ending_balance }</td>
                            </tr>
                        `);
                    });
                }
            })
        }

        function formatRupiah(angka, prefix) {
            let number_string = angka.replace(/[^,\d]/g, '').toString(),
                split = number_string.split(','),
                sisa = split[0].length % 3,
                rupiah = split[0].substr(0, sisa),
                ribuan = split[0].substr(sisa).match(/\d{3}/gi);

            if (ribuan) {
                separator = sisa ? '.' : '';
                rupiah += separator + ribuan.join('.');
            }

            rupiah = split[1] != undefined ? rupiah + ',' + split[1] : rupiah;
            return prefix == undefined ? rupiah : (rupiah ? 'Rp. ' + rupiah : '');
        }
    </script>
@endpush
