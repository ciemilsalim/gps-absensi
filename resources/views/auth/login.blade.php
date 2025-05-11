@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center mt-5">
    <div class="col-md-6 col-lg-5">
      <div class="card shadow-sm">
        <div class="card-body">
          <h4 class="text-center mb-4">Login</h4>

          @if (session('status'))
            <div class="alert alert-success">
              {{ session('status') }}
            </div>
          @endif

          <form method="POST" action="{{ route('login') }}">
            @csrf

            <!-- Email -->
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input
                id="email"
                type="email"
                name="email"
                class="form-control @error('email') is-invalid @enderror"
                value="{{ old('email') }}"
                required
                autofocus
              >
              @error('email')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Password -->
            <div class="mb-3">
              <div class="d-flex justify-content-between">
                <label for="password" class="form-label">Password</label>
                @if (Route::has('password.request'))
                  <a href="{{ route('password.request') }}" class="small text-primary">Lupa password?</a>
                @endif
              </div>
              <input
                id="password"
                type="password"
                name="password"
                class="form-control @error('password') is-invalid @enderror"
                required
              >
              @error('password')
                <div class="invalid-feedback">{{ $message }}</div>
              @enderror
            </div>

            <!-- Remember Me -->
            <div class="mb-3 form-check">
              <input type="checkbox" name="remember" class="form-check-input" id="remember" {{ old('remember') ? 'checked' : '' }}>
              <label class="form-check-label" for="remember">Ingat saya</label>
            </div>

            <!-- Submit -->
            <div class="d-grid">
              <button type="submit" class="btn btn-primary">
                Masuk
              </button>
            </div>
          </form>

          <hr class="my-4">

          <!-- Register link -->
          @if (Route::has('register'))
            <p class="text-center mb-0">
              Belum punya akun? <a href="{{ route('register') }}">Daftar</a>
            </p>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection
