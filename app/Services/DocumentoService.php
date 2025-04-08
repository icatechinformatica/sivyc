<?php
namespace App\Services;

class DocumentoService
{
    protected function __construct()
    {

    }

    protected function generarDocumento(array $parameters = [])
    {
        $count = 0;
        $ccpHtml = ''; // Aquí se guarda el contenido generado en el foreach
        $validadores = ['DIRECTOR', 'DIRECTORA', 'ENCARGADO DE LA UNIDAD', 'ENCARGADA DE LA UNIDAD'];
        $ccpValidador = '';

        foreach ($parameters as $key => $info) {
            $val = $info['value'];
            if (!empty($info['upper'])) {
                $val = strtoupper($val);
            }
            $$key = htmlspecialchars($val);
        }

        $ccp = $this->setCpp($idUnidad);

        foreach ($ccp as $key => $value) {
            if ($count === 0) {
                $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Para su conocimiento. <br>';
            } elseif (
                !str_contains($value->cargo, 'DIRECTOR') &&
                !str_contains($value->cargo, 'DIRECTORA') &&
                !str_contains($value->cargo, 'ENCARGADO DE LA UNIDAD') &&
                !str_contains($value->cargo, 'ENCARGADA DE LA UNIDAD')
            ) {
                if ($key == 1) {
                    $ccpHtml .= 'Archivo / Minutario. <br>';
                }
                $ccpHtml .= htmlspecialchars($value->nombre) . '. ' . htmlspecialchars($value->cargo) . '. Mismo fin. <br>';
            }
            $count++;
        }


        foreach ($ccp as $v) {
            foreach ($validadores as $validador) {
                if (str_contains($v->cargo, $validador)) {
                    $ccpValidador .= 'Validó: ' . htmlspecialchars($v->nombre) . '. ' . htmlspecialchars($v->cargo) . '. <br>';
                    break;
                }
            }
        }

        $html = <<<HTML
        <div class="contenedor">
            <div class="bloque_dos" align="right" style="font-family: Arial, sans-serif; font-size: 14px;">
                <p class="delet_space_p color_text"><b>UNIDAD DE CAPACITACIÓN {$unidad}</b></p>
                <p class="delet_space_p color_text">MEMORÁNDUM No. {$memo}</p>
                <p class="delet_space_p color_text">{$mun}, CHIAPAS; <span class="color_text">{$fecha}</span></p>
            </div>
            <br>
            <div class="bloque_dos" align="left" style="font-family: Arial, sans-serif; font-size: 14px;">
                <p class="delet_space_p color_text"><b>{$tit} {$nom}</b></p>
                <p class="delet_space_p color_text"><b>{$car}</b></p>
                <p class="delet_space_p color_text"><b>PRESENTE.</b></p>
            </div>
            <div class="contenido" style="font-family: Arial, sans-serif; font-size: 14px; margin-top: 25px" align="justify">
                Por medio del presente, me permito enviar a usted el Concentrado de Ingresos Propios (FORMA RF-001) de la Unidad de Capacitación
                <span class="color_text"> {$unidad}, </span> correspondiente a la semana comprendida {$intervalo}.
                El informe refleja un total de \${$importe} ({$importeLetra}), mismo que se adjunta para su conocimiento y trámite correspondiente.
            </div>
            <br>
            <div class="tabla_alumnos">
                <p style="font-family: Arial, sans-serif; font-size: 14px;">Sin otro particular, aprovecho la ocasión para saludarlo.</p>
            </div>
            <br><br>
            <div class="ccp" style="font-size: 9px;">
                C.c.p <br>
                {$ccpHtml}
                <br>
                {$ccpValidador}
            </div>
        </div>
        HTML;

        return $html;
    }

    protected function setCpp($idUnidad)
    {
        $query = \DB::table('tbl_funcionarios as funcionario')
        ->join('tbl_organismos as organismos', 'funcionario.id_org', '=', 'organismos.id')
        ->select('funcionario.nombre', 'funcionario.id_org', 'organismos.id_parent', 'funcionario.cargo')
        ->where('funcionario.activo', 'true')
        ->where('funcionario.titular', true)
        ->where(function ($query) use ($idUnidad) {
            $query->where(function ($sub) use ($idUnidad) {
                $sub->where('organismos.id_unidad', $idUnidad)
                    ->where(function ($q) {
                        $q->where('funcionario.cargo', 'like', 'DELEG%')
                        ->orWhere('organismos.id_parent', 1);
                    });
            })
            ->orWhere('organismos.id_parent', 0)
            ->orWhere('funcionario.id_org', 13);
        })
        ->where(function ($query) {
            $query->whereNull('funcionario.incapacidad')
                ->orWhere('funcionario.incapacidad', '{}')
                ->orWhereNull(\DB::raw("funcionario.incapacidad->>'id_firmante'"));
        })
        ->orderBy('funcionario.id_org', 'asc')
        ->get();

        return $query;
    }
}
