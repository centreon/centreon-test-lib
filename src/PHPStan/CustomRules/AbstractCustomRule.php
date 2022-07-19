<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules;
use \PhpParser\Node;

/**
 * AbstractCustomRule class defines TAG constant to be added in Custom Rule
 */
abstract class AbstractCustomRule
{
    /**
     * This constant contains a beginning tag for custom error displayed by PHPStan.
     */
    protected const TAG = '[CENTREON-RULE]: ';

    /**
     * This property contains a specific error message displayed by PHPStan.
     *
     * @var string
     */
    protected string $errMessage;

    /**
     * This method returns variable name string to be displayed by PHPStan.
     *
     * @param Node $node
     * @return string
     */
    abstract function getVariableNameFromNode(Node $node): ?string;

    /**
     * This method constructs the error message string displayed by PHPStan.
     *
     * @param string $varName
     * @return string
     */
    protected function buildErrorMessage(string $varName): string
    {
        return self::TAG . $varName . $this->errMessage;
    }
}
