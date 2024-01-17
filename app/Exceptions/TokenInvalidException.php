<?php

namespace App\Exceptions;

// use Exception;
use Symfony\Component\HttpKernel\Exception\HttpException;

class TokenInvalidException extends HttpException
{

    public function __construct($message = 'Token no válido, intente de nuevo en un momento', $code = 401)
    {
        parent::__construct($code, $message);
    }

    public function report()
    {
        // Puedes agregar lógica de registro o notificación aquí si es necesario
    }

    public function render()
    {
        // return response()->json(['error' => $this->getMessage()], $this->getCode());
        // return response()->json(['error' => $this->getMessage()], $this->getStatusCode());
        return response($this->getMessage(), $this->getStatusCode());
    }
}
