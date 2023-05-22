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

use App\Models\curso;

class xlsCursosDV implements WithMultipleSheets, WithTitle, FromQuery, WithHeadings, WithEvents, WithColumnWidths
{
    use Exportable;
    protected $title;

    public function __construct( $title)
    {   
        $this->title = $title;
        $this->head = [
            'CATEGORIA','ESPECIALIDAD','NOMBRE','HORAS','OBJETIVO',
            'PERFIL DE INGRESO SUGERIDO','TIPO CAPACITACION','MODALIDAD','CLASIFICACION',
            'COSTO','SERVICIO','PROYECTO','UNIDADES DISPONIBLES'
        ];
        if($this->title=='PROGRAMA ESTRATÉGICO')array_push($this->head, "DEPENDENCIAS");
    }
   
    public function headings(): array
    {
        return   $this->head;        
    }

    public function columnWidths(): array
    {
        return [
            'A' => 25,
            'B' => 25,
            'C' => 50,
            'D' => 8,
            'E' => 50,
            'F' => 40,
            'G' => 15,
            'H' => 8,
            'I' => 10,
            'J' => 8,
            'K' => 8,
            'L' => 8,
            'M' => 100,
            'N' => 100,
        ];
    }


    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:M1')->applyFromArray([
                    'font'=>['bold'=>true,'color' => ['argb' => 'FFFFFF']],   
                    'fill' => ['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '621132']],
                    'borders' => [ 
                        'outline' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                            'color' => ['argb' => 'FFFFFFF'],
                        ],

                    ],
                ]);
                if($this->title=='PROGRAMA ESTRATÉGICO'){
                    $event->sheet->getStyle('N1')->applyFromArray([
                        'font'=>['bold'=>true,'color' => ['argb' => 'FFFFFF']],   
                        'fill' => ['fillType' => 'solid','rotation' => 0, 'color' => ['rgb' => '621132']],
                        'borders' => [ 
                            'outline' => [
                                'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THICK,
                                'color' => ['argb' => 'FFFFFFF'],
                            ],
    
                        ],
                    ]);
                }

            },
        ];
    }

    public function query()
    {
        $data  = curso::query();   
        switch ($this->title){            
            case "CERTIFICACIÓN EXTRAORDINARIA":                             
                $data->where( 'cursos.proyecto', false)->where( 'cursos.servicio', 'LIKE', "%CERTIFICACION%");
            break;
            case "PROGRAMA ESTRATÉGICO":                
                $data->where( 'cursos.proyecto', true);
                
            break;
            default:                
                $data->where( 'cursos.proyecto', false)->where( 'cursos.servicio', 'LIKE', "%CURSO%");
            break;           
        }
        $data->where('cursos.estado', '=', true)->join('especialidades','especialidades.id','cursos.id_especialidad');
        
    
        if($this->title=='PROGRAMA ESTRATÉGICO') $data->select('categoria','especialidades.nombre','nombre_curso','horas','objetivo','perfil','tipo_curso','modalidad','clasificacion','costo',\DB::raw("TRANSLATE(cursos.servicio::TEXT,'[\"\"]','') as servicio"),\DB::raw("CASE WHEN proyecto=true THEN 'SI' ELSE 'NO' END "),\DB::raw("TRANSLATE(cursos.unidades_disponible::TEXT,'[\"\"]','') as unidades_disponibles"),\DB::raw("TRANSLATE(cursos.dependencia::TEXT,'[\"\"]','') as dependencias"));
        else $data->select('categoria','especialidades.nombre','nombre_curso','horas','objetivo','perfil','tipo_curso','modalidad','clasificacion','costo',\DB::raw("TRANSLATE(cursos.servicio::TEXT,'[\"\"]','') as servicio"),\DB::raw("CASE WHEN proyecto=true THEN 'SI' ELSE 'NO' END "),\DB::raw("TRANSLATE(cursos.unidades_disponible::TEXT,'[\"\"]','') as unidades_disponibles"));
        return  $data;
        
    }

    public function title(): string
    {
        return $this->title;

    }

    public function sheets():array
    {
        $sheets = [];
        $hojas = ['CURSOS'=>'CURSOS','CERTIFICACION'=>'CERTIFICACIÓN EXTRAORDINARIA','PROGRAMA'=>'PROGRAMA ESTRATÉGICO'];        
        $sheets[] = new xlsCursosDV ( $hojas[$this->title]);        
        return $sheets;
    }


}
