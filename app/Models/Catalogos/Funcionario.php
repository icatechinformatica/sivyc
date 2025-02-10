<?php

namespace App\Models\Catalogos;

use Illuminate\Database\Eloquent\Model;

class Funcionario extends Model
{

    protected $table = 'tbl_funcionario';

    protected $fillable = [ 'id', 'clave_cat', 'categoria_estatal', 'clave_puesto', 'puesto_estatal', 'nombre_adscripcion', 'clave_empleado', 'fecha_ingreso', 'fecha_baja', 'nombre_trabajador', 'rfc_usuario', 'curp_usuario', 'num_comisionados', 'fecha_comision', 'comision_direccion_o_unidad', 'comision_depto', 'comision_accion_movil', 'titular', 'direccion', 'telefono', 'correo', 'status', 'titulo', 'incapacidad', 'correo_institucional', 'id_user_created', 'id_user_updated'];

    protected $hidden = ['created_at', 'updated_at'];
}
