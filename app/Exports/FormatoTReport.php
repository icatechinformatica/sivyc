<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;

class FormatoTReport implements FromCollection, WithHeadings, WithStrictNullComparison, WithTitle, WithEvents
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
        return $this->data;
    }

    public function sheets(): array
    {
        $sheets[] = new ProductsPerMonthSheet('REPORTE FORMATO T'); 
        $sheet->setColumnFormat(array('Y' => 'dd/mm/yyyy', ));               
        return $sheets;
    }

    /**
     * @return string
     */
    public function title(): string
    {
        return $this->title;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->getSheet()->autoSize();
                $event->getSheet()->getDelegate()->getStyle('A1:C11')
                    ->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            }
        ];
    }
}
