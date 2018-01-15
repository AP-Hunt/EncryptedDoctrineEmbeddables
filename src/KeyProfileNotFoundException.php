<?php

namespace App;

use Throwable;

class KeyProfileNotFoundException extends \Exception
{
    public function __construct(string $keyProfileName, Throwable $previous = null)
    {
        parent::__construct(sprintf("Key profile '%s' could not be loaded"), 0, $previous);
    }
}