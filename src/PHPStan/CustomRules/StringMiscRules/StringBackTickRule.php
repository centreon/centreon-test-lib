<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\StringMiscRules;

use Centreon\PHPStan\CustomRules\AbstractCustomRule;

class StringBackTickRule extends AbstractCustomRule
{
    /**
     * String specifying the error message to be displayed in PHPStan.
     *
     * @var string
     */
    protected string $errMessage = ' must be enclosed in ``.';
}