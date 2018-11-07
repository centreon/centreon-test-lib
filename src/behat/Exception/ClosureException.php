<?php

namespace Centreon\Test\Behat\Exception;

use Throwable;

class ClosureException extends \Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}
