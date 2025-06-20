<?php

namespace App\Excel;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Color;

class xls implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithEvents
{
    public function __construct($data, $head, $title)
    {        
        $this->data = $data;        
        $this->head = $head;     
        $this->title = $title;   
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function headings(): array
    {
        return   $this->head;        
    }

    public function collection()
    {
        //
        //dd($this->data);
        return $this->data;
    }

    public function sheets(): array
    {
        $sheets[] = new ProductsPerMonthSheet($this->title); 
        //$sheet->setColumnFormat(array('Y' => 'dd/mm/yyyy', ));               
        return $sheets;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    public function registerEvents(): array{
        return [
                AfterSheet::class => function(AfterSheet $event) {
                    // Establecer color de fondo y color de texto para el encabezado
                       
                    
                    // Establecer ancho de columnas
                    $contador = 0;
                    //foreach (range('A', 'Z') as $columnID) {
                    foreach ($this->excelColumnRange('A', 'AZ') as $columnID) {
                        $contador++;
                        $event->sheet->getColumnDimension($columnID)->setAutoSize(true);
                        $col2 = $columnID."1";
                        if(count($this->head) == $contador) break;
                    }

                    $event->sheet->getStyle('A1:'.$col2)->applyFromArray([
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF'], // Color de texto (blanco)
                        ],
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => '621132'], // Color de fondo (azul)
                        ],
                    ]); 
                },
            ];
    }

    function excelColumnRange($start, $end) {
        $columns = [];
        $current = $start;

        while ($current !== $end) {
            $columns[] = $current;
            $current++;
        }

        // Incluir el final también
        $columns[] = $end;

        return $columns;
    }
        
}
