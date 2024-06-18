<?php
namespace App\Utilities;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Contracts\Encryption\DecryptException;

define('secret_key', 'Xw0T%6@&');
class MyCrypt
{    
    public static function encrypt_($val)
    {
        if($val){
            $val_encrypt = Crypt::encrypt($val, secret_key);            
        }else $val_encrypt = "NO SE HA ENVIADO NINGUN VALOR PARA ENCRIPTAR.";
        return $val_encrypt;
    }
    
    public static function decrypt_($val)
    {        

        if($val){             
            try {                
                $val_decrypt = Crypt::decrypt($val, secret_key);
            } catch (DecryptException $e) {
                $val_decrypt = "Ocurrió un error al desencriptar el valor.";                     
            }                  
        }else $val_decrypt = "NO SE HA ENVIADO NINGUN VALOR PARA DESENCRIPTAR.";
        return $val_decrypt;
        
    }
}