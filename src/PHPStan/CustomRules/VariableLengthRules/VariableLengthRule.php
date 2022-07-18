<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\VariableLengthRules;

use Centreon\PHPStan\CustomRules\AbstractCustomRule;
use \PhpParser\Node;

class VariableLengthRule extends AbstractCustomRule
{
    /**
     * String specifying the error message to be displayed in PHPStan.
     *
     * @var string
     */
    protected string $errMessage = ' is less than 3 characters.';

    /**
     * List of accepted variable names with less than 3 characters.
     *
     * @var array
     */
    protected array $whitelistVarNames = [
        'id',
        'db',
        'ex'
    ];
}
