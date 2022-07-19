<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules;

use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use PhpParser\Node;
use Centreon\PHPStan\CustomRules;

class VariableLengthCustomRule extends AbstractCustomRule implements Rule
{
    /**
     * This constant contains an array of variable names to whitelist by custom rule.
     */
    public const WHITELIST_VARIABLE_NAME = [
        'db',
        'ex',
        'id'
    ];

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @inheritDoc
     *
     * @param Node $node
     * @param Scope $scope
     * @return array
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $varName = $this->getVariableNameFromNode($node);
        if ($varName !== null && strlen($varName) < 3 && ! in_array($varName, self::WHITELIST_VARIABLE_NAME)) {
            $varName = "$$varName";
            $this->errMessage = ' must contain 3 or more characters.';
            return [
                RuleErrorBuilder::message(
                    $this->buildErrorMessage($varName)
                )->build(),
            ];
        }
        return [];
    }

    /**
     * @inheritDoc
     *
     * @param Node $node
     * @return string|null
     */
    public function getVariableNameFromNode(Node $node): ?string
    {
        switch ($node->getType()) {
            case 'PHPStan_Node_ClassPropertyNode':
                return $node->getName();
            case 'Expr_PropertyFetch':
                return $node->name->name;
            case 'Expr_Variable':
                return $node->name;
            case 'Param':
                return $node->var->name;
            default:
                return null;
        }
    }
}
