<?php
namespace App\Utilities;

class MyUtility
{
    public static function ejercicios()
    {
        $anioActual = date('Y'); 
            $anios = [];
            for ($i = -2; $i <= 0; $i++) {
                $anio = $anioActual + $i;
                $anios[$anio] = $anio;
            }
        return $anios;
    }

    public static function letras($cantidad){
        $unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];
        $especiales = ["diez", "once", "doce", "trece", "catorce", "quince"];
        $centenas = ["", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
    
        $entero = floor($cantidad);
        $decimal = round(($cantidad - $entero) * 100);    
        $pesos = ($entero == 1) ? "peso" : "pesos";
        $centavos = ($decimal == 1) ? "centavo" : "centavos";    
        $parteEntera = "";
        $parteDecimal = "";
    
        if ($entero >= 1 && $entero <= 999999999) {
            $millones = floor($entero / 1000000);
            $millar = floor(($entero % 1000000) / 1000); //dd($millar);
            $centena =  floor(($entero % 1000) / 100); //dd($centena);            
            $decena = floor(($entero % 100) / 10); //dd($decena);
            $unidad = $entero % 10;

            if ($millones > 0) $parteEntera .= $this->letras($millones) . " millón ";
            if ($millar > 0) {
                if ($millar == 1) $parteEntera .= "mil ";
                else $parteEntera .= $unidades[$millar] . " mil ";            
            }
            if ($centena > 0) $parteEntera .= $centenas[$centena] . " ";
            if ($decena > 0) $parteEntera .= $decenas[$decena] . " ";
            if ($unidad > 0) {
                if ($unidad == 1) $parteEntera .= "un ";                
                if ($unidad >= 2 && $unidad <= 9) $parteEntera .= $unidades[$unidad] . " ";                
            }    
            $parteEntera .= " ";
        } else $parteEntera = "No soportado";
        
        if ($decimal > 0) {
            if ($decimal >= 10 && $decimal <= 15) {
                $parteDecimal .= $especiales[$decimal - 10];
            } else {
                $d = floor($decimal / 10);
                $u = $decimal % 10;    
                if ($d > 0) $parteDecimal .= $decenas[$d] . " y ";                
                if ($u > 0) $parteDecimal .= $unidades[$u];                
            }    
            $parteDecimal = " $decimal/100 MN ";
        } else $parteDecimal = " 00/100 MN ";
        return strtoupper(trim($parteEntera) . " $pesos" . $parteDecimal );
    }
}