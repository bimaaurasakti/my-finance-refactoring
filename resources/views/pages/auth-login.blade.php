@extends('layouts.auth')

@section('title', 'Login')

@push('style')
    <!-- CSS Libraries -->
    <link rel="stylesheet"
        href="{{ asset('library/bootstrap-social/bootstrap-social.css') }}">
    <link rel="stylesheet"
        href="{{ asset('css/finance-style/login.css') }}">
@endpush

@section('main')
    <div class="auth-wrap">
        <div class="card card-primary">
            <div class="card-header">
                <h4>Login</h4>
            </div>
    
            <div class="card-body">
                <form method="POST"
                    action="#"
                    id="login-form"
                    class="needs-validation"
                    novalidate="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <input id="username"
                            type="text"
                            class="form-control"
                            name="username"
                            tabindex="1"
                            required
                            autofocus>
                        <div class="invalid-feedback">
                            Please fill in your username
                        </div>
                    </div>
    
                    <div class="form-group">
                        <div class="d-block">
                            <label for="password" class="control-label">Password</label>
                        </div>
                        <input id="password"
                            type="password"
                            class="form-control"
                            name="password"
                            tabindex="2"
                            required>
                        <div class="invalid-feedback">
                            Please fill in your password
                        </div>
                    </div>
    
                    <div class="form-group">
                        <button type="submit"
                            class="btn btn-primary btn-lg btn-block"
                            tabindex="4">
                            Login
                        </button>
                    </div>
                </form>
            </div>
        </div>
        <div class="text-muted mt-5 text-center">
            Belum Punya Akun? <a href="{{url('/auth-register')}}">Daftar Di Sini</a>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="{{ asset('library/sweetalert/dist/sweetalert.min.js') }}"></script>

    <script>
      function handleLogin() {
        $.ajax({
          url: "{{ url('/api/auth/login') }}",
          method: 'POST',
          dataType: 'json',
          contentType: 'application/json',
          data: JSON.stringify({
            username: document.getElementById('username').value,
            password: document.getElementById('password').value,
          }),
          success: function(data) {
            if (data.status === 'Success') {
              var token = data.data; 

              localStorage.setItem('token', token);

              handleUserId();
              
              window.location.href = "{{ url('/home') }}";
            } else {
              var errorMessage = data.message;
            }
          },
          error: function(jqXHR, textStatus, errorThrown) {
            console.error('Error:', errorThrown);
            swal('Tidak dapat Login di Website My Finance', {
                  icon: 'error',
            });
          }
        });
      }

      $(document).ready(function() {
        $('#login-form').submit(function(event) {
          event.preventDefault();
          
          handleLogin();
        });
      });
    
      function handleUserId() {
        const csrfToken = document.cookie.match(/XSRF-TOKEN=([^;]+)/)[1];
        const token = localStorage.getItem('token');

        $.ajax({
          url: '/api/user',
          headers: {
            'X-XSRF-TOKEN': csrfToken,
            'Authorization': 'Bearer ' + token,
          },
          method: 'GET',
          dataType: 'json',
          success: function(res) {
            localStorage.setItem('user_id', JSON.stringify(res.user_id));
          }
        })
      }

      $(document).ready(function() {
        $('#login-form').submit(function(event) {
          event.preventDefault();
          
          handleLogin();
        });
      });
    </script>

@endpush