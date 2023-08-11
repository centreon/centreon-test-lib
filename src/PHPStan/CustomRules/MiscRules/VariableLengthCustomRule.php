<?php

/*
 * Copyright 2005 - 2023 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 *
 */

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules\MiscRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;

/**
 * This class implements custom rule for PHPStan to check if variable name contains more
 * than 3 characters.
 *
 * @implements Rule<Node>
 */
class VariableLengthCustomRule implements Rule
{
    /**
     * This constant contains an array of variable names to whitelist by custom rule.
     */
    public const WHITELIST_VARIABLE_NAME = [
        'db',
        'ex',
        'id',
    ];

    public function getNodeType(): string
    {
        return Node::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $varName = $this->getVariableNameFromNode($node);
        if ($varName !== null && mb_strlen($varName) < 3 && ! in_array($varName, self::WHITELIST_VARIABLE_NAME, true)) {
            return [
                CentreonRuleErrorBuilder::message("${$varName} must contain 3 or more characters.")->build(),
            ];
        }

        return [];
    }

    /**
     * This method returns variable name from a scanned node if the node refers to
     * variable/property/parameter.
     *
     * @param Node $node
     *
     * @return string|null
     */
    private function getVariableNameFromNode(Node $node): ?string
    {
        return match (true) {
            $node instanceof \PHPStan\Node\ClassPropertyNode => $node->getName(),
            $node instanceof Node\Expr\PropertyFetch => $node->name->name ?? null,
            $node instanceof Node\Expr\Variable => is_string($node->name) ? $node->name : ($node->name->name ?? null),
            $node instanceof Node\Param => $node->var->name ?? null,
            default => null
        };
    }
}
