<?php

namespace SenseiTarzan\MultiEconomy\Class\Exception;

use Exception;

class EconomyUpdateException extends Exception
{
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}