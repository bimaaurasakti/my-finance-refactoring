@extends('layouts.auth')

@section('title', 'Register')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet" href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
@endpush

@section('main')
    <div class="auth-wrap">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Register</h4>
            </div>

            <div class="card-body">
                <form id="register-form" class="needs-validation" novalidate="">
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input id="email" type="email" class="form-control" name="email" tabindex="1" required
                            autofocus>
                        <div class="invalid-feedback">
                            Please fill in your email
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username" type="username" class="form-control" name="username" tabindex="2" required
                            autofocus>
                        <div class="invalid-feedback">
                            Please fill in your username
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="d-block">
                            <label for="password" class="control-label">Password</label>
                        </div>
                        <input id="password" type="password" class="form-control" name="password" tabindex="3" required>
                        <div class="invalid-feedback">
                            Please fill in your password
                        </div>
                    </div>

                    <div class="form-group">
                        <button type="submit" class="btn btn-primary btn-lg btn-block" tabindex="4">
                            Register
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- JS Libraies -->
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    <script>
        function handleRegister() {
            $.ajax({
                url: "{{ url('/api/auth/register') }}",
                method: 'POST',
                dataType: 'json',
                contentType: 'application/json',
                data: JSON.stringify({
                    email: document.getElementById('email').value,
                    username: document.getElementById('username').value,
                    password: document.getElementById('password').value,
                }),
                success: function(data) {
                    if (data.status === 'Success') {
                        var token = data.data; 

                        localStorage.setItem('token', token);

                        window.location.href = "{{ url('/home') }}";
                    } else {
                        var errorMessage = data.message;
                    }
                },
                error: function(jqXHR, textStatus, errorThrown) {
                    console.error('Error:', errorThrown);
                    swal('Tidak dapat Register Coba Lagi', {
                        icon: 'error',
                    });
                }
            });
        }
        $(document).ready(function() {
            $('#register-form').submit(function(event) {
                event.preventDefault();

                handleRegister();
            });
        });
    </script>

@endpush
