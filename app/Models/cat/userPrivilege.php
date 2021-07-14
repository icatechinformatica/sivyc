<?php

namespace App\Models\cat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

trait userPrivilege
{
     function __construct() {        
       
    } 
    
    protected function idRol(){
        $id_user = Auth::user()->id;
        $unidades=NULL;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)
            ->value('roles.id'); 
        return $rol;
    }

    protected function unidades_permitidas(){  
        //1. Rol unidad o administrador
        //2. Es acción móvil o unidad       
        $id_user = Auth::user()->id;
        $unidades=NULL;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%unidad%')->orWhere('roles.slug', 'like', '%vincula%')
            ->value('roles.slug');        
        
        if($rol){
            $id_unidad = Auth::user()->unidad;             
            $ubicacion= DB::table('tbl_unidades')->where('id',$id_unidad)->where('cct','like','07EIC%')->value('ubicacion');
            if($ubicacion) $unidades = DB::table('tbl_unidades')->where('ubicacion',$ubicacion)->pluck('unidad','id');
            else $unidades = DB::table('tbl_unidades')->where('id',$id_unidad)->pluck('unidad','id'); 
        }   
        if(!$unidades) $unidades = DB::table('tbl_unidades')->pluck('unidad','id');       
        return $unidades;
       
    }

   


}