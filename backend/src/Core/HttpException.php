<?php

declare(strict_types=1);

namespace App\Core;

use Exception;

// Error esperado que se devuelve al cliente con un código HTTP.
class HttpException extends Exception
{
    public function __construct(private int $status, private string $codigo)
    {
        parent::__construct($codigo);
    }

    public function getStatus(): int
    {
        return $this->status;
    }

    public function getCodigo(): string
    {
        return $this->codigo;
    }
}