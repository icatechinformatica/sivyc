<?php

namespace App;

use App\Models\Rol;
use App\Models\Unidad;
use App\Models\Permission;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;

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
                        return $query->where(\DB::raw("upper(concat(name, ' ', curp, ' ', email))"), 'LIKE', '%'.strtoupper($buscar).'%');
                        break;
                    default:
                        # code...
                        break;
                }
            }
        }
    }

    public function roles()
    {
        return $this->belongsToMany(Rol::class, 'role_user', 'user_id', 'role_id');
    }

    public function permissions()
    {
        return $this->belongsToMany(Permission::class, 'permission_user', 'user_id', 'permission_id');
    }

    // Métodos de ayuda (opcional)
    public function hasRole($role)
    {
        return $this->roles()->where('name', $role)->exists();
    }

    public function hasPermission($permission)
    {
        // Si algún rol tiene all-access, retorna true
        if ($this->roles()->where('special', 'all-access')->exists()) {
            return true;
        }

        // Si algún rol tiene no-access, retorna false
        if ($this->roles()->where('special', 'no-access')->exists()) {
            return false;
        }

        // Permisos directos o por rol
        return $this->permissions()->where('slug', $permission)->exists() ||
               $this->roles()->whereHas('permissions', function($q) use ($permission) {
                   $q->where('slug', $permission);
               })->exists();
    }

}
