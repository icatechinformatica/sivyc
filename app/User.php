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

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'registro_id', 'registro_type', 'password', 'activo', 'fecha_caducidad',
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
            # entramos y validamos
            if (!empty(trim($buscar))) {
                # empezamos
                switch ($tipo) {
                    case 'matricula_aspirante':
                        return $query->where('matricula', '=', $buscar);
                    case 'curp_aspirante':
                        return $query->where('curp', '=', $buscar);
                    case 'nombres':
                        // return $query->where(\DB::raw("upper(name)"), 'LIKE', "%$buscar%");
                        return $query->where(DB::raw("upper(concat(nombre, ' ', curp, ' ', email))"), 'LIKE', '%'.strtoupper($buscar).'%');
                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
