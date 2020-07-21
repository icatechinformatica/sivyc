<!--plantilla trabajada por DANIEL MENDEZ CRUZ-->
@extends('theme.sivyc.layout')
<!--generado por Daniel Méndez-->
@section('title', 'Alumnos Matriculados | Sivyc Icatech')
<!--contenido-->
@section('content')
    <div class="container g-pt-50">
        <div style="text-align: left;">
            <h2><b>Indicadores Historico Cursos vs Objetivo</b></h2>
        </div>
        <div class="form-row">
            <!--nombre aspirante-->
            <div class="form-group col-md-5">
                <table class="table">
                    <thead class="thead-dark">
                      <tr>
                        <th scope="col">UNIDAD CENTRAL</th>
                        <th scope="col">OBJETIVOS CURSOS ABIERTOS</th>
                        <th scope="col">REALIZADOS CURSOS ABIERTOS</th>
                        <th scope="col">DIFERENCIA</th>
                      </tr>
                    </thead>
                    <tbody>
                      <tr>
                        <th scope="row">TUXTLA</th>
                        <td>1412</td>
                        <td>216</td>
                        <td>1196</td>
                      </tr>
                      <tr>
                        <th scope="row">SAN CRISTOBAL</th>
                        <td>781</td>
                        <td>30</td>
                        <td>751</td>
                      </tr>
                      <tr>
                        <th scope="row">YAJALON</th>
                        <td>468</td>
                        <td>45</td>
                        <td>423</td>
                      </tr>
                      <tr>
                        <th scope="row">TONALA</th>
                        <td>420</td>
                        <td>30</td>
                        <td>390</td>
                      </tr>
                      <tr>
                        <th scope="row">TAPACHULA</th>
                        <td>423</td>
                        <td>42</td>
                        <td>381</td>
                      </tr>
                      <tr>
                        <th scope="row">REFORMA</th>
                        <td>379</td>
                        <td>40</td>
                        <td>339</td>
                      </tr>
                      <tr>
                        <th scope="row">CATAZAJA</th>
                        <td>351</td>
                        <td>15</td>
                        <td>336</td>
                      </tr>
                      <tr>
                        <th scope="row">JIQUIPILAS</th>
                        <td>372</td>
                        <td>57</td>
                        <td>315</td>
                      </tr>
                      <tr>
                        <th scope="row">COMITAN</th>
                        <td>362</td>
                        <td>52</td>
                        <td>310</td>
                      </tr>
                      <tr>
                        <th scope="row">VILLAFLORES</th>
                        <td>339</td>
                        <td>45</td>
                        <td>294</td>
                      </tr>
                      <tr>
                        <th scope="row">OCOSINGO</th>
                        <td>197</td>
                        <td>11</td>
                        <td>186</td>
                      </tr>
                    </tbody>
                    <tfoot>
                        <tr>
                          <td>TOTAL:</td>
                          <td>$180</td>
                          <td>$180</td>
                          <td>$180</td>
                        </tr>
                    </tfoot>
                  </table>
            </div>
            <div class="form-group col-md-7">
                <!--Div that will hold the pie chart-->
                <div id="columnchart_material" style="width: 850px; height: 600px;"></div>
            </div>
        </div>
        <div class="form-row">

            <div class="form-group col-md-3">
                <label for="curp" class="control-label">CURP:  </label>
            </div>
            <div class="form-group col-md-3">
                <label for="fecha_nacimiento" class="control-label">Fecha de Nacimiento:  </label>
            </div>
            <div class="form-group col-md-3">
                <label for="telefono" class="control-label">Teléfono:  </label>
            </div>
            <div class="form-group col-md-3">
                <label for="cp" class="control-label">C.P.  </label>
            </div>
        </div>

        <!---->
    </div>
@endsection
