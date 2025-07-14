<?php
namespace App\Services;

use App\Factories\Transaction\TransactionFactory;
use Illuminate\Support\Facades\DB;
use Exception;

class TransactionService
{
    protected $repositorio;

    public function __construct(string $dataString, TransactionFactory $factory)
    {
        $this->repositorio = $factory->make($dataString);
    }

    public function obtenerTodoDatos()
    {
        return $this->repositorio->obtenerTodo();
    }

    public function obtenerDatosPorId(int $id)
    {
        return $this->repositorio->obtenerPorId($id);
    }

    public function crearDato(array $data)
    {
        DB::beginTransaction();
        try {
            $record = $this->repositorio->crear($data);
            DB::commit();
            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            report($e); // opcional: logear el error
            throw $e;
        }
    }

    public function actualizaDatos(int $id, array $data)
    {
        DB::beginTransaction();
        try {
            $record = $this->repositorio->actualizar($id, $data);
            DB::commit();
            return $record;
        } catch (Exception $e) {
            DB::rollBack();
            report($e); // opcional: logear el error
            throw $e;
        }
    }
    public function eliminarDatos(int $id)
    {
        DB::beginTransaction();
        try {
            $result = $this->repositorio->eliminar($id);
            DB::commit();
            return $result; // true si se eliminó, false si no
        } catch (Exception $e) {
            DB::rollBack();
            report($e); // opcional: logear el error
            throw $e;
        }
    }

}
