<?php
namespace App\Utilities;
use Illuminate\Support\Facades\Storage;
class Algoritmo35
{    
      
    public static function digito_verificador($folio=null){ 
        if($folio){                
            $abc = Algoritmo35::abc(); 
            $f = str_replace('-','', $folio); 
            $f = str_pad($f, 19, "0", STR_PAD_RIGHT);
            $f = str_split($f);
            $f = Algoritmo35::reemplazar_abc($f,$abc); 
            $f = Algoritmo35::multiplicando($f);
            $f = Algoritmo35::sumando($f);
            $total = array_sum($f);
            $residuo = 10-($total % 10);
             if($residuo==10) $residuo = 0;
           return $residuo;
        }else $message = "Por favor ingres un valor válido.";
        return $message ?? null;
        
    }

    public static function abc(){     
        for ($i = 0; $i < 26; $i++) {
            $letra = chr(65 + $i); // 65 es 'A' en ASCII
            $abc[$letra] = ($i + 1); // A => 1, B => 2, ..., Z => 26
        }
        return $abc;
    }

    public static function reemplazar_abc($f, $abc){
        return $result = array_map(function($item) use ($abc) {
        return isset($abc[$item]) ? $abc[$item] : $item;
        }, $f);

        
    }

    public static function multiplicando($a){
        $b = [4,3,8,4,3,8,4,3,8,4,3,8,4,3,8,4,3,8,4]; //ponderaciones
        $c = array_map(function($x, $y) {
                return $x * $y;
        }, $a, $b);
        return $c;
    }


    public static function sumando($a){
        foreach($a as $v){
            $result[] = Algoritmo35:: sumar($v);
        }
        return $result ?? null;
    }

    public static function sumar($valor){
        while($valor>9){
            $v = str_split($valor);
            $valor = $v[0]+$v[1];
        }
        return $valor;
    }
}