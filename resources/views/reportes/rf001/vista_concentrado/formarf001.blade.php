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

         .contenedor {
             margin-left: 1cm;
             margin-right: 1cm;
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
         $html_sin_saltos = str_replace(["\r", "\n"], '', $bodyRf001);
     @endphp
     {!! $html_sin_saltos !!}
     <br>
     @if (!is_null($uuid))
         <div class="contenedor">
             <table style="width: 100%; font-size: 10px;">
                 @foreach ($objeto['firmantes']['firmante'][0] as $key => $moist)
                     <tr>
                         <td style="width: 10%; font-size: 9px;"><b>Nombre del firmante:</b></td>
                         <td style="width: 90%; font-size: 9px;">{{ $moist['_attributes']['nombre_firmante'] }}</td>
                     </tr>
                     <tr>
                         <td style="vertical-align: top; font-size: 9px;"><b>Firma Electrónica:</b></td>
                         <td style="font-size: 9px;">
                             {{ wordwrap($moist['_attributes']['firma_firmante'], 87, "\n", true) }}
                         </td>
                     </tr>
                     <tr>
                         <td style="font-size: 9px;"><b>Puesto:</b></td>
                         <td style="font-size: 9px; height: 25px;">{{ $puestos[$key] }}</td>
                     </tr>
                     <tr>
                         <td style="font-size: 9px;"><b>Fecha de Firma:</b></td>
                         <td style="font-size: 9px;">{{ $moist['_attributes']['fecha_firmado_firmante'] }}</td>
                     </tr>
                     <tr>
                         <td style="font-size: 9px;"><b>Número de Serie:</b></td>
                         <td style="font-size: 9px;">{{ $moist['_attributes']['no_serie_firmante'] }}</td>
                     </tr>
                 @endforeach
             </table>
             <div style="display: inline-block; width: 16%;">
                 {{-- <img style="position: fixed; width: 100%; top: 55%; left: 80%" src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR"> --}}
                 <img style="position: fixed; width: 16%; top: 60%; left: 77%"
                     src="data:image/png;base64,{{ $qrCodeBase64 }}" alt="Código QR">
             </div>
         </div>
     @endif
 @endsection

 @section('script_content_js')
     {{-- <script type="text/php">
        if (isset($pdf) ) {
            $font = $fontMetrics->getFont("helvetica", "bold");
            $pdf->page_text(370, 570, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script> --}}
 @endsection
