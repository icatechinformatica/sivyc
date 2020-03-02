<html>
<head>
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css" integrity="sha384-Vkoo8x4CGsO3+Hhxv8T/Q5PaXtkKtu6ug5TOeNV6gBiFeWPGFN9MuhOf23Q9Ifjh" crossorigin="anonymous">
  <style>
    body{
      font-family: sans-serif;
    }
    @page {
      margin: 160px 50px;
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
      height: 40px;
      border-bottom: 2px solid #ddd;
    }
    footer .page:after {
      content: counter(page);
    }
    footer table {
      width: 100%;
    }
    footer p {
      text-align: right;
    }
    footer .izq {
      text-align: left;
    }
  </style>
 <body>
    <header>
        <h1>Cabecera de mi documento</h1>
        <h2>DesarrolloWeb.com</h2>
    </header>
    <div class= "container g-pt-50">
 <footer>
    <table>
      <tr>
        <td>
            <p class="izq">
              Desarrolloweb.com
            </p>
        </td>
        <td>
          <p class="page">
            Página
          </p>
        </td>
      </tr>
    </table>
  </footer>
  <div id="content">
    <div align=right> <b>Unidad de Capacitacion.</b> </div>
    <div align=right> <b>Memorandum No. ICATECH/000/000/2020.</b></div>
    <div align=right> <b>Unidad, Chiapas 00 de Mes del 2020.</b></div>
    
    <br><br><b>Ing. Luis Alfonso Cruz Velasco.</b>
    <br>Jefe de Depto. de Programacion y Presupuesto.
    <br>Presente.

    <br><br><p class="text-justify">Por medio del presente me permito solicitar suficiencia presupuestal, en la partida 12101 Honorarios, para la contratacion de instructores para la imparticion de cursos de la Unidad de Capacitacion <b> La que sea </b>, de acuerdo a los numeros de folio que se indican en el cuadro analitico siguiente y acorde a lo que se describe en el formato anexo.</p>

    <table class="table table-bordered" id="table-one">
        
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
                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                {!! Form::submit('Borar', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            </td>
          </tr>
          <tr>
            <th scope="row">2</th>
            <td>Jacob</td>
            <td>Thornton</td>
            <td>@fat</td>
            <td>
                <a class="btn btn-info" href="">Mostrar</a>
                <a class="btn btn-primary" href="">Editar</a>
                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                {!! Form::submit('Borar', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
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
                {!! Form::open(['method' => 'DELETE','route' => ['usuarios'],'style'=>'display:inline']) !!}
                {!! Form::submit('Borar', ['class' => 'btn btn-danger']) !!}
                {!! Form::close() !!}
            </td>
          </tr>
        </tbody>
    </table>
    
    <br><p class="text-left"><p>Sin mas por el momento, aprovecho la ocacion para enviarle un cordial saludo.</p></p>
    <br><p class="text-left"><p>Atentamente.</p></p>
    <br><br><b>Nombre del Director</b>
    <br><b>Director(a) de la Unidad de Capacitacion (la que sea).</b>
    <br><br><br><br><br><h6><small><b>C.c.p. C.P. Nombre del director de planeacion.-Director de Planeacion.-Mismo Fin</b></small></h6>
    <h6><small><b>C.P.Jorge Luis Barragan Lopez.- Jefe del Depto. de Recursos Financieros.-Mismo Fin</b></small></h6>
    <h6><small><b>Archivo/Minutario<b></small></h6>
    <br><br><small><b>Valido: Nombre Completo.- Director de la Unidad<b></small></h6>
    <br><small><b>Valido: Nombre Completo.- Delegado Administrativo de la Unidad<b></small></h6>

  </div>
    </div>
 </body>
</html>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>