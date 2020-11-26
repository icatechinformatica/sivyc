<?php
namespace App\Excel;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use DB;
class xlsReportes implements FromCollection,WithHeadings
{
    
    public function __construct($data, $head)
    {        
        $this->data = $data;        
        $this->head = $head;        
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
         return $this->data;
    }
    
    public function sheets(): array
    {
        $sheets[] = new ProductsPerMonthSheet('REPORTES'); 
        $sheet->setColumnFormat(array('Y' => 'dd/mm/yyyy', ));               
        return $sheets;
    }
}