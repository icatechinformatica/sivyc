<!--ELABORO ROMELIA PEREZ NANGUELU- rpnanguelu@gmail.com-->

<div class="table-responsive">
    <table class="table table-bordered table-striped">
        <thead>
            <tr>
                <th scope="col">Vo.Bo.</th>
                <th scope="col">CURSO</th>
                <th scope="col">INSTRUCTOR</th>
                <th scope="col" width="90px">INICIO</th>
                <th scope="col" width="90px">TERMINO</th>
                <th scope="col" width="13%">UNIDAD/AM</th>
                <th scope="col" width="0%">RECHAZAR</th>
            </tr>
        </thead>
        <tbody id="result_table">
            @foreach ($data as $item)
                @php
                    if(strlen($item->curso) >= 18) $curso = mb_substr($item->curso, 0, 18, 'UTF-8')."..";
                    else $curso = $item->curso;
                @endphp
                <tr>
                    <td class="text-center">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" value="{{ $item->id }}" name="activo_curso"   onchange="cambia_estado({{$item->id}},$(this))"  @if($item->vb_dg==true){{'checked'}} @endif >
                        </div>
                    </td>
                    <td>
                        <a onclick="ver_modal('CURSO', '{{ $item->folio_grupo}}')" style="color:rgb(1, 95, 84);">
                            <b>{{ $item->curso }}</b>
                        </a>
                    </td>
                    <td>
                        <a onclick="seleccion_instructor('{{ $item->folio_grupo }}')" title="Seleccionar Instructor"><i class="fa fa-address-book mr-2" aria-hidden="true" style="color:rgb(1, 95, 84);"></i></a>
                        <a onclick="ver_modal('INSTRUCTOR', '{{ $item->folio_grupo}}')" style="color:rgb(1, 95, 84);">
                            <b>{{ $item->nombre }}</b>
                        </a>
                    </td>
                    <td>{{ date('d/m/Y', strtotime($item->inicio)) }}</td>
                    <td>{{ date('d/m/Y', strtotime($item->termino)) }}</td>
                    <td>{{ $item->unidad }}</td>
                    <td class="text-center">
                        @if($item->clave == '0')
                        <a onclick="modal_motivo('{{ $curso }}','{{ $item->id }}')">
                            <i class="fas fa-window-close fa-2x fa-danger"></i>
                        </a>
                        @else
                            {{ $item->turnado}}
                        @endif
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="11" >
                    {{$data->render()}}
                </td>
            </tr>
        </tbody>
        <tfoot>
        </tfoot>
    </table>
</div>
