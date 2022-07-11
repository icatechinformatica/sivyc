<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
class ExportExcelPOA implements FromView
{
    public function __construct($data,$head,$title,$view)
    {   
        $this->head = $head;     
        $this->data = $data;
        $this->title = $title;
        $this->view = $view;
    }
    
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
        $sheets[] = new ProductsPerMonthSheet($this->title); 
        $sheet->setColumnFormat(array('Y' => 'dd/mm/yyyy', ));               
        return $sheets;
    }

    public function title(): string
    {
        return $this->title;
    }

    public function view(): View
    {
        $data = $this->data;
        return view($this->view,compact('data'));
    }    

}
