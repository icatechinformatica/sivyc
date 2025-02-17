@extends('theme.sivyc.layout')
@section('content_script_css')
    <link rel="stylesheet" href="{{ asset('css/global.css') }}" />
    <style>
        .table-container {
            max-width: 100%;
            max-height: 400px;
            /* Ajusta la altura según necesites */
            overflow-y: auto;
            /* Scroll vertical */
            overflow-x: auto;
            /* Scroll horizontal si es necesario */
            border: 1px solid #ddd;
        }

        table {
            border: 1px solid #ccc;
            border-collapse: collapse;
            margin: 0;
            padding: 0;
            width: 100%;
            table-layout: fixed;
        }

        table caption {
            font-size: 1.5em;
            margin: .5em 0 .75em;
        }

        table tr {
            background-color: #f8f8f8;
            border: 1px solid #ddd;
            padding: .35em;
        }

        table th,
        table td {
            padding: .8em;
            text-align: center;
        }

        thead {
            position: sticky;
            top: 0;
            /* Asegura que el encabezado se pegue en la parte superior */
            z-index: 10;
            /* Asegura que el encabezado esté por encima del contenido */
            background-color: #009B85;
            /* Color de fondo del encabezado */
        }

        th {
            padding: .625em;
            text-align: center;
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        thead tr {
            height: 60px;
            background: #009B85;
            font-size: 16px;
            color: #ffffff;
        }

        table th {
            font-size: .85em;
            letter-spacing: .1em;
            text-transform: uppercase;
        }

        .search-box {
            width: 100%;
            max-width: 400px;
            margin-bottom: 10px;
        }

        @media screen and (max-width: 600px) {
            table {
                border: 0;
                width: 100%;
            }

            table caption {
                font-size: 1.3em;
                margin-bottom: 10px;
            }

            table thead {
                display: none;
            }

            table tr {
                border-bottom: 3px solid #ddd;
                display: block;
                margin-bottom: 0.625em;
            }

            table td {
                border-bottom: 1px solid #ddd;
                display: block;
                font-size: 0.8em;
                text-align: right;
                padding: 8px;
            }

            table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
                margin-right: 10px;
            }

            table td:last-child {
                border-bottom: 0;
            }
        }
    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')

    <div class="card-header">
        Generación de Código Qr
    </div>
    <div class="card card-body">
        <div class="row">
            <div class="col-10">
                <form action="{{ route('credencial.indice') }}" method="get">
                    <div class="d-flex align-items-center">
                        <input type="text" placeholder="Filtrar Registros por número de enlace / Nombre ..." class="form-control"
                            name="filtroBusqueda" id="filtroBusqueda" style="width:85%;">
                        <button class="btn">Filtrar</button>
                    </div>
                </form>
            </div>
        </div>
        <hr style="border-color:dimgray">
        <div class="table-container">
            <table class=“responsive-table”>

                <thead>
                    <tr>
                        <th>Nombre del Trabajador</th>
                        <th>Clave de Empleado</th>
                        <th>Puesto</th>
                        <th>Categoría</th>
                        <th>Detalles</th>
                    </tr>
                </thead>

                <tbody>
                    @if (count($query) > 0)
                        @foreach ($query as $item)
                            <tr>
                                <td data-label=Nombre del Trabajador>
                                    <strong>{{ $item->nombre_trabajador }}</strong>
                                </td>
                                <td data-label=Clave de Empleado>{{ $item->clave_empleado }}</td>
                                <td data-label=Puesto>{{ $item->puesto_estatal }}</td>
                                <td data-label=Categoría>{{ $item->categoria_estatal }}</td>
                                <td data-label=Detalles>
                                    <a class="nav-link pt-0" title="generar" href="{{ route('credencial.ver', ['id' => $item->id]) }}">
                                        <i class="fa fa-edit  fa-2x fa-lg text-success" aria-hidden="true"></i>
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    @else
                        <td data-label=“Tipo” colspan="5"><strong>¡NO HAY REGISTROS!</strong></td>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <td colspan='5'>
                            {{ $query->links() }}
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
        <br>
    </div>
@endsection
@section('script_content_js')
@endsection
