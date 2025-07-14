<?php

namespace App\Factories\Transaction;

use App\Repositories\Transaction\TransactionRepository;
use App\Interfaces\Transaction\TransactionRepositoryInterface;

class TransactionFactory
{
    public function make(?string $modelo = null): TransactionRepositoryInterface
    {
        //instanciando el repositorio con el constructor mandando mi modelo
        return (new TransactionRepository($modelo));
    }
}
