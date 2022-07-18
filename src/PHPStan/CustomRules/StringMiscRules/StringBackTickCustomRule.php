<?php

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\StringMiscRules;

use Centreon\PHPStan\CustomRules\StringMiscRules;
use \PHPStan\Analyser\Scope;
use \PHPStan\Rules\Rule;
use \PHPStan\Rules\RuleErrorBuilder;
use \PhpParser\Node;

class StringBackTickCustomRule extends StringBackTickRule implements Rule
{
    public function getNode(): string
    {
        return Node\Scalar\String_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        preg_match('/:db/', $node->value, $matches);
        if (!empty($matches) && !preg_match('/`:dbstg`|`:db`/', $node->value)) {
            return [
                RuleErrorBuilder::message(
                    $this->buildErrMessage($node)
                )->build(),
            ];
        }
        return [];
    }
}
