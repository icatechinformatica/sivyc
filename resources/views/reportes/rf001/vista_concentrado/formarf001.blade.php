 {{-- desarrollado y diseñado por MIS. DANIEL MÉNDEZ CRUZ - DERECHOS RESERVADOS ICATECH 2021 --}}
 @extends('theme.formatos.vlayout')
 @section('title', 'Soporte de Entrega | SIVyC Icatech')

 @section('content_script_css')
     <style>
         table,
         th,
         td {
             /* border: 1px solid black; */
             border-collapse: collapse;
             border: none;
         }

         th,
         td {
             padding: 2px;
         }

         th {
             text-align: left;
         }

         .tablas {
             width: 100%
         }

         .tablas tr td {
             font-size: 11px;
             border: none;
             text-align: center;
             padding: 1px 1px;
         }

         .tablas th {
             font-size: 8px;
             text-align: center;
             padding: 1px 1px;
         }

         .tabla_con_border {
             border-collapse: collapse;
             font-size: 10px;
             font-family: sans-serif;
             width: 100%;
         }

         .tabla_con_border tr td {
             border: 1px solid #000000;
             word-wrap: break-word;
         }

         /* .tabla_con_border td {
                                                                                         page-break-inside: avoid;
                                                                                     } */

         .tabla_con_border thead tr th {
             border: 1px solid #000000;
         }

         /* espaciado */
         center.espaciado {
             padding-top: 10px;
             font-size: 11px;
         }

         .celdaAsignado {
             word-break: break-all !important;
             white-space: nowrap !important;
             width: 40px !important;
         }

         .tablas_notas {
             border-collapse: collapse;
             width: 100%;
             page-break-before: auto;
         }

         .tablas_notas tr td {
             font-size: 9px;
             border: #000000 1.8px solid;
             text-align: justify;
             height: 2em;
         }

         .tablas_notas th {
             font-size: 9px;
             border: gray 1px solid;
             text-align: justify;
             padding: 1px 1px;
         }

         /* tablas para firmas */
         .tablaf {
             border-collapse: collapse;
             width: 100%;
             margin-top: 1em;
         }

         .tablaf tr td {
             font-size: 8px;
             text-align: center;
             padding: 0px 0px;
         }

         /* tabla para notas */
         .tablad {
             border-collapse: collapse;
             width: 100%;
             margin-top: 0.6em;
         }

         .tablad tr td {
             font-size: 8px;
             text-align: left;
             padding: 2px;
         }

         .page-break {
             page-break-after: always;
         }

         body {
             padding-bottom: 60px;
         }
     </style>
 @endsection

 @section('content')
     @php
         $movimiento = json_decode($data->movimientos, true);
         $importeTotal = 0;
         $periodoInicio = Carbon\Carbon::parse($data->periodo_inicio);
         $periodoFin = Carbon\Carbon::parse($data->periodo_fin);
         $dateCreacion = Carbon\Carbon::parse($data->created_at);
         $dateCreacion->locale('es'); // Configurar el idioma a español
         $nombreMesCreacion = $dateCreacion->translatedFormat('F');
     @endphp
     {{-- contenido del documento --}}
     <table class="tabla_con_border" style="padding-top: 20px;">
         <tr>
             <td width="200px">FECHA DE ELABORACIÓN</td>
             <td width="750px" style="border-top-style: none; border-bottom-style: none; border-left-style: dotted;" colspan="8"></td>
             <td width="200px;" style="text-align:center;">SEMANA </td>
             <td colspan="13" style="border: inset 0pt"></td>
         </tr>
         <tr>
             <td style="text-align:center;">{{ Carbon\Carbon::parse($data->created_at)->format('d/m/Y') }}</td>
             <td colspan="8" style="border-top-style: none; border-bottom-style: none; border-left-style: dotted;"></td>
             <td style="text-align:center;">{{ $periodoInicio->format('d/m/Y') . ' AL ' . $periodoFin->format('d/m/Y') }}
             </td>
             <td colspan="13" style="border: inset 0pt"></td>
         </tr>
     </table>
     <center class="espaciado"></center>
     <table class="tabla_con_border">
         <tr>
             <td style="text-align: center;">
                 <b>DEPOSITO (S) EFECTUADO (S) A LA CUENTA BANCARIA:</b>
             </td>
         </tr>
         <tr>
             <td style="text-align: center;">
                 NO. CUENTA {{ $cuenta }}
             </td>
         </tr>
     </table>
     {{-- body table --}}
     <table class="tabla_con_border">
         <thead>
             <tr>
                 <th style="text-align: center;" width="40px"><b>MOVTO BANCARIO Y/O <br> NÚMERO DE FOLIO</b></th>
                 <th style="text-align: center;" width="100px"><b>N°. RECIBO Y/O FACTURA</b></th>
                 <th style="text-align: center;">CONCEPTO DE COBRO</th>
                 <th style="text-align: center;">IMPORTE</th>
             </tr>
         </thead>
         <tbody>
             @foreach ($movimiento as $item)
                 @php
                     $depositos = isset($item['depositos']) ? json_decode($item['depositos'], true) : [];
                 @endphp
                 <tr>
                     <td data-label="KM inicial" style="width: 55px; text-align: center;">
                         {{ $item['folio'] }}
                     </td>
                     <td data-label="KM inicial" style="width: 40px; text-align: center;">
                         @foreach ($depositos as $k)
                             {{ $k['folio'] }} &nbsp;
                         @endforeach
                     </td>
                     <td data-label="De:" style="width: 160px; text-align: left; font-size: 9px;">
                         @if ($item['curso'] != null)
                             {{ $item['curso'] }}
                         @else
                             {{ $item['descripcion'] }}
                         @endif
                     </td>
                     <td data-label="Importe" style="width: 50px; text-align: center;">
                         ${{ number_format($item['importe'], 2, '.', ',') }}
                     </td>
                 </tr>
                 @php
                     $importeTotal += $item['importe'];
                 @endphp
             @endforeach
             <tr>
                 <td></td>
                 <td><b></b></td>
                 <td><b></b></td>
                 <td style="text-align:center;">
                     <b>$ {{ number_format($importeTotal, 2, '.', ',') }}</b>
                 </td>
             </tr>
         </tbody>
     </table>
     <center class="espaciado"></center>
     {{-- body table END --}}
     <table class="tabla_con_border">
         <tr>
             <td colspan="3">OBSERVACIONES:</td>
         </tr>
         <tr>
             <td colspan="3" style=" vertical-align: text-top;"><b>SE ENVIAN FICHAS DE DEPOSITO:</b> <br>
                 <div style="padding-top: 3px;">
                     @foreach ($movimiento as $k)
                         {{ $k['folio'] }},
                     @endforeach
                     <p>
                         <b>RECIBO OFICIAL: &nbsp;</b>
                         @foreach ($movimiento as $v)
                             @php
                                 $deposito = isset($v['depositos']) ? json_decode($v['depositos'], true) : [];
                             @endphp
                             @foreach ($deposito as $j)
                                 {{ $j['folio'] }},
                             @endforeach
                         @endforeach
                         &nbsp;
                         <b>{{ $dateCreacion->day ."/". Str::upper($nombreMesCreacion)."/".$dateCreacion->year}}</b>
                     </p>
                 </div>
             </td>
         </tr>
         <tr>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
             <td>&nbsp;</td>
         </tr>
     </table>
     {{-- firmar --}}
     {{-- contenido del documento END --}}
 @endsection

 @section('script_content_js')
     {{-- <script type="text/php">
        if (isset($pdf) ) {
            $font = $fontMetrics->getFont("helvetica", "bold");
            $pdf->page_text(370, 570, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script> --}}
 @endsection
