<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Formatter\JsonFormatter;

class CustomizeJsonFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            $formatter = new JsonFormatter(JsonFormatter::DEFAULT_MAX_DEPTH, true, true);
            // El segundo true => agrega "newline" al final; el tercero => pretty = true (si prefieres compacto, pon false)
            $handler->setFormatter($formatter);
        }
    }
}
