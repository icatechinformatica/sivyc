@extends('theme.sivyc.layout')
<!--llamar a la plantilla -->
@section('title', 'Plantilla de demostración | Sivyc Icatech')
<!--seccion-->
@section('content')
    <style>
        * {
        box-sizing: border-box;
        }

        #myInput {
        background-image: url('img/search.png');
        background-position: 5px 10px;
        background-repeat: no-repeat;
        background-size: 32px;
        width: 100%;
        font-size: 16px;
        padding: 12px 20px 12px 40px;
        border: 1px solid #ddd;
        margin-bottom: 12px;
        }
    </style>
    <div class="container g-pt-50">
        <div class="row">

            <div class="col-lg-12 margin-tb">
                <div class="pull-left">
                    <h2>Registros</h2>
                </div>
                <div class="pull-right">
                    <a class="btn btn-success" href="#"> Nuevo</a>
                </div>
            </div>
        </div>
        <input type="text" class="form-control" id="myInput" onkeyup="myFunction()" placeholder="Busqueda por cursos.">
        <br>
        <table width="70%" class="table table-bordered" id="table-one">
            <caption>Lista de Cursos</caption>
            <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">First</th>
                <th scope="col">Last</th>
                <th scope="col">Handle</th>
                <th width="280px">Action</th>
              </tr>
            </thead>
            <tbody>
              <tr>
                <th scope="row">1</th>
                <td>Mark</td>
                <td>Otto</td>
                <td>@mdo</td>
                <td>
                    <a class="btn btn-info" href="">Mostrar</a>
                    <a class="btn btn-primary" href="">Editar</a>
                </td>
              </tr>
              <tr>
                <th scope="row">2</th>
                <td>Jacob</td>
                <td>Thornton</td>
                <td>@fat</td>
                <td>
                </td>
              </tr>
              <tr>
                <th scope="row">3</th>
                <td>Larry</td>
                <td>the Bird</td>
                <td>@twitter</td>
                <td>
                    <a class="btn btn-info" href="">Mostrar</a>
                    <a class="btn btn-primary" href="">Editar</a>
                </td>
              </tr>
            </tbody>
        </table>
    </div>
@endsection
