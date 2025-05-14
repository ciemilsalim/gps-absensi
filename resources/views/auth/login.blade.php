@extends('layouts.user-app')

@section('content')
<div class="login-form mt-1">
  <div class="section">
    <img src="{{ asset('assets/user/img/sample/photo/vector4.png') }}" alt="image" class="form-image" />
  </div>

  <div class="section mt-1">
    <h1>LOGIN</h1>
    <h4>Isi form untuk masuk</h4>
  </div>

  <div class="section mt-1 mb-5">
    @if (session('status'))
      <div class="alert alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
      @csrf

      <!-- Email -->
      <div class="form-group boxed">
        <div class="input-wrapper">
          <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                 placeholder="Email address" value="{{ old('email') }}" required autofocus>
          <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
          @error('email')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <!-- Password -->
      <div class="form-group boxed">
        <div class="input-wrapper">
          <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                 placeholder="Password" required>
          <i class="clear-input"><ion-icon name="close-circle"></ion-icon></i>
          @error('password')
            <div class="invalid-feedback d-block">{{ $message }}</div>
          @enderror
        </div>
      </div>

      <!-- Remember Me -->
      <div class="form-group boxed mt-2">
        <div class="input-wrapper d-flex align-items-center">
          <input type="checkbox" name="remember" id="remember"
                 class="me-2" {{ old('remember') ? 'checked' : '' }}>
          <label for="remember" class="mb-0">Ingat saya</label>
        </div>
      </div>

      <!-- Links -->
      <div class="form-links mt-2 d-flex justify-content-between">
        @if (Route::has('register'))
          <a href="{{ route('register') }}">Daftar Sekarang</a>
        @endif
        @if (Route::has('password.request'))
          <a href="{{ route('password.request') }}" class="text-muted">Lupa Password?</a>
        @endif
      </div>

      <!-- Submit -->
      <div class="form-button-group mt-3">
        <button type="submit" class="btn btn-primary btn-block btn-lg">Masuk</button>
      </div>
    </form>
  </div>
</div>
@endsection
