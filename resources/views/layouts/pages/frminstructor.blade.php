<!-- Creado por Orlando Chávez orlando@sidmac.com-->
@extends('theme.sivyc.layout')
@section('title', 'Registro de Instructor | Sivyc Icatech')
@section('content')
    <link rel="stylesheet" href="{{asset('css/supervisiones/global.css') }}" />
    <style>
        table tr th .nav-link {padding: 0; margin: 0;}

        label.onpoint{
            cursor:pointer;
        }
        #center {
            vertical-align:middle;
            text-align:center;
            padding:0px;
        }

    </style>
    <div class="card-header">
        Registro Instructor
    </div>
    <div class="card card-body">
        @if ($errors->any())
            <div class="alert alert-danger">
                {{ $errors->first() }}
            </div>
        @endif
        <form action="{{ url('/instructor/guardar') }}" method="post" id="reginstructor" enctype="multipart/form-data">
            @csrf
            <div>
                <label><h2>Datos Personales</h2></label>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputnombre">Nombre</label>
                    <input name='nombre' id='nombre' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_paterno">Apellido Paterno</label>
                    <input name='apellido_paterno' id='apellido_paterno' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputapellido_materno">Apellido Materno</label>
                    <input name='apellido_materno' id='apellido_materno' type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputcurp">CURP</label>
                    <input name='curp' id='curp' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputrfc">RFC/Constancia Fiscal</label>
                    <input name='rfc' id='rfc' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputhonorarios">Regimen</label>
                    <select class="form-control" name="honorario" id="honorario">
                        <option value="sin especificar">Sin Especificar</option>
                        <option value="HONORARIOS">Honorarios</option>
                        {{-- <option value="ASIMILADOS A SALARIOS">Asimilados a Salarios</option> --}}
                        <option value="HONORARIOS Y ASIMILADOS A SALARIOS">Honorarios y Asimilado a Salario</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputtipo_identificacion">Tipo de Identificación</label>
                    <select class="form-control" name="tipo_identificacion" id="tipo_identificacion">
                        <option value="">SIN ESPECIFICAR</option>
                        <option value="INE">INE</option>
                        <option value="PASAPORTE">PASAPORTE</option>
                        <option value="LICENCIA DE CONDUCIR">LICENCIA DE CONDUCIR</option>
                        <option value="CARTILLA MILITAR">CARTILLA MILITAR</option>
                        <option value="CEDULA PROFESIONAL">CEDULA PROFESIONAL</option>
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputfolio_ine">Folio de Identificación</label>
                    <input name='folio_ine' id='folio_ine' type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputexpiracion_identificacion">Expiración de Identificación</label>
                    <input name='expiracion_identificacion' id='expiracion_identificacion' type="date" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputarch_ine">Archivo Identificación</label>
                    <input type="file" accept="application/pdf" class="form-control" id="arch_ine" name="arch_ine" placeholder="Archivo PDF">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputsexo">Sexo</label>
                    <select class="form-control" name="sexo" id="sexo">
                        <option value="">SELECCIONE</option>
                        <option value='MASCULINO'>Masculino</option>
                        <option value='FEMENINO'>Femenino</option>
                    </select>
                </div>
                <div class="form-gorup col-md-4">
                    <label for="inputestado_civil">Estado Civil</label>
                    <select class="form-control" name="estado_civil" id="estado_civil">
                        <option value="">SELECCIONE</option>
                        @foreach ($lista_civil as $item)
                            <option value="{{$item->nombre}}">{{$item->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputfecha_nacimiento">Fecha de Nacimiento</label>
                    <input name='fecha_nacimientoins' id='fecha_nacimientoins' type="date" class="form-control" aria-required="true">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Nacimiento</label>
                    <select class="form-control" name="entidad_nacimiento" id="entidad_nacimiento" onchange="local2_nacimiento()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}">{{$cadwell->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Nacimiento</label>
                    <select class="form-control" name="municipio_nacimiento" id="municipio_nacimiento" onchange="local_nacimiento()">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
                <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Nacimiento</label>
                    <select class="form-control" name="localidad_nacimiento" id="localidad_nacimiento">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-3">
                    <label for="inputentidad">Entidad de Residencia</label>
                    <select class="form-control" name="entidad" id="entidad" onchange="local2()">
                        <option value="">SELECCIONE</option>
                        @foreach ($estados as $cadwell)
                            <option value="{{$cadwell->id}}">{{$cadwell->nombre}}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label for="inputmunicipio">Municipio de Residencia</label>
                    <select class="form-control" name="municipio" id="municipio" onchange="local()">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
                <div class="form-gorup col-md-3">
                    <label for="inputlocalidad">Localidad de Residencia</label>
                    <select class="form-control" name="localidad" id="localidad">
                        <option value="sin especificar">Sin Especificar</option>
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="inputbanco">Dirección de Domicilio</label>
                    <input name="domicilio" id="domicilio" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-3">
                    <label for="inputbanco">Codigo Postal</label>
                    <input name="codigo_postal" id="codigo_postal" type="text" class="form-control" aria-required="true" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono Personal</label>
                    <input name="telefono" id="telefono" type="tel" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-4">
                    <label for="inputtelefono">Numero de Telefono de Casa</label>
                    <input name="telefono_casa" id="telefono_casa" type="tel" class="form-control" aria-required="true" required>
                </div>
                <div class="form-group col-md-4>
                    <label for="inputcorreo">Correo Electronico</label>
                    <input name="correo" id="correo" type="email" class="form-control" placeholder="correo_electronico@ejemplo.com" aria-required="true" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label for="inputbanco">Nombre del Banco</label>
                    <input name="banco" id="banco" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputclabe">Clabe Interbancaria</label>
                    <input name="clabe" id="clabe" type="text" class="form-control" aria-required="true">
                </div>
                <div class="form-group col-md-4">
                    <label for="inputnumero_cuenta">Numero de Cuenta</label>
                    <input name="numero_cuenta" id="numero_cuenta" type="text" class="form-control" aria-required="true">
                </div>
            </div>
            <hr style="border-color:dimgray">
            <div>
                <label><h2>Requisitos</h2></label>
            </div>
            <table class="table table-borderless table-responsive-md" id="table-perfprof2">
                <tbody>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Comprobante Domicilio</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_domicilio">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" id="arch_domicilio" name="arch_domicilio" placeholder="Archivo PDF">
                               <br><span id="imageName"></span>
                            </label>
                        </td>
                        <td id="center" width="60px">
                            <H5><small><small>CURP</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_curp">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curp" name="arch_curp" placeholder="Archivo PDF">
                               <br><span id="imageName2"></span>
                            </label>
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Bancario</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_banco">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_banco" name="arch_banco" placeholder="Archivo PDF">
                               <br><span id="imageName3"></span>
                            </label>
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Fotografía</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_foto">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="image/jpeg" class="form-control" id="arch_foto" name="arch_foto" placeholder="Archivo PDF">
                               <br><span id="imageName4"></span>
                            </label>
                        </td>
                    </tr>
                    <tr >
                        <td id="center" width="200px">
                            <H5><small><small>Acta de nacimiento</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_id">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_id" name="arch_id" placeholder="Archivo PDF">
                               <br><span id="imageName5"></span>
                            </label>
                        </td>
                        <td id="center" width="50px">
                            <H5><small><small>RFC</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_rfc">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_rfc" name="arch_rfc" placeholder="Archivo PDF">
                               <br><span id="imageName6"></span>
                            </label>
                        </td>
                        <td id="center" width="180px">
                            <H5><small><small>Comprobante Estudios</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_estudio">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_estudio" name="arch_estudio" placeholder="Archivo PDF">
                               <br><span id="imageName7"></span>
                            </label>
                        </td>
                        <td id="center" width="100px">
                            <H5><small><small>Curriculum</small></small></H5>
                        </td>
                        <td id="center" width="50px">
                            <i  class="fa fa-file-pdf-o  fa-2x fa-lg text-danger"></i>
                        </td>
                        <td id="center" width="160px">
                            <label class='onpoint' for="arch_curriculum_personal">
                                <a class="btn mr-sm-4 mt-3 btn-sm">
                                    Subir &nbsp; <i class="fa fa-2x fa-cloud-upload"></i>
                                </a>
                                <input style='display:none;' type="file" accept="application/pdf" class="form-control" id="arch_curriculum_personal" name="arch_curriculum_personal" placeholder="Archivo PDF">
                               <br><span id="imageName8"></span>
                            </label>
                        </td>
                    </tr>
                </tbody>
            </table>
            {{-- <div class="form-row">
                <div class="form-group col-md-6">
                    <label for="extracurricular"><h3>Registro de Capacitador Externo STPS</h3></label>
                    <textarea name="stps" id="stps" cols="6" rows="4" class="form-control"></textarea>
                </div>
                <div class="form-group col-md-6">
                    <label for="extracurricular"><h3>Estandar CONOCER</h3></label>
                    <textarea name="conocer" id="conocer" cols="6" rows="4" class="form-control"></textarea>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-12">
                    <label for="extracurricular"><h3>Datos Extracurriculares</h3></label>
                    <textarea name="extracurricular" id="extracurricular" cols="6" rows="10" class="form-control"></textarea>
                </div>
            </div> --}}
            <br>
            <div class="row">
                <div class="col-lg-12 margin-tb">
                    <div class="pull-left">
                        <a class="btn mr-sm-4 mt-3" href="{{URL::previous()}}">Regresar</a>
                    </div>
                    <div class="pull-right">
                        <button type="submit" class="btn mr-sm-4 mt-3" >Guardar</button>
                    </div>
                </div>
            </div>
            <br>
        </form>
    </div>
@endsection
@section('script_content_js')
    <script src="{{ asset("js/validate/orlandoBotones.js") }}"></script>
    <script>
        function local() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio").value;
            var datos = {valor: valor};
            console.log('hola');
            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad").empty();
                var selectL = document.getElementById('localidad'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad").value;
            var datos = {valor: valor};
            // console.log('hola');
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio").empty();
                var selectL = document.getElementById('municipio'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("municipio_nacimiento").value;
            var datos = {valor: valor};
            // console.log('hola');
            var url = '/instructores/busqueda/localidad';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#localidad_nacimiento").empty();
                var selectL = document.getElementById('localidad_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].clave;
                    newOption.text=respuesta[i].localidad;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        function local2_nacimiento() {
            // var x = document.getElementById("municipio").value;
            // console.log(x);

            var valor = document.getElementById("entidad_nacimiento").value;
            var datos = {valor: valor};
            // console.log('hola');
            var url = '/instructores/busqueda/municipio';
            var request = $.ajax
            ({
                url: url,
                method: 'POST',
                data: datos,
                dataType: 'json'
            });

            request.done(( respuesta) =>
            {
                $("#municipio_nacimiento").empty();
                var selectL = document.getElementById('municipio_nacimiento'),
                option,
                i = 0,
                il = respuesta.length;
                // console.log(il);
                // console.log( respuesta[1].id)
                for (; i < il; i += 1)
                {
                    newOption = document.createElement('option');
                    newOption.value = respuesta[i].id;
                    newOption.text=respuesta[i].muni;
                    // selectL.appendChild(option);
                    selectL.add(newOption);
                }
            });
        }

        let ardom = document.getElementById("arch_domicilio");
        let arcur = document.getElementById("arch_curp");
        let arban = document.getElementById("arch_banco");
        let arfot = document.getElementById("arch_foto");
        let arid = document.getElementById("arch_id");
        let arrfc = document.getElementById("arch_rfc");
        let arest = document.getElementById("arch_estudio");
        let aralt = document.getElementById("arch_curriculum_personal");
        let imageName = document.getElementById("imageName");
        let imageName2 = document.getElementById("imageName2");
        let imageName3 = document.getElementById("imageName3");
        let imageName4 = document.getElementById("imageName4");
        let imageName5 = document.getElementById("imageName5");
        let imageName6 = document.getElementById("imageName6");
        let imageName7 = document.getElementById("imageName7");
        let imageName8 = document.getElementById("imageName8");

        ardom.addEventListener("change", ()=>{
            let inputImage = document.querySelector("#arch_domicilio").files[0];
            imageName.innerText = inputImage.name;
        })
        arcur.addEventListener("change", ()=>{
            let inputImage2 = document.querySelector("#arch_curp").files[0];
            imageName2.innerText = inputImage2.name;
        })
        arban.addEventListener("change", ()=>{
            let inputImage3 = document.querySelector("#arch_banco").files[0];
            imageName3.innerText = inputImage3.name;
        })
        arfot.addEventListener("change", ()=>{
            let inputImage4 = document.querySelector("#arch_foto").files[0];
            imageName4.innerText = inputImage4.name;
        })
        arid.addEventListener("change", ()=>{
            let inputImage5 = document.querySelector("#arch_id").files[0];
            imageName5.innerText = inputImage5.name;
        })
        arrfc.addEventListener("change", ()=>{
            let inputImage6 = document.querySelector("#arch_rfc").files[0];
            imageName6.innerText = inputImage6.name;
        })
        arest.addEventListener("change", ()=>{
            let inputImage7 = document.querySelector("#arch_estudio").files[0];
            imageName7.innerText = inputImage7.name;
        })
        aralt.addEventListener("change", ()=>{
            let inputImage8 = document.querySelector("#arch_curriculum_personal").files[0];
            imageName8.innerText = inputImage8.name;
        })
    </script>
@endsection

