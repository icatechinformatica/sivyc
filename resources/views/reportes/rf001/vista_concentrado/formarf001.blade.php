 {{-- desarrollado y diseñado por MIS. DANIEL MÉNDEZ CRUZ - DERECHOS RESERVADOS ICATECH 2021 --}}
 @extends('reportes.rf001.plantilla.vertical_layout', ['title' => __('Reporte de Recorrido')])

 @section('name')
 @endsection

 @section('contenido_css')
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

 @section('contenido')
     {{-- contenido del documento --}}
     <table class="tabla_con_border">
         <tr>
             <td width="200px">FECHA DE ELABORACIÓN</td>
             <td width="700px" style="border: medium transparent" colspan="7"></td>
             <td colspan="3" width="200px;">SEMANA </td>
             <td colspan="13" style="border: inset 0pt"></td>
         </tr>
         <tr>
            <td>16/10/2023</td>
            <td style="border: medium transparent" colspan="7"></td>
            <td colspan="3">09/19/2023 AL 13/10/2023 </td>
            <td colspan="13" style="border: inset 0pt"></td>
        </tr>
     </table>
     <center class="espaciado"></center>
     <table class="tabla_con_border">
        <tr>
            <td colspan="3" style="text-align: center;">
                <b>DEPOSITO (S) EFECTUADO (S) A LA CUENTA BANCARIA:</b>
            </td>
        </tr>
        <tr>
            <td style="text-align: center;">
                NO. CUENTA 0119703322
            </td>
        </tr>
     </table>
     {{-- body table --}}
     <table class="tabla_con_border">
         <thead style="background-color:#f2f2f2;">
             <tr>
                <th style="text-align: center;"><b>MOVTO BANCARIO Y/O NÚMERO DE FOLIO</b></th>
                 <th style="text-align: center;"><b>N°. RECIBO Y/O FACTURA</b></th>
                 <th style="text-align: center;">CONCEPTO DE COBRO</th>
                 <th style="text-align: center;">IMPORTE</th>
             </tr>
         </thead>
         <tbody>
             @for ($i = 0; $i < 12; $i++)
                 <tr>
                     <td data-label="KM inicial" style="width: 55px; text-align: center;">

                     </td>
                     <td data-label="KM inicial" style="width: 40px; text-align: center;">

                     </td>
                     <td data-label="De:" style="width: 160px; text-align: left; font-size: 9px;">

                     </td>
                     <td data-label="Importe" style="width: 50px; text-align: center;">
                         xxxxxx
                     </td>
                 </tr>
             @endfor

             <tr>
                 <td>LITROS:</td>
                 <td><b></b></td>
                 <td><b>$ xxxx</b></td>
                 <td>$ xxxx</td>
             </tr>
         </tbody>
     </table>
     {{-- body table END --}}
     <table class="tabla_con_border">
         <tr>
             <td>OBSERVACIONES:</td>
         </tr>
         <tr>
            <td rowspan="3" style=" vertical-align: text-top;">OBSERVACIONES: <br>
                <div style="padding-top: 3px;">
                </div>
            </td>
         </tr>
     </table>
     {{-- firmar --}}
     {{-- NOTAS --}}
     <table class="tablad">
         <tr>
             <td colspan="3">NOTA: EL LLENADO DE ESTA BITACORA DEBERA SER EN BASE AL RECORRIDO PARA LLEVAR A CABO LA
                 COMISIÓN, EL CUAL SERA REVISADO POR EL AREA DE RECURSOS MATERIALES Y SERVICIOS.</td>
         </tr>
     </table>
     {{-- notas --}}
     <br>
     <table class="tablas_notas">
         <tr>
             <td colspan="3" style="text-align: center;">
                 <b>PARA USO EXCLUSIVO DEL AREA DE RECURSOS MATERIALES Y SERVICIOS</b>
             </td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; border-bottom: none; width:15%;">KM INICIAL:</td>
             <td style="border-left:none; width:20%; text-align: right; border-top:none;">
             </td>
             <td style="width:55%; border-bottom: none; border-top:none;">PUESTO DEL RESGUARDANTE: &nbsp;
             </td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; width:15%; border-bottom:none;">KM. FINAL:</td>
             <td style="border-left:none; width:20%; text-align: right;"></td>
             <td style="width:55%; border-bottom: none; border-top:none;">RESGUARDANTE: &nbsp;.</td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; width:15%; border-bottom:none;">KMS. RECORRIDOS:</td>
             <td style="border-left:none; width:20%; text-align: right;">

             </td>
             <td style="width:55%;border-top:none;">No. DE UNIDAD O ECONOMICO:</td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; width:15%; border-bottom:none;">LTS. DE GASOLINA:</td>
             <td style="border-left:none; width:20%; text-align: right;">
             </td>
             <td rowspan="3" style="border-bottom: none; vertical-align: text-top;">OBSERVACIONES: <br>
                 <div style="padding-top: 3px;">
                 </div>
             </td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; width:15%; border-bottom:none;">RENDIMIENTO POR LTS:</td>
             <td style="border-left:none; width:20%; text-align: right;">
             </td>
         </tr>
         <tr>
             <td style="border-top:none; border-right:none; width:15%; border-bottom:none;"></td>
             <td style="border-left:none; width:20%;"></td>
         </tr>
         <tr>
             <td colspan="2" style="text-align: center;">
                 <b>AREA DE RECURSOS MATERIALES Y SERVICIOS</b>
             </td>
             <td style="border-top:none;">
                 D.V: DIVISION DEL VALE.
             </td>
         </tr>
     </table>
     {{-- contenido del documento END --}}
 @endsection

 @section('contentJS')
     <script type="text/php">
        if (isset($pdf) ) {
            $font = $fontMetrics->getFont("helvetica", "bold");
            $pdf->page_text(370, 570, "Página {PAGE_NUM} de {PAGE_COUNT}", $font, 6, array(0,0,0));
        }
    </script>
 @endsection
