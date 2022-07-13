<?php declare(strict_types=1);

namespace Centreon\PHPStan\Rules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * Class to test phpstan.neon config file
 */
class MyTestRule implements \PHPStan\Rules\Rule
{
    /**
     * Method returns Param class from Abstract Syntax Tree
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return \PhpParser\Node\Param::class;
    }

    /**
     * Method verifies the length of Param name and returns an error if Param name is less than 3 chars.
     *
     * @param Node $node
     * @param Scope $scope
     * @return array
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (strlen($node->var->name) < 3) {
            return [
                RuleErrorBuilder::message(
                    '[CENTREON-ERROR] ' . $node->var->name . ': Parameter name is too short.'
                )->build(),
                ];
        }
        return [];
    }
}