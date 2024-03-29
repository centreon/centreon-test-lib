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

namespace Centreon\PHPStan\CustomRules\RepositoryRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\CentreonRuleTrait;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check if a Repository implements
 * an Interface defined in Application layer.
 *
 * @implements Rule<Node\Stmt\Class_>
 */
class RepositoryImplementsInterfaceCustomRule implements Rule
{
    use CentreonRuleTrait;

    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // This rule does not apply.
        if (
            ! str_contains($node->name->name ?? '', 'Repository')
            || empty($node->implements)
            || $node->isAbstract()
            || $this->extendsAnException($node->namespacedName?->toCodeString())
        ) {
            return [];
        }

        // Check rule.
        foreach ($node->implements as $implementation) {
            if (str_contains($implementation->toString(), '\\Application\\')) {
                return [];
            }
        }

        return [
            CentreonRuleErrorBuilder::message(
                'Repositories must implement an Interface defined in Application layer.'
            )->build(),
        ];
    }
}
