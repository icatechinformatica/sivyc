<?php

namespace App\Excel;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithColumnWidths;


class xlsTransferencia implements WithMultipleSheets, WithTitle, FromQuery, WithHeadings, WithEvents, WithColumnWidths
{
    use Exportable;
    protected $title;
    protected $data;

    public function __construct( $title, $data)
    {   
        $this->title = $title;
        $this->data = $data;
        $this->head = [
            'UNIDAD','CONTRATO','SOLICITUD DE PAGO','FECHA SOLICITUD','CLAVE','CURSO',
            'INSTRUCTOR','RFC','FOLIO FISCAL','BANCO','CUENTA','CLABE','IMPORTE','FECHA DE PAGO','#LAYOUT','ESTATUS'
        ];        
    }
   
    public function headings(): array
    {
        return   $this->head;        
    }

    public function columnWidths(): array
    {
        return [
            'A' => 15,
            'B' => 25,
            'C' => 25,
            'D' => 12,
            'E' => 25,
            'F' => 73,
            'G' => 38,
            'H' => 18,
            'I' => 40,
            'J' => 15,
            'K' => 15,
            'L' => 25,
            'M' => 15,            
            'N' => 12,
            'O' => 15,
            'P' => 15
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:P1')->applyFromArray([
                    'font'=>['bold'=>true,'color' => ['argb' => 'FFFFFF']],   
                    'fill' => ['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '621132']],
                    'borders' => [ 
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFFFFF'],
                        ],

                    ],
                ]);
            },
        ];
    }

    public function collection()
    {    
         return $this->data;
    }

    public function query()
    {        
        return  $this->data;
        
    }

    public function title(): string
    {
        return $this->title;

    }

    public function sheets():array
    {
        $sheets = [];
        $hojas = ['TRANSFERENCIAS'=>'TRANSFERENCIAS'];        
        $sheets[] = new xlsTransferencia ( 'SOLICITUDES DE PAGO', $this->data);        
        return $sheets;
    }


}
