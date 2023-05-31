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

class xlsCursosMultiple implements WithMultipleSheets, WithTitle, FromQuery, WithHeadings, WithEvents, WithColumnWidths
{
    use Exportable;
    protected $title;

    public function __construct( $title)
    {        
        /*$this->data = $data;        
        $this->head = $head;     
        $this->title = $title;*/
       
        $this->title = $title;
        $this->head = [
            'CAMPO','CATEGORIA','ESPECIALIDAD','CURSO','HORAS','OBJETIVO',
            'PERFIL DE INGRESO SUGERIDO','SOLICITUD AUTORIZACION DE RIESGO',
            'TIPO CAPACITACION','MODALIDAD','CLASIFICACION',
            'COSTO','CRITERIO DE PAGO MINIMO','NOMBRE CRITERIO MINIMO','CRITERIO DE PAGO MAXIMO',
            'NOMBRE DE CRITERIO MAXIMO','UNIDADES DISPONIBLES'
        ];
    }
   
    public function headings(): array
    {
        return   $this->head;        
    }

    public function columnWidths(): array
    {
        return [ 'A' => 25, 'B' => 25,'C' => 25, 'D' => 50,'E' => 8,'F' => 50, 'G' => 35, 'H' => 15, 'I' => 10,
            'J' => 10, 'K' => 15, 'L' => 8, 'M' => 8, 'N' => 30, 'O' => 8, 'P' => 30, 'Q' => 100 ];
    }

    public function query()
    {
        $data  = curso::query()->where('cursos.estado', '=', true);
        switch ($this->title){            
            case 1:
                $data->where( 'cursos.proyecto', false)->where( 'cursos.servicio', 'LIKE', "%CERTIFICACION%");
            break;
            case 2:
                $data->where( 'cursos.proyecto', true);
            break;
            default:
                $data->where( 'cursos.proyecto', false)->where( 'cursos.servicio', 'LIKE', "%CURSO%");
            break;           
        }
        
        $data->join('especialidades','especialidades.id','cursos.id_especialidad')->leftjoin('area', 'area.id', 'especialidades.id_areas')
            ->select('area.formacion_profesional','categoria','especialidades.nombre','nombre_curso','horas','objetivo','perfil',
            \DB::raw("(case when cursos.solicitud_autorizacion = 'true' then 'SI' else 'NO' end) as etnia"),'tipo_curso','modalidad','clasificacion','costo',
            'cursos.rango_criterio_pago_minimo',\DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_minimo) as mini"),
            'cursos.rango_criterio_pago_maximo',\DB::raw("(select perfil_profesional from criterio_pago where id = rango_criterio_pago_maximo) as maxi"),            
            \DB::raw("TRANSLATE(cursos.unidades_disponible::TEXT,'[\"\"]','') as unidades_disponibles"));

        return  $data;
        
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $event->sheet->getStyle('A1:Q1')->applyFromArray([
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

    public function title(): string
    {
        $hojas = [0=>'CURSOS',1=>'CERTIFICACIÓN EXTRAORDINARIA',2=>'PROGRAMA ESTRATÉGICO'];
        return $hojas[$this->title];

    }

    public function sheets():array
    {
        /*
        $sheets = [];
        $hojas = ['CURSOS','CERTIFICACIÓN EXTRAORDINARIA','PROGRAMA ESTRATÉGICO'];
        for($n = 0; $n < 3; $n++){
            $sheets[] = new xlsCursosMultiple ( $hojas[$n]);
        }
        return $sheets;
        */
               
        return collect(range(0,2))->map(function($hojas){
            return new xlsCursosMultiple ( $hojas);
        })->toArray();
    }


}
