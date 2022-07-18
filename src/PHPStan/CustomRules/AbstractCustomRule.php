<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules;
use \PhpParser\Node;

/**
 * AbstractCustomRule class defines TAG constant to be added in Custom Rule
 */
class AbstractCustomRule
{
    /**
     * Constant TAG that will be used in the beginning of the custom error.
     */
    protected const TAG = '[CENTRON-RULE]: ';

    /**
     * buildErrMessage method constructs the error message string using TAG constant,
     * getType() method of Node class and $errMessage attribute.
     *
     * @param Node $node
     * @return string
     */
    protected function buildErrMessage(Node $node): string
    {
        return self::TAG . $node->getType() . $this->errMessage;
    }
}
