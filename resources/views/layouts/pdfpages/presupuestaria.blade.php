<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/css/bootstrap.min.css">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.4.1/js/bootstrap.min.js"></script>
  <style>
    body{
      font-family: sans-serif;
    }
    @page {
      margin: 90px 50px;
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
</head>
 <body>

    <div class= "container g-pt-30">
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
  <div align=right> <b>Unidad de Capacitación {{$data_supre->unidad_capacitacion}}</b> </div>
    <div align=right> <b>Memorandum No. {{$data_supre->no_memo}}</b></div>
    <div align=right> <b>{{$data_supre->unidad_capacitacion}}, Chiapas {{$D}} de {{$M}} del {{$Y}}.</b></div>

    <br><br><b>{{$getdestino->nombre}} {{$getdestino->apellidoPaterno}} {{$getdestino->apellidoMaterno}}.</b>
    <br>{{$getdestino->puesto}}
    <br>Presente.

    <br><br><p class="text-justify">Por medio del presente me permito solicitar suficiencia presupuestal, en la partida 12101 Honorarios, para la contratacion de instructores para la imparticion de cursos de la Unidad de Capacitacion <b>{{$data_supre->unidad_capacitacion}}</b>, de acuerdo a los numeros de folio que se indican en el cuadro analitico siguiente y acorde a lo que se describe en el formato anexo.</p>
    <br><br><div align=center> <b>Números de Folio</b></div>

    <table class="table table-bordered">

        <thead>
        </thead>
        <tbody>
            @foreach ($data_folio as $key=>$value )
                @if ($key == 0 || $key == 3 || $key == 6 || $key == 9 || $key == 12 || $key == 15)
                <tr><td>{{$value->folio_validacion}}</td>
                @else
                <td>{{$value->folio_validacion}}</td>
                @endif
                @if ($key == 2 || $key == 5 || $key == 8 || $key == 11 || $key == 14)
                </tr>
                @endif
            @endforeach
          <tr>
        </tbody>
    </table>

    <br><p class="text-left"><p>Sin mas por el momento, aprovecho la ocacion para enviarle un cordial saludo.</p></p>
    <br><p class="text-left"><p>Atentamente.</p></p>
    <br><br><b>{{$getremitente->nombre}} {{$getremitente->apellidoPaterno}} {{$getremitente->apellidoMaterno}}</b>
    <br><b>{{$getremitente->puesto}} de la Unidad de Capacitacion {{$data_supre->unidad_capacitacion}}.</b>
    <br><br><br><br><br><h6><small><b>C.c.p. C.P. {{$getccp1->nombre}} {{$getccp1->apellidoPaterno}} {{$getccp1->apellidoMaterno}}.-{{$getccp1->puesto}}.-Mismo Fin</b></small></h6>
    <h6><small><b>C.P. {{$getccp2->nombre}} {{$getccp2->apellidoPaterno}} {{$getccp2->apellidoMaterno}}.-{{$getccp2->puesto}}.-Mismo Fin</b></small></h6>
    <h6><small><b>Archivo/Minutario<b></small></h6>
    <br><br><small><b>Valido: {{$getvalida->nombre}} {{$getvalida->apellidoPaterno}} {{$getvalida->apellidoMaterno}}.-{{$getvalida->puesto}}<b></small></h6>
    <br><small><b>Elaboró:  {{$getelabora->nombre}} {{$getelabora->apellidoPaterno}} {{$getelabora->apellidoMaterno}}.-{{$getelabora->puesto}}<b></small></h6>

  </div>
    </div>
 </body>
</html>
<script src="https://code.jquery.com/jquery-3.4.1.slim.min.js" integrity="sha384-J6qa4849blE2+poT4WnyKhv5vZF5SrPo0iEjwBvKU7imGFAV0wwj1yYfoRSJoZ+n" crossorigin="anonymous"></script>
<script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/js/bootstrap.min.js" integrity="sha384-wfSDF2E50Y2D1uUdj0O3uMBJnjuUD4Ih7YwaYd1iqfktj0Uod8GCExl3Og8ifwB6" crossorigin="anonymous"></script>
