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

    // Removido el protected $with para evitar errores de eager loading automático

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id',
        'nombre',
        'email',
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

    // public function unidades()
    // {
    //     return $this->belongsTo(Unidad::class, 'unidad');
    // }

    // public function unidadTo() //Romelia
    // {
    //     return $this->belongsTo(Unidad::class, 'unidad');
    // }

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

    public function getUbicacionAttribute()
    {
        if (!$this->registro) {
            return null;
        }

        // Si es funcionario, usar query directo para obtener la ubicación
        if ($this->registro_type === 'App\Models\funcionario') {
            try {
                // Intentar usar la relación primero
                $unidad = $this->registro->getPrimeraUnidad();
                if ($unidad && $unidad->ubicacion) {
                    return $unidad->ubicacion;
                }
            } catch (\Exception $e) {
                // Si todo falla, retornar null
                return null;
            }
        }

        // Para instructores u otros tipos
        return $this->registro->ubicacion ?? null;
    }

    public function organismo()
    {
        if ($this->registro && method_exists($this->registro, 'organismo')) {
            return $this->registro->organismo();
        }
        return null;
    }

    public function getUnidadAttribute()
    {
        // Siempre devolver un modelo Unidad o null (no un ID) para mantener consistencia
        if (!$this->registro) {
            return null;
        }

        // Funcionario: obtiene la primera unidad a través de sus organismos
        if ($this->registro_type === 'App\\Models\\funcionario') {
            return $this->registro->getPrimeraUnidad(); // -> Unidad|null
        }

        // Instructor: intentar diversas fuentes conocidas
        // 1) Si existe relación 'unidad' en el modelo (por compatibilidad futura)
        if (method_exists($this->registro, 'unidad')) {
            $rel = $this->registro->unidad;
            // Si es relación, obtener el modelo; si ya es modelo, retornarlo
            return $rel instanceof \Illuminate\Database\Eloquent\Relations\Relation ? $rel->getResults() : $rel;
        }

        // 2) Campo 'clave_unidad' (puede referir a id o CCT)
        if (property_exists($this->registro, 'clave_unidad') || isset($this->registro->clave_unidad)) {
            $clave = $this->registro->clave_unidad;
            if (!empty($clave)) {
                $unidad = Unidad::where('id', $clave)->orWhere('cct', $clave)->first();
                if ($unidad) return $unidad;
            }
        }

        // 3) Arreglo de 'unidades_disponible' (tomar la primera)
        if (isset($this->registro->unidades_disponible) && is_array($this->registro->unidades_disponible)) {
            $ids = array_values(array_filter($this->registro->unidades_disponible));
            if (!empty($ids)) {
                $unidad = Unidad::whereIn('id', $ids)->first();
                if ($unidad) return $unidad;
            }
        }

        return null;
    }

    // Método para obtener el ID de la unidad
    public function getUnidadIdAttribute()
    {
        return optional($this->unidad)->id;
    }

    // Método para obtener el nombre de la unidad
    public function getUnidadNombreAttribute()
    {
        return optional($this->unidad)->unidad;
    }
}
