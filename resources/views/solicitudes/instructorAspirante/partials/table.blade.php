{{-- filepath: resources/views/solicitudes/instructorAspirante/partials/table.blade.php --}}
@php
    $iteration = 0;
    $rechazadoStatus = [
        'ENVIADO' => 'RECHAZADO ENVIADO',
        'PREVALIDADO' => 'RECHAZADO PREVALIDADO',
        'CONVOCADO' => 'RECHAZADO CONVOCADO'
    ];
@endphp
<table class="table table-responsive-md">
    <thead>
        <tr>
            <th scope="col">#</th> <!-- Consecutive column -->
            <th scope="col">INSTRUCTOR</th>
            {{-- <th scope="col">NUMERO DE REVISIÓN</th> --}}
            <th scope="col">UNIDAD ASIGNADA</th>
            <th scope="col">ESPECIALIDAD</th>
            <th scope="col">FECHA</th>
            <th scope="col">
                {{ !empty($showRechazados) ? 'MOTIVO' : 'ACCION' }}
            </th>
        </tr>
    </thead>
    <tbody>
        @forelse($data as $rise)
            @php
                $isRechazado = isset($rechazadoStatus[$status]) && $rise->status == $rechazadoStatus[$status];
            @endphp
            @if($rise->status == $status || (!empty($showRechazados) && $isRechazado))
            @php  $iteration++; @endphp
                <tr>
                    <td>{{ $iteration }}</td> <!-- Show consecutive number -->
                    <td>{{ $rise->nombre }} {{$rise->apellidoPaterno}} {{$rise->apellidoMaterno}}</td>
                    {{-- <td>{{ $rise->nrevision }}</td> --}}
                    <td>{{ $rise->unidad_asignada }}</td>
                    <td>
                        <ul class="mb-0 ps-3">
                            @if(is_array($rise->data_especialidad))
                                @foreach ($rise->data_especialidad as $esp)
                                    @if (isset($especialidades[$esp['especialidad_id']]))
                                        <li>{{ $especialidades[$esp['especialidad_id']] }}</li>
                                    @endif
                                @endforeach
                            @endif
                        </ul>
                    </td>
                    <td>{{ $rise->updated_at}}</td>
                    <td>
                        @if(!in_array($rise->status, ['RECHAZADO ENVIADO', 'RECHAZADO PREVALIDADO', 'RECHAZADO CONVOCADO']))
                            @if($status == 'ENVIADO')
                                <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                <a class="prevalidar-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="PREVALIDAR">
                                    <i class="fa fa-edit fa-2x fa-lg text-primary"></i>
                                </a> &nbsp;
                                <a class="rechazar-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="RECHAZAR">
                                    <i class="fa fa-times fa-2x fa-lg text-danger"></i>
                                </a>
                            @elseif($status == 'PREVALIDADO')
                                <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                <a class="cotejar-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="CONVOCAR">
                                    <i class="fa fa-check fa-2x fa-lg text-primary"></i>
                                </a> &nbsp;
                                <a class="rechazar-prevalidado-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="RECHAZAR">
                                    <i class="fa fa-times fa-2x fa-lg text-danger"></i>
                                </a>
                            @elseif($status == 'CONVOCADO')
                                <a style="color: white;" target="_blank" class="fa fa-eye fa-2x fa-lg text-success" title="MOSTRAR INFORMACION" href="{{route('instructor-ver', ['id' => $rise->id])}}"></a> &nbsp;
                                <a class="aprobar-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="APROBAR">
                                    <i class="fa fa-thumbs-up fa-2x fa-lg text-primary"></i>
                                </a> &nbsp;
                                <a class="rechazar-convocado-btn"
                                    style="color: white; cursor:pointer;"
                                    data-id="{{ $rise->id }}"
                                    data-name="{{ $rise->nombre }} {{ $rise->apellidoPaterno }} {{ $rise->apellidoMaterno }}"
                                    title="RECHAZAR">
                                    <i class="fa fa-times fa-2x fa-lg text-danger"></i>
                                </a>
                            @endif
                        @else
                            <span class="badge bg-success">RECHAZADO: {{ $rise->rechazo }}</span>
                        @endif

                    </td>
                </tr>
            @endif
        @empty
            <tr>
                <td colspan="6" class="text-center">No Hay Datos</td>
            </tr>
        @endforelse
    </tbody>
</table>
