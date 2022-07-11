<?php

namespace App\Models\cat;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
trait catUnidades
{      
    protected function unidades_user($rol){
        $id_user = Auth::user()->id;
        $id_unidad= Auth::user()->unidad;
        $unidades = $unidad = NULL;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)->WHERE('roles.slug', 'like', '%'.$rol.'%')
            ->value('roles.slug'); 
        if($rol){
            $uni = DB::table('tbl_unidades')->select(DB::raw('SUBSTR(cct,1,5) as clave'),DB::raw('SUBSTR(cct,6,10) as cct'),'ubicacion','unidad')->where('id',$id_unidad)->first();
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$uni->unidad)->orderby('unidad','ASC')->pluck('unidad','unidad');         
            if(count($unidades)==0)$unidades = DB::table('tbl_unidades')->where('unidad',$uni->unidad)->orderby('unidad','ASC')->pluck('unidad','unidad');
            
            if($uni->clave == '07EIC')$cct= $uni->cct;
            else $cct = DB::table('tbl_unidades')->where('unidad',$uni->ubicacion)->value(DB::raw('SUBSTR(cct,6,10) as cct'));
            $letra =substr($cct,4,5);
            $dig =  intval(preg_replace("/D/", "", $cct))*1;
            $cct = $dig.$letra;
            
            $data['cct_unidad'] = $cct;
            $unidad = $uni->unidad;         
        }
        if(!$unidades) $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
        $data ['unidad'] = $unidad;
        $data ['unidades'] = $unidades;
        $data ['slug'] = $rol;
        return $data; 
              
    }

    protected function unidad_user(){
        $id_user = Auth::user()->id;
        $id_unidad= Auth::user()->unidad;
        $unidades = $unidad = NULL;
        $rol = DB::table('role_user')->LEFTJOIN('roles', 'roles.id', '=', 'role_user.role_id')            
            ->WHERE('role_user.user_id', '=', $id_user)
            ->value('roles.slug'); 
        if($rol<>'admin'){
            $uni = DB::table('tbl_unidades')->select(DB::raw('SUBSTR(cct,1,5) as clave'),DB::raw('SUBSTR(cct,6,10) as cct'),'ubicacion','unidad')->where('id',$id_unidad)->first();
            $unidades = DB::table('tbl_unidades')->where('ubicacion',$uni->unidad)->orderby('unidad','ASC')->pluck('unidad','unidad');         
            if(count($unidades)==0)$unidades = DB::table('tbl_unidades')->where('unidad',$uni->unidad)->orderby('unidad','ASC')->pluck('unidad','unidad');
            
            if($uni->clave == '07EIC')$cct= $uni->cct;
            else $cct = DB::table('tbl_unidades')->where('unidad',$uni->ubicacion)->value(DB::raw('SUBSTR(cct,6,10) as cct'));
            $letra =substr($cct,4,5);
            $dig =  intval(preg_replace("/D/", "", $cct))*1;
            $cct = $dig.$letra;
            
            $data['cct_unidad'] = $cct;
            $unidad = $uni->unidad;         
        }
        if(!$unidades) $unidades = DB::table('tbl_unidades')->orderby('unidad','ASC')->pluck('unidad','unidad');
        $data ['unidad'] = $unidad;
        $data ['unidades'] = $unidades;
        $data ['slug'] = $rol;
        return $data; 
              
    }


}