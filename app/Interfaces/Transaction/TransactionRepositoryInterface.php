<?php
namespace App\Interfaces\Transaction;

interface TransactionRepositoryInterface
{
    /**
     * obtener todos los registros del modelo.
     *
     * @return array
     */
    public function obtenerTodo(): object;

    /**
     * obtener un registro por su ID.
     *
     * @param int $id
     * @return object|null
     */
    public function obtenerPorId(int $id): ?object;

    /**
     * crear un nuevo registro en el modelo.
     *
     * @param array $data
     * @return object
     */
    public function crear(array $data): object;

    /**
     * actualizar un registro por su ID.
     *
     * @param int $id
     * @param array $data
     * @return object|null
     */
    public function actualizar(int $id, array $data): ?object;

    /**
     * borrar un registro por su ID.
     *
     * @param int $id
     * @return bool
     */
    public function eliminar(int $id): bool;
}
