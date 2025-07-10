@extends("theme.sivycAuth.app")

@section('title', 'Sivyc | Inicio de Sesión')

@section('content')
<!-- Bootstrap 4 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
<!-- Bootstrap 4 JS, Popper, and jQuery -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.min.js"></script>
<br>
<div class="container">
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li><strong>{!! $error !!}</strong></li>
                @endforeach
            </ul>
        </div> <br>
    @endif
    @if (session('success'))
        <div class="alert alert-success">
            <strong>{{ session('success') }}</strong>
        </div>
    @endif
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">{{ __('Inicio de Sesión') }}</div>

                <div class="card-body">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf

                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('Usuario') }}</label>

                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>

                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{!! $message !!}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Contraseña') }}</label>

                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">

                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <h2>{!! $message !!}</h2>
                                    </span>
                                @enderror
                                @if ($errors->any())
                                    <div class="form-group row">
                                        <div class="col-md-8 offset-md-4">
                                            <a href="#" data-toggle="modal" data-target="#resetPwdModal">
                                                ¿Olvidaste tu contraseña?
                                            </a>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Recordar') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Iniciar Sesión') }}
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal fade" id="resetPwdModal" tabindex="-1" aria-labelledby="resetPwdModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form method="POST" action="{{ route('reset.password.modal') }}">
      @csrf
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="resetPwdModalLabel">Restablecer Contraseña</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Cerrar"></button>
        </div>
        <div class="modal-body">
          <div class="mb-3">
            <label for="resetEmail" class="form-label">Correo electrónico</label>
            <input type="email" class="form-control" id="resetEmail" name="email" required autofocus>
          </div>
          <div class="mb-3">
            <label for="resetTelefono" class="form-label">Número Telefonico</label>
            <input type="number" class="form-control" id="resetTelefono" name="resetTelefono" required readonly>
          </div>
        </div>
        <div class="modal-footer">
          <button type="submit" class="btn btn-primary">Enviar enlace de restablecimiento</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    var emailInput = document.getElementById('email');
    var resetEmail = document.getElementById('resetEmail');
    var resetTelefono = document.getElementById('resetTelefono');

    // Autofill email in modal
    if(emailInput && resetEmail) {
        emailInput.addEventListener('input', function() {
            resetEmail.value = emailInput.value;
        });
        resetEmail.value = emailInput.value;
    }

    // When modal opens, fetch telefono
    $('#resetPwdModal').on('show.bs.modal', function () {
        var email = resetEmail.value;
        if(email) {
            $.ajax({
                url: '{{ route("get.telefono.by.email") }}',
                type: 'POST',
                data: {
                    email: email,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    resetTelefono.value = response.telefono || '';
                    if (!resetTelefono.value) {
                        resetTelefono.readOnly = false; // Enable if empty
                    } else {
                        resetTelefono.readOnly = true;  // Keep readonly if found
                    }
                },
                error: function() {
                    resetTelefono.value = '';
                }
            });
        } else {
            resetTelefono.value = '';
        }
    });
});
</script>
@endsection
