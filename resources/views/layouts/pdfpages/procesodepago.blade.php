<html>
    <body>
        <div class= "container g-pt-20">
            <div id="content">
                <div align=right>
                    <b>Unidad de Capacitación {{$data->unidad_capacitacion}}.</b>
                </div>
                <div align=right>
                    <b>Memorandum No. ICATECH/000/000/2020.</b>
                </div>
                <div align=right>
                    <b>{{$data->unidad_capacitacion}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b>
                </div>
                <br><br><b>Ing. Jorge Luis Barragán López.</b>
                <br>Jefe de Depto. de Recursos Financieros.
                <br>Presente.
                <br><p class="text-justify">en virtud de haber cumplido con los requisitos de apertura de curso y validación de instructor, solicito de la manera más atenta gire sus apreciables instrucciones a fin de que proceda el pago correspondiente al curso que a continuación se detalla:</p>
                <div align=center>
                    <FONT SIZE=2><b>DATOS DEL CURSO</b></FONT>
                </div>
                <table class="table table-responsive table-bordered">
                    <tbody>
                        <tr>
                            <td>Nombre del Curso: {{$data->curso}}</td>
                            <td>Clave del Curso: {{$data->clave}}</td>
                        </tr>
                        <tr>
                            <td>Especialidad: {{$data->espe}}</td>
                            <td>Modalidad: {{$data->mod}}</td>
                        </tr>
                        <tr>
                            <td>Fecha de Inicio y Término: {{$data->inicio}} AL {{$data->termino}}</td>
                            <td>Horario: {{$data->hini}} A {{$data->hfin}}</td>
                        </tr>
                    </tbody>
                </table>
                <div align=center>
                    <FONT SIZE=2> <b>DATOS DEL INSTRUCTOR</b></FONT>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td colspan="2">Nombre del Instructor: {{$data->nombre}} {{$data->apellidoPaterno}} {{$data->apellidoMaterno}}</td>
                        </tr>
                        <tr>
                            <td>Registro STPS:</td>
                            <td>Memorándum de Validación: {{$data->memoramdum_validacion}}</td>
                        </tr>
                        <tr>
                            <td>RFC: {{$data->rfc}}</td>
                            <td>Importe: {{$data->importe_total}}</td>
                        </tr>
                    </tbody>
                </table>
                <div align=center>
                    <FONT SIZE=2> <b>DATOS DE LA CUENTA PARA DEPOSITO O TRANSFERENCIA INTERBANCARIA</b></FONT>
                </div>
                <table>
                    <tbody>
                        <tr>
                            <td>Banco: {{$data->banco}}</td>
                        </tr>
                        <tr>
                            <td>Número de Cuenta: {{$data->no_cuenta}}</td>
                        </tr>
                        <tr>
                            <td>Clabe Interbancaria: {{$data->interbancaria}}</td>
                        </tr>
                    </tbody>
                </table>
                <br><p class="text-left"><p>Nota: El Expediente Único soporte documental del curso, obra en poder de la Unidad de Capacitación.</p></p>
                <br><br><table class="table1">
                    <tr>
                        <td colspan="2"><p align="center">Atentamente</p></td>
                        <td colspan="2"><p align="center">Autoriza</p></td>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">{{$data->nombre_director}}</td></div>
                        <td colspan="2"><div align="center">Mtro. Walter Dompinguez Camacho</td></div>
                    </tr>
                    <tr>
                        <td colspan="2"><div align="center">Director de la Unidad de Capacitación {{$data->unidad_capacitacion}}</td></div>
                        <td colspan="2"><div align="center">Director Administrativo</td></div>
                    </tr>
                </table>
                <br><br><br><p><FONT SIZE=1><b><C.c.p.</C></b>Mtra. Fabiola Lizbeth Astudillo Reyes.-Directora General del ICATECH.-Para su conocimiento<br/>
                <FONT SIZE=1><b><C.c.p.</C></b>Mtro. Walter Domínguez Camacho.-Director Administrativo.-mismo fin</FONT><br/>
                <FONT SIZE=1><b><C.c.p.</C></b>Nombre del Delegado Administrativo.-Delegado Administrativo.-mismo fin</FONT><br/>
                <FONT SIZE=1><b><C.c.p.</C></b>Archivo/ Minutario</FONT><br/>
                <FONT SIZE=1><b><C.c.p.</C></b>Elaboró: (nombre de quien Elabora)</FONT></p>
            </div>
        </div>
    </body>

    <script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
    <style>
        body{
            font-family: sans-serif;
          }
          @page {
            margin: 80px 30px;
          }
          header { position: fixed;
            left: 0px;
            top: -155px;
            right: 0px;
            height: 100px;
            background-color: #ddd;
            text-align: center;
          }
          header h1{
            margin: 10px 0;
          }
          header h2{
            margin: 0 0 10px 0;
          }
          footer {
            position: fixed;
            left: 0px;
            bottom: -50px;
            right: 0px;
            height: 10px;
            border-bottom: 2px solid #ddd;
          }
          footer .page:after {
            content: counter(page);
          }
          footer table {
            width: 10%;
          }
          footer p {
            text-align: right;
          }
          footer .izq {
            text-align: left;
          }
        table, td {
          border:1px solid black;
        }
        table {
          border-collapse:collapse;
          width:100%;
        }
        td {
          padding:px;
        }

        .table1, .table1 td {
            border:0px ;
        }
        .table1 td {
            padding:5px;
        }
        small {
            font-size: .7em
        }

    </style>

</html>
