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
        'id','nombre', 'email', 'unidad', 'puesto', 'token_movil','correo_institucional','activo',
        'registro_id', 'registro_type', 'fecha_caducidad'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
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
            if (!empty(trim($buscar))) {
                switch ($tipo) {
                    case 'curp_aspirante':
                        return $query->whereHas('registro', function($q) use ($buscar) {
                            $q->where('curp_usuario', '=', $buscar);
                        });
                    case 'nombres':
                        return $query->whereHas('registro', function($q) use ($buscar) {
                            $q->where(DB::raw("upper(concat(tblz_usuarios.id, ' ', nombre_trabajador, ' ', curp_usuario, ' ', correo))"), 'LIKE', '%'.strtoupper($buscar).'%');
                        });
                    default:
                        break;
                }
            }
        }
        
        return $query;
    }
    
    public function registro()
    {
        return $this->morphTo();
    }

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
