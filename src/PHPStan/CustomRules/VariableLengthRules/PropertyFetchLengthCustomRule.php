<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\VariableLengthRules;

use Centreon\PHPStan\CustomRules\VariableLengthRules\VariableLengthRule;
use \PHPStan\Analyser\Scope;
use \PHPStan\Rules\Rule;
use \PHPStan\Rules\RuleErrorBuilder;
use \PhpParser\Node;

/**
 * PropertyFetchLengthCustomRule class implements custom PHPStan rule: property's invocation within class name 
 * shouldn't be shorter than 3 characters.
 */
class PropertyFetchLengthCustomRule extends VariableLengthRule implements Rule
{
    /**
     * getNodeType() method returns an Abstract Syntax Tree PropertyFetch class to be scanned.
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return Node\Expr\PropertyFetch::class;
    }

    /**
     * processNode() method implements the logic of the custom rule and checks if the scanned node respects
     * the implemented logic.
     *
     * @param Node $node
     * @param Scope $scope
     * @return array
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (strlen($node->name->name) < 3 && !in_array($node->name->name, $this->whitelistVarNames, $strict = true))
        {
            return[
                RuleErrorBuilder::message(
                    $this->buildErrMessage($node)
                )->build(),
            ];
        }
        return [];
    }
}