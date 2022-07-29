<?php

/*
 * Copyright 2005 - 2022 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 *
 * For more information : contact@centreon.com
 */

declare(strict_types=1);

namespace Centreon\PHPStan\CustomRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;

/**
 * This class implements custom rule for PHPStan to check if variable name contains more
 * than 3 characters.
 */
class VariableLengthCustomRule implements Rule
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
     */
    public function getNodeType(): string
    {
        return Node::class;
    }

    /**
     * @inheritDoc
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $varName = $this->getVariableNameFromNode($node);
        if ($varName !== null && strlen($varName) < 3 && ! in_array($varName, self::WHITELIST_VARIABLE_NAME)) {
            return [
                CentreonRuleErrorBuilder::message("$$varName must contain 3 or more characters.")->build(),
            ];
        }

        return [];
    }

    /**
     * This method returns variable name from a scanned node if the node refers to
     * variable/property/parameter
     *
     * @param Node $node
     * @return string|null
     */
    private function getVariableNameFromNode(Node $node): ?string
    {
        return match (true) {
            $node instanceof \PHPStan\Node\ClassPropertyNode => $node->getName(),
            $node instanceof Node\Expr\PropertyFetch => $node->name->name,
            $node instanceof Node\Expr\Variable => $node->name,
            $node instanceof Node\Param => $node->var->name,
            default => null
        };
    }
}
