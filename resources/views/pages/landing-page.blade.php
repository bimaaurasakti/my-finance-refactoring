@extends('layouts.landingpage')

@section('title', 'Landing Page')

@push('style')
<!-- CSS Libraries -->
<link rel="stylesheet" href="{{ asset('library/jqvmap/dist/jqvmap.min.css') }}">
<link rel="stylesheet" href="{{ asset('library/summernote/dist/summernote-bs4.min.css') }}">

@endpush

@section('main')
<nav class="navbar" style="left: 0px!important; position: relative; z-index: 0; background: #8DCBE6;">
    <a class="navbar-brand  container" href="#" style="margin-left: 0px">My Finance</a>
</nav>
<div class="card-body " style="background: linear-gradient(180deg, #8DCBE6 0%, #9DF1DF 100%); height: 650px; padding-top: 100px">
    <div class="display-4 container text-header-landing" style="color: black; margin-left: 0px; font-weight: bold">Pastikan transaksi harianmu tercatat disini.</div>
    <a type="" href='/auth-login'class="btn button-login mt-4 " style="background-color: transparant; border-color: 1px; margin-left: 15px; border: 2px solid #323232; border-radius: 7px; width: 150px; font-weight: bold">Login</a>

    <div class="row" style="background: #323232; border: 4px solid #323232; border-radius: 25px; padding: 30px; margin: 100px 15px ">
        <div class="text-center col">
            <div class="d-flex flex-column justify-content-center items-align-center">
                <div>
                    <i class="fa-regular fa-clipboard fa-2xl" style="color: #ffffff;"></i>
                </div>
                <div class="mt-3 text-white">
                    Mencatat transaksi harian dengan mudah.
                </div>
            </div>
        </div>
        <div class="text-center col">
            <div class="d-flex flex-column justify-content-center items-align-center">
                <div>
                    <i class="fa-solid fa-chart-line fa-2xl" style="color: #ffffff;"></i>
                </div>
                <div class="mt-3 text-white">
                    Memantau keuangan harian.
                </div>
            </div>

        </div>
        <div class="text-center col">
            <div class="d-flex flex-column justify-content-center items-align-center">
                <div>
                    <i class="fa-sharp fa-solid fa-coins fa-2xl" style="color: #ffffff;"></i>
                </div>
                <div class="mt-3 text-white">
                    Tentukan sumber keuangan sesukamu.
                </div>
            </div>

        </div>

    </div>
</div>
@endsection

@push('scripts')
<!-- JS Libraies -->
<script src="{{ asset('library/simpleweather/jquery.simpleWeather.min.js') }}"></script>
<script src="{{ asset('library/chart.js/dist/Chart.min.js') }}"></script>
<script src="{{ asset('library/jqvmap/dist/jquery.vmap.min.js') }}"></script>
<script src="{{ asset('library/jqvmap/dist/maps/jquery.vmap.world.js') }}"></script>
<script src="{{ asset('library/summernote/dist/summernote-bs4.min.js') }}"></script>
<script src="{{ asset('library/chocolat/dist/js/jquery.chocolat.min.js') }}"></script>

<!-- Page Specific JS File -->
<script src="{{ asset('js/page/index-0.js') }}"></script>
@endpush