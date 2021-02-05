<!--Elaboró Romelia Pérez Nangüelú - rpnanguelu@gmail.com-->
@extends('theme.sivyc.layout')
@section('title', 'Supervisión de Instructores | SIVyC Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <link rel="stylesheet" href="{{asset('css/supervisiones/revision-alumno.css') }}" />
    <div class="card-header">
        Revisi&oacute;n Escolar a un Alumno > Curso: {{ $curso->clave}} > {{ $curso->unidad }}.
    </div>
    @if(session('mensaje'))
        <div class="card-body card-msg " >
                {{ html_entity_decode(session('mensaje')) }}
        </div>
    @endif
    <div class="card card-body">
        <nav>
        <div class="nav nav-tabs" id="nav-tab" role="tablist">
            <?php $i = 0;?>
            @foreach ($data as $item)
                <?php $i++; ?>
                <a class="nav-item nav-link <?php if($i==1) echo 'active'; ?>" id="nav-{{$i}}-tab" data-toggle="tab" href="#nav-{{$i}}" role="tab" aria-controls="nav-{{$i}}" aria-selected="false">{{ date('Y/m/d', strtotime($item->created_at)) }}</a>
            @endforeach
         </div>
        </nav>
        <div class="tab-content" id="nav-tabContent">
            <?php $n = 0;?>
            @foreach ($data as $item)
                <?php $n++; ?>
                <div class="tab-pane fade <?php if($n==1) echo 'show active'; ?>" id="nav-{{$n}}" role="tabpanel" aria-labelledby="nav-{{$i}}-tab">
                    <form action="{{ route('supervision.alumno.guardar') }}" name="frm{{$item->id}}" id="frm{{$item->id}}" method="post" enctype="multipart/form-data" class="was-validated"  novalidate>
                    @csrf
                    {{ Form::hidden('id_supervision',$item->id, ['id'=>'id_supervision[]']) }}
                    {{ Form::hidden('boton',null, ['id'=>'boton']) }}
                    <div class="table-responsive">
                        <table class="table">
                          <thead>
                            <tr>
                              <th scope="col">Datos</th>
                              <th scope="col">SIVyC</th>
                              <th scope="col">Del Alumno</th>
                              <th scope="col" width="30%">Validar</th>
                            </tr>
                          </thead>
                          <tbody>
                            <tr>
                              <th scope="row">Alumno</th>
                              <td>{{ $item->alumno}} </td>
                              <td>{{ $item->apellido_paterno}} {{ $item->apellido_materno}} {{ $item->nombre}}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-4">
                                    <input type="checkbox" name="ok_nombre" value="1" class="custom-control-input  mb-1" id="check{{$item->id}}[1]" @if($item->ok_nombre) {{ 'checked'}} @endif  required/>
                                    <label class="custom-control-label  mb-1" for="check{{$item->id}}[1]"></label>
                                    <div class="invalid-feedback  mb-2">
                                        <input type="text" class="form-control " id="coment{{$item->id}}[1]" name="obs_nombre" value="{{$item->obs_nombre}}" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Edad</th>
                              <td>{{ $item->sivyc_edad }}</td>
                              <td>{{ $item->edad }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_edad" value="1" class="custom-control-input" id="check{{$item->id}}[2]" @if($item->ok_edad) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[2]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_edad" value="{{$item->obs_edad}}" class="form-control" id="coment{{$item->id}}[2]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Escolaridad</th>
                              <td>{{ $item->ultimo_grado_estudios }}</td>
                              <td>{{ $item->escolaridad }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_escolaridad" value="1" class="custom-control-input" id="check{{$item->id}}[3]" @if($item->ok_escolaridad) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[3]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_escolaridad" value="{{$item->obs_escolaridad}}" class="form-control" id="coment{{$item->id}}[3]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                              <tr>
                              <th scope="row">Fecha de Inscripci&oacute;n</th>
                              <td>...</td>
                              <td>{{ $item->fecha_inscripcion }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_fecha_inscripcion" value="1" class="custom-control-input" id="check{{$item->id}}[4]" @if($item->ok_fecha_inscripcion) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[4]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_fecha_inscripcion" value="{{$item->obs_fecha_inscripcion}}" class="form-control" id="coment{{$item->id}}[4]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Documentos Entregados</th>
                              <td>

                              @if($item->chk_acta_nacimiento) {{ 'ACTA DE NACIMIENTO, ' }} @endif
                              @if($item->chk_curp) {{ 'CURP, ' }} @endif
                              @if($item->comprobante_domicilio) {{ 'COMPROBANTE DE DOMICILIO, ' }} @endif
                              @if($item->chk_fotografia) {{ 'FOTOGRAFíA, '}} @endif
                              @if($item->chk_ine) {{ 'INE, ' }} @endif
                              @if($item->chk_comprobante_ultimo_grado) {{ 'COMPROBANTE ULTIMO GRADO DE ESTUDIOS, ' }} @endif
                              @if($item->chk_comprobante_calidad_migratoria) {{ 'COMPROBANTE CALIDAD MIGRATORIA' }} @endif
                              </td>
                              <td>{{ $item->documentos }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_documentos" value="1" class="custom-control-input" id="check{{$item->id}}[5]" @if($item->ok_documentos) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[5]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_documentos" value="{{$item->documentos}}" class="form-control" id="coment{{$item->id}}[5]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>

                            <tr>
                              <th scope="row">Curso</th>
                              <td>{{ $curso->curso }}</td>
                              <td>{{ $item->curso }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_curso" value="1" class="custom-control-input" id="check{{$item->id}}[6]" @if($item->ok_curso) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[6]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_curso" value="{{$item->obs_curso}}" class="form-control" id="coment{{$item->id}}[6]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Fecha en Autorización</th>
                              <td>{{ $curso->fecha_apertura }}</td>
                              <td>{{ $item->fecha_autorizacion }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_fecha_autorizacion" value="1" class="custom-control-input" id="check{{$item->id}}[8]" @if($item->ok_fecha_autorizacion) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[8]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_fecha_autorizacion" value="{{$item->obs_fecha_autorizacion}}" class="form-control" id="coment{{$item->id}}[8]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Tipo</th>
                              <td>{{ $curso->tcapacitacion }}</td>
                              <td>{{ $item->tipo }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_tipo" value="1" class="custom-control-input" id="check{{$item->id}}[10]" @if($item->ok_tipo) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[10]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_tipo" value="{{$item->obs_tipo}}" class="form-control" id="coment{{$item->id}}[10]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Lugar / Medio Virtual</th>
                              <td>{{ $curso->efisico}}</td>
                              <td>{{ $item->lugar }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_lugar" value="1" class="custom-control-input" id="check{{$item->id}}[11]" @if($item->ok_lugar) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[11]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_lugar" value="{{$item->obs_lugar}}" class="form-control" id="coment{{$item->id}}[11]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Cuota de Recuperaci&oacute;n Pagada</th>
                              <td>{{ $item->costo }}</td>
                              <td>{{ $item->cuota }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_cuota" value="1" class="custom-control-input" id="check{{$item->id}}[12]" @if($item->ok_cuota) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[12]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_cuota" value="{{$item->obs_cuota}}" class="form-control" id="coment{{$item->id}}[12]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>


                            <tr>
                              <th scope="row">Fecha Inicio</th>
                              <td>{{ $curso->inicio }}</td>
                              <td>{{ $item->inicio }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_fecha_inicio" value="1" class="custom-control-input" id="check{{$item->id}}[13]" @if($item->ok_fecha_inicio) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[13]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_fecha_inicio" value="{{$item->obs_fecha_inicio}}" class="form-control" id="coment{{$item->id}}[13]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Fecha Termino</th>
                              <td>{{ $curso->termino }} </td>
                              <td>{{ $item->termino }}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_fecha_termino" value="1" class="custom-control-input" id="check{{$item->id}}[14]" @if($item->ok_fecha_termino) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[14]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_fecha_termino" value="{{$item->obs_fecha_termino}}" class="form-control" id="coment{{$item->id}}[14]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>
                            <tr>
                              <th scope="row">Horario</th>
                              <td>{{ $curso->hini }} - {{ $curso->hfin}}</td>
                              <td>{{ $item->hinicio }} - {{ $item->hfin}}</td>
                              <td>
                                  <div class="custom-control custom-checkbox mb-3">
                                    <input type="checkbox" name="ok_horario" value="1" class="custom-control-input" id="check{{$item->id}}[15]" @if($item->ok_horario) {{ 'checked'}} @endif required/>
                                    <label class="custom-control-label" for="check{{$item->id}}[15]"></label>
                                    <div class="invalid-feedback">
                                        <input type="text" name="obs_horario" value="{{$item->obs_horario}}" class="form-control" id="coment{{$item->id}}[15]" placeholder="Comentarios"  required/>
                                    </div>
                                  </div>
                              </td>
                            </tr>


                            <tr>
                              <th scope="row">Comentarios</th>
                              <td colspan="3">
                                    <textarea class="form-control" name="comentarios" rows="3" >{{$item->comentarios}}</textarea>
                              </td>
                            </tr>
                          </tbody>
                      </table>
                </div>
                <br />
                <h6> FOTOS / CAPTURA DE PANTALLAS</h6>
                <hr />
                <br />
                <div class="row">
                    <div class="col text-center">
                    <?PHP $ficheros = preg_grep('~^'.$item->id.'-~', scandir($path_dir)); ?>
                        <div class="row">
                            @foreach($ficheros as $file)
                              <div class="col-sm-8 col-md-3">
                                <img class="img-thumbnail zoom" src="{{asset($path_file)}}/{{$file}}" alt="{{$file}}" width="40%" />
                              </div>
                              @endforeach
                        </div>
                    </div>
                </div>
                <br/>
                 <div class="row">
                    <div class="col text-center">
                        <a href="{{ url('supervision/escolar') }}" class="btn" role="button"><< REGRESAR</a>
                        <button type="button" onclick="if(confirm('Esta seguro de ELIMINAR?')==true){$('#boton').val('eliminar');submit();}" class="btn " @if($item->enviado) {{'disabled'}} @endif disabled>ELIMINAR</button>
                        <button type="submit" onclick="if(confirm('Esta seguro de ENVIAR?')==true){$('#boton').val('enviar');submit();}" class="btn" @if($item->enviado) {{'disabled'}} @endif >ENVIAR</button>
                    </div>
                </div>


                <br/>
            </form>

                </div>
            @endforeach
        </div>
    </div>
    <script src="{{asset("vendor/jquery/jquery.min.js")}}"></script>
    @endsection

