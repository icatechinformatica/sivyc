<?php
namespace App\Repositories\Transaction;

use App\Interfaces\Transaction\TransactionRepositoryInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionRepository  implements TransactionRepositoryInterface
{
    protected $model = null;
    //constructor y inicializa la clase que le paso para poder trabajar con los métodos
    public function __construct($nombreClase = null)
    {
        $className = "App\\Models\\{$nombreClase}";
        if (class_exists($className) && is_subclass_of($className, Model::class)) {
            $this->model = new $className;
            return;
        }
    }

    //obtener información completa toda la del modelo
    public function obtenerTodo(): object
    {
        try {
            return $this->model->all();
        } catch (Exception $th) {
            return []; // Siempre retorna array
        }
    }

    public function obtenerPorId(int $id): ?object
    {
        $transaction = $this->model->find($id);
        return $transaction ? $transaction : null;
    }

    // crear datos de un modelo dinámico
    public function crear(array $data): object
    {
        return $record = $this->model->create($data);
    }

    public function actualizar(int $id, array $data): ?object
    {
        $record = $this->model->findOrFail($id);
        if (!$record) {
            return null; // o lanzar una excepción si prefieres
        }
        return $record->update($data);
    }

    public function eliminar(int $id): bool
    {
        $deleted = $this->model->destroy($id) > 0;
        return $deleted;
    }
}
