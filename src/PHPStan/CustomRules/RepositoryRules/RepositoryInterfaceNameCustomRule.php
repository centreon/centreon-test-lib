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
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;

/**
 * This class implements a custom rule for PHPStan to check Interface naming requirement.
 * It must start with 'Read' or 'Write' mentions and end with 'RepositoryInterface'.
 *
 * @implements Rule<Node\Stmt\Class_>
 */
class RepositoryInterfaceNameCustomRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        // If there's no implementation of Repository Interface,
        // it's RepositoryImplementsInterfaceCustomRule that will return an error.
        if (
            ! empty($node->implements)
            && str_contains($node->name->name ?? '', 'Repository')
        ) {
            foreach ($node->implements as $implementation) {
                $arrayInterfaceName = explode('\\', $implementation->toString());
                $interfaceName = end($arrayInterfaceName);
                if (preg_match('/^(?:Read|Write)[a-zA-Z]+RepositoryInterface$/', $interfaceName)) {
                    return [];
                }
            }

            return [
                CentreonRuleErrorBuilder::message(
                    "At least one Interface name must start with 'Read' or 'Write' and end with 'RepositoryInterface'."
                )->build(),
            ];
        }

        return [];
    }
}
