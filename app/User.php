<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Unidad;
use App\Models\Rol;
use Icatech\PermisoRolMenu\Traits\ConfiguresSpanishUserModel;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable, ConfiguresSpanishUserModel;
    protected $guard_name = 'web'; // Añade esto específicamente

    protected $with = ['registro'];

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nombre',
        'email',
        'unidad',
        'puesto',
        'token_movil',
        'correo_institucional',
        'activo',
        'registro_id',
        'registro_type',
        'fecha_caducidad'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function unidades()
    {
        return $this->belongsTo(Unidad::class, 'unidad');
    }

    public function unidadTo() //Romelia
    {
        return $this->belongsTo(Unidad::class, 'unidad');
    }

    public function scopeBusquedaPor($query, $tipo, $buscar)
    {
        if (!empty($tipo)) {
            // Si hay búsqueda, filtrar
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'nombres':
                        return $query->where(function ($q) use ($buscar) {
                            // Búsqueda en funcionarios
                            $q->where(function ($subQ) use ($buscar) {
                                $subQ->where('registro_type', 'App\\Models\\funcionario')
                                    ->whereExists(function ($exists) use ($buscar) {
                                        $exists->select(DB::raw(1))
                                            ->from('tbl_funcionario')
                                            ->whereRaw('tblz_usuarios.registro_id = tbl_funcionario.id')
                                            ->whereRaw("upper(tbl_funcionario.id::text || ' ' || COALESCE(tbl_funcionario.nombre_trabajador, '') || ' ' || COALESCE(tbl_funcionario.curp_usuario, '') || ' ' || COALESCE(tbl_funcionario.correo, '')) LIKE ?", ['%' . strtoupper($buscar) . '%']);
                                    });
                            })
                                ->orWhere(function ($subQ) use ($buscar) {
                                    $subQ->where('registro_type', 'App\\Models\\instructor')
                                        ->whereExists(function ($exists) use ($buscar) {
                                            $exists->select(DB::raw(1))
                                                ->from('instructores')
                                                ->whereRaw('tblz_usuarios.registro_id = instructores.id')
                                                ->whereRaw("upper(instructores.id::text || ' ' || COALESCE(instructores.nombre, '') || ' ' || COALESCE(instructores.curp, '') || ' ' || COALESCE(instructores.correo, '')) LIKE ?", ['%' . strtoupper($buscar) . '%']);
                                        });
                                })
                                // Búsqueda en usuarios sin asociación polimórfica
                                ->orWhere(function ($subQ) use ($buscar) {
                                    $subQ->where(function ($nullQ) {
                                        $nullQ->whereNull('registro_id')->orWhereNull('registro_type');
                                    })
                                        ->whereRaw("upper(COALESCE(email, '') || ' ' || COALESCE(nombre, '')) LIKE ?", ['%' . strtoupper($buscar) . '%']);
                                })->ORDERBY('registro_type', 'ASC');
                        });
                    default:
                        break;
                }
            } else {
                // Sin búsqueda: mostrar TODOS los usuarios (con y sin registro)
                return $query;
            }
        }
        return $query;
    }

    // public function registro()
    // {
    //     return $this->morphTo();
    // }

    // Accessor para obtener el nombre desde la tabla relacionada
    public function getNameAttribute()
    {
        return $this->registro ? $this->registro->nombre_trabajador : null;
    }

    public function getNombreAttribute()
    {
        return $this->registro ? $this->registro->nombre_trabajador : null;
    }

    public function registro()
    {
        return $this->morphTo(__FUNCTION__, 'registro_type', 'registro_id');
    }
}
