<!--ELABORO ORLANDO CHAVEZ - orlando@sidmac.com.com-->
@extends('theme.sivyc.layout')
@section('title', 'Prevalidacion | SIVyC Icatech')
@section('content')
<link rel="stylesheet" href="{{asset('css/global.css') }}" />
<link rel="stylesheet" href="{{asset('edit-select/jquery-editable-select.min.css') }}" />
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<div class="card-header">
    Prevalidacion de Aspirante a Instructor
</div>
<div class="card card-body" style=" min-height:450px;">
    @if (Session::has('success'))
        <div class="alert alert-info alert-block">
            <strong>{{ Session::get('success') }}</strong>
        </div>
    @endif
    @if (Session::has('error'))
        <div class="alert alert-danger alert-block">
            <strong>{{ Session::get('error') }}</strong>
        </div>
    @endif
    <!-- Nav Tabs Start -->
    <ul class="nav nav-tabs mb-3" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="recepcion-tab" data-bs-toggle="tab" data-bs-target="#recepcion" type="button" role="tab" aria-controls="recepcion" aria-selected="true">
                Recepcion
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="prevalidado-tab" data-bs-toggle="tab" data-bs-target="#prevalidado" type="button" role="tab" aria-controls="prevalidado" aria-selected="false">
                Prevalidado
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="cotejado-tab" data-bs-toggle="tab" data-bs-target="#cotejado" type="button" role="tab" aria-controls="cotejado" aria-selected="false">
                Cotejado
            </button>
        </li>
    </ul>
    <div class="tab-content" id="myTabContent">
        <!-- Recepcion Tab -->
        <div class="tab-pane fade show active" id="recepcion" role="tabpanel" aria-labelledby="recepcion-tab">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th scope="col">INSTRUCTOR</th>
                        {{-- <th scope="col">NUMERO DE REVISIÓN</th> --}}
                        <th scope="col">UNIDAD SOLICITA</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $rise)
                        @if($rise->status == 'ENVIADO')
                            <tr>
                                <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                                {{-- <td>{{ $rise->nrevision }}</td> --}}
                                <td>{{ $rise->unidad_solicita }}</td>
                                <td>{{ $rise->status }} {{ $rise->turnado }}</td>
                                <td>{{ $rise->updated_at}}</td>
                                <td>
                                    <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                    <a class="prevalidar-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="PREVALIDAR">
                                        <i class="fa fa-edit fa-2x fa-lg text-primary"></i>
                                    </a> &nbsp;
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Hay Datos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Prevalidado Tab -->
        <div class="tab-pane fade" id="prevalidado" role="tabpanel" aria-labelledby="prevalidado-tab">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th scope="col">INSTRUCTOR</th>
                        {{-- <th scope="col">NUMERO DE REVISIÓN</th> --}}
                        <th scope="col">UNIDAD SOLICITA</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $rise)
                        @if($rise->status == 'PREVALIDADO')
                            <tr>
                                <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                                {{-- <td>{{ $rise->nrevision }}</td> --}}
                                <td>{{ $rise->unidad_solicita }}</td>
                                <td>{{ $rise->status }} {{ $rise->turnado }}</td>
                                <td>{{ $rise->updated_at}}</td>
                                <td>
                                    <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                    <a class="cotejar-btn"
                                        style="color: white; cursor:pointer;"
                                        data-id="{{ $rise->id }}"
                                        data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                        title="COTEJAR">
                                        <i class="fa fa-check fa-2x fa-lg text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Hay Datos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <!-- Cotejado Tab -->
        <div class="tab-pane fade" id="cotejado" role="tabpanel" aria-labelledby="cotejado-tab">
            <table class="table table-responsive-md">
                <thead>
                    <tr>
                        <th scope="col">INSTRUCTOR</th>
                        {{-- <th scope="col">NUMERO DE REVISIÓN</th> --}}
                        <th scope="col">UNIDAD SOLICITA</th>
                        <th scope="col">STATUS</th>
                        <th scope="col">FECHA</th>
                        <th scope="col">ACCION</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($data as $rise)
                        @if($rise->status == 'COTEJADO')
                            <tr>
                                <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                                {{-- <td>{{ $rise->nrevision }}</td> --}}
                                <td>{{ $rise->unidad_solicita }}</td>
                                <td>{{ $rise->status }} {{ $rise->turnado }}</td>
                                <td>{{ $rise->updated_at}}</td>
                                <td>
                                    <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                    <a class="aprobar-btn"
                                        style="color: white; cursor:pointer;"
                                        data-id="{{ $rise->id }}"
                                        data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                        title="APROBAR">
                                        <i class="fa fa-thumbs-up fa-2x fa-lg text-primary"></i>
                                    </a>
                                </td>
                            </tr>
                        @endif
                    @empty
                        <tr>
                            <td colspan="5" class="text-center">No Hay Datos</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <!-- Nav Tabs End -->
</div>

<!-- Modal -->
<div class="modal fade" id="prevalidarModal" tabindex="-1" aria-labelledby="prevalidarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.prevalidar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="prevalidarModalLabel">Prevalidar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea prevalidar este registro?
          <input type="hidden" id="prevalidar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-prevalidar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-danger">Prevalidar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Cotejar Modal -->
<div class="modal fade" id="cotejarModal" tabindex="-1" aria-labelledby="cotejarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.cotejar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="cotejarModalLabel">Cotejar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea cotejar este registro?
          <input type="hidden" id="cotejar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-cotejar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Cotejar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<!-- Aprobar Modal -->
<div class="modal fade" id="aprobarModal" tabindex="-1" aria-labelledby="aprobarModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <form method="POST" action="{{ route('aspirante.instructor.aprobar') }}">
        @csrf
        <div class="modal-header">
          <h5 class="modal-title w-100 text-center" id="aprobarModalLabel">Aprobar</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body text-center">
          ¿Desea aprobar este registro?
          <input type="hidden" id="aprobar-id" name="id" value="">
          <div class="mt-2">
            Aspirante seleccionado: <b><span id="show-aprobar-name"></span></b>
          </div>
        </div>
        <div class="modal-footer justify-content-center">
          <button type="button" class="btn btn-success" data-bs-dismiss="modal">Cerrar</button>
          <button type="submit" class="btn btn-primary">Aprobar</button>
        </div>
      </form>
    </div>
  </div>
</div>

<script>
    $(document).on('click', '.prevalidar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#prevalidar-id').val(id);
        $('#show-prevalidar-name').text(name);
        $('#prevalidarModal').modal('show');
    });
    $(document).on('click', '.cotejar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#cotejar-id').val(id);
        $('#show-cotejar-name').text(name);
        $('#cotejarModal').modal('show');
    });
    $(document).on('click', '.aprobar-btn', function() {
        var id = $(this).data('id');
        var name = $(this).data('name');
        $('#aprobar-id').val(id);
        $('#show-aprobar-name').text(name);
        $('#aprobarModal').modal('show');
    });
</script>
@endsection
