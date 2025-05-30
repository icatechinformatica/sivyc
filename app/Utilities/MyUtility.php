<?php
namespace App\Utilities;
use Illuminate\Support\Facades\Storage;

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
    public static function numerico($importe){
        return preg_replace('/[^0-9.]/', '', $importe);
    }
    public static function letras($cantidad, $ver_decimal=true){
        $unidades = ["", "un", "dos", "tres", "cuatro", "cinco", "seis", "siete", "ocho", "nueve"];
        $decenas = ["", "diez", "veinte", "treinta", "cuarenta", "cincuenta", "sesenta", "setenta", "ochenta", "noventa"];        
        $centenas = ["cien", "ciento", "doscientos", "trescientos", "cuatrocientos", "quinientos", "seiscientos", "setecientos", "ochocientos", "novecientos"];
        $especiales = ["diez", "once", "doce", "trece", "catorce", "quince", "dieciseis", "diecisiete","dieciocho", "diecinueve"];
    
        $entero = floor($cantidad);//dd($entero);
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
            $unidad = $entero % 10; //dd($unidad);
            //dd($millar);
            if ($millones > 0){
                $parteEntera .= MyUtility::letras($millones, false);
                if($millones>1) $parteEntera .= " millones ";
                else $parteEntera .= " millon ";
            }

            if ($millar > 0) {
                if ($millar == 1) $parteEntera .= " un"; 
                elseif ($millar >= 2 && $millar <= 9) $parteEntera .= $unidades[$millar];  
                elseif ($millar >= 10 && $millar <= 19) $parteEntera .= $especiales[$millar-10];
                else $parteEntera .= MyUtility::letras($millar, false);
                   
                $parteEntera .= " mil ";
            }

            if ($centena > 0){                
                if($centena==1 and $decena==0) $parteEntera .=  $centenas[0] . " ";
                else $parteEntera .= $centenas[$centena] . " ";                
            }
            if ($decena > 0){                
                $parteEntera .= $decenas[$decena] . " ";
            }
            if ($unidad > 0) {
                $d = floor($decena / 1); 
                $u = $unidad % 10; //dd($d);
                if ($unidad == 1){
                    if($d>0) $parteEntera .= " y un ";
                    else $parteEntera .= " un ";
                }
                if ($unidad >= 2 && $unidad <= 9){
                    if ($d > 0) $parteEntera .= " y ".$unidades[$u] ;
                    else $parteEntera .= $unidades[$u];
                }                           
            }    
            $parteEntera .= " ";
        } else $parteEntera = "No soportado";
      //  dd($parteEntera);
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
        }else $parteDecimal = " 00/100 MN ";
        
        if(!$ver_decimal) $parteDecimal="";
        else $parteDecimal = " $pesos" . $parteDecimal;

        return strtoupper(trim($parteEntera) . $parteDecimal );
    }

    public static function upload_file($path, $file, $name, $file_delete=null){ 
        //php artisan storage:link
        $data_file = ["message"=>null, 'url_file'=>null, 'up'=>null];
        if($file){
            $ext = $file->getClientOriginalExtension();
            $ext = strtolower($ext);
            $msg= null;
            $up = false;
            if($ext == "pdf"){          
                $up = Storage::disk('public')->put($path.$name, file_get_contents($file));
                if($up){
                    if($file_delete){
                        if(Storage::exists($file_delete)){
                            Storage::delete($file_delete);
                            $msg = "El archivo ha sido reemplazado correctamente!";
                        }
                    }else $msg = "El archivo ha sido cargado correctamente!";
                }
            }else $msg= "Formato de Archivo no válido, sólo PDF.";
                    
            $data_file = ["message"=>$msg, 'url_file'=>$path, 'up'=>$up];
        }
        return $data_file;
    }

    public static function textoAltasBajas($texto)
    {
        $minisculas = ['de', 'del', 'la', 'y', 'en', 'el', 'a', 'con'];
        
        $palabras = explode(' ', mb_strtolower($texto, 'UTF-8'));
        $resultado = [];
        foreach ($palabras as $index => $palabra) {
            if ($index === 0 || !in_array($palabra, $minisculas)) {
                $resultado[] = mb_convert_case($palabra, MB_CASE_TITLE, 'UTF-8');
            } else {
                $resultado[] = $palabra;
            }
        }

        return implode(' ', $resultado);
    }
}