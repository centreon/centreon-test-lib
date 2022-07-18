<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\VariableLengthRules;

use Centreon\PHPStan\CustomRules\VariableLengthRules\VariableLengthRule;
use \PHPStan\Analyser\Scope;
use \PHPStan\Rules\Rule;
use \PHPStan\Rules\RuleErrorBuilder;
use \PhpParser\Node;

/**
 * PropertyPropertyLengthCustomRule class implements custom PHPStan rule: class' property name shouldn't be shorter than
 * 3 characters.
 */
class PropertyPropertyLengthCustomRule extends VariableLengthRule implements Rule
{
    /**
     * getNodeType() method returns an Abstract Syntax Tree PropertyProperty class to be scanned.
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return Node\Stmt\PropertyProperty::class;
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
        if (strlen($node->name->toString()) < 3 && !in_array($node->name->toString(), $this->whitelistVarNames, $strict = true))
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