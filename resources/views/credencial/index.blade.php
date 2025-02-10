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
            background-color: #FFED86;
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
            background: #FFED86;
            font-size: 16px;
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
            }

            table caption {
                font-size: 1.3em;
            }

            table thead {
                border: none;
                clip: rect(0 0 0 0);
                height: 1px;
                margin: -1px;
                overflow: hidden;
                padding: 0;
                position: absolute;
                width: 1px;
            }

            table tr {
                border-bottom: 3px solid #ddd;
                display: block;
                margin-bottom: .625em;
            }

            table td {
                border-bottom: 1px solid #ddd;
                display: block;
                font-size: .8em;
                text-align: right;
            }

            table td::before {
                content: attr(data-label);
                float: left;
                font-weight: bold;
                text-transform: uppercase;
            }

            table td:last-child {
                border-bottom: 0;
            }
        }
    </style>
@endsection
@section('title', 'Formatos Rf001 enviados a revisión | SIVyC Icatech')
@section('content')
    <div class="card card-body">
        <h3 class="text-center text-muted font-weight-bold">Generación de código QR</h2>
            <div class="row">
                <div class="form-group col-md-12">
                    <form action="{{ route('credencial.index') }}" method="get">
                        <div class="d-flex align-items-center">
                            <input type="text" placeholder="Buscar Registros de los Funcionarios Icatech ..."
                                class="form-control" name="filtroBusqueda" id="filtroBusqueda" style="width:92%;">
                            <button class="btn">Filtrar</button>
                        </div>
                    </form>
                    <br>
                    <!-- Input para el filtro -->
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

                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan='5'>

                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </div>
    </div>
@endsection
@section('script_content_js')
@endsection
