@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'ICATECH | SIVyC Icatech')

@section('content')

    <div class="container g-pt-30">
        <div class="row">
            <div class="col">
                <h1>ACERCA DEL INSTITUTO</h1>
            </div>
        </div>

        <form action="{{ route('instituto.guardar') }}" method="post">
            @csrf

            <div class="row py-2">
                <div class="col">
                    <div class="form-group">
                        <label for="nombre" class="control-label">Nombre del instituto</label>
                        <input type="text" class="form-control" id="nombre" name="nombre" placeholder="Nombre del instituto"
                            value="{{ $instituto != null ? $instituto->name : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="distintivo" class="control-label">Distintivo del instituto</label>
                        <input type="text" class="form-control" id="distintivo" name="distintivo" placeholder="Distintivo del instituto"
                            value="{{ $instituto != null ? $instituto->distintivo : '' }}" required>
                    </div>
                </div>
            </div>

            <div class="row py-2">
                <div class="col">
                    <div class="form-group">
                        <label for="telefono" class="control-label">Telefono del instituto</label>
                        <input type="text" class="form-control" id="telefono" name="telefono"
                            placeholder="Telefono del instituto"
                            value="{{ $instituto != null ? $instituto->telefono : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="direccion" class="control-label">Dirección del instituto</label>
                        <input type="text" class="form-control" id="direccion" name="direccion"
                            placeholder="Dirección del instituto"
                            value="{{ $instituto != null ? $instituto->direccion : '' }}" required>
                    </div>
                </div>
            </div>

            <div class="row py-2">
                <div class="col">
                    <div class="form-group">
                        <label for="email" class="control-label">Email del instituto</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email del instituto"
                            value="{{ $instituto != null ? $instituto->correo : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="url" class="control-label">Pagina web del instituto</label>
                        <input type="text" class="form-control" id="url" name="url" placeholder="Pagina web del instituto"
                            value="{{ $instituto != null ? $instituto->url : '' }}" required>
                    </div>
                </div>
            </div>

            {{-- new campos --}}
            <div class="row py-2">
                <div class="col">
                    <div class="form-group">
                        <label for="titular" class="control-label">Titular</label>
                        <input type="text" class="form-control" id="titular" name="titular" placeholder="Nombre del titular"
                            value="{{ $instituto != null ? $instituto->titular : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="cargo" class="control-label">Cargo</label>
                        <input type="text" class="form-control" id="cargo" name="cargo" placeholder="Cargo del titular"
                            value="{{ $instituto != null ? $instituto->cargo : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="email_titular" class="control-label">Email del titular</label>
                        <input type="email" class="form-control" id="email_titular" name="email_titular" placeholder="Email del titular"
                            value="{{ $instituto != null ? $instituto->correo_titular : '' }}" required>
                    </div>
                </div>
            </div>

            {{-- actualizado y fechas --}}
            <hr>
            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="DCC" class="control-label">Creado por</label>
                        <input disabled type="text" class="form-control" id="DCC" name="DCC" placeholder="Creado por"
                            value="{{ $instituto != null ? $instituto->nameCreated : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="DIeIA" class="control-label">Actualizado por</label>
                        <input disabled type="text" class="form-control" id="DIeIA" name="DIeIA"
                            placeholder="Actualizado por" value="{{ $instituto != null ? $instituto->nameUpdated : '' }}"
                            required>
                    </div>
                </div>
            </div>

            <div class="row">
                <div class="col">
                    <div class="form-group">
                        <label for="DCC" class="control-label">Creado</label>
                        <input disabled type="text" class="form-control" id="DCC" name="DCC" placeholder="Creado"
                            value="{{ $instituto != null ? $instituto->created_at : '' }}" required>
                    </div>
                </div>
                <div class="col">
                    <div class="form-group">
                        <label for="DIeIA" class="control-label">Actualizado</label>
                        <input disabled type="text" class="form-control" id="DIeIA" name="DIeIA" placeholder="Actualizado"
                            value="{{ $instituto != null ? $instituto->updated_at : '' }}" required>
                    </div>
                </div>
            </div>

            {{-- boton --}}
            <div class="row my-2">
                <div class="col">
                    <div class="pull-right">
                        <button type="submit" class="btn btn-primary">Guardar cambios</button>
                        <input type="text" name="idarea" id="idarea" hidden value="{{ 1 }}">
                    </div>
                </div>
            </div>

        </form>

    </div>
@endsection
