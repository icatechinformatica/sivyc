<?php

namespace App;


use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use App\Models\Unidad;
use App\Models\Rol;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use Notifiable;
    protected $guard_name = 'web'; // Añade esto específicamente

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'unidad', 'puesto','unidades','status','telefono', 'curp', 'activo'
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

    // public function roles()
    // {
    //     return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'role_id')->withPivot('user_id ', 'role_id');
    // }

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
                        # code...
                        return $query->where('matricula', '=', $buscar);
                        break;
                    case 'curp_aspirante':
                        # code...
                        return $query->where('curp', '=', $buscar);
                        break;
                    case 'nombres':
                        # code...
                        // return $query->where(\DB::raw("upper(name)"), 'LIKE', "%$buscar%");
                        return $query->where(DB::raw("upper(concat(name, ' ', curp, ' ', email))"), 'LIKE', '%'.strtoupper($buscar).'%');
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }
}
