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

namespace Centreon\PHPStan\CustomRules\ArchitectureRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check if UseCase, Request, Response
 * or Controller classes are final.
 */
class FinalClassCustomRule implements Rule
{

    /**
     * @inheritDoc
     */
    public function getNodeType(): string
    {
        return Node\Stmt\Class_::class;
    }

    /**
     * @inheritDoc
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if (
            (
                str_ends_with($node->name->name, 'Request')
                || str_ends_with($node->name->name, 'Response')
                || str_ends_with($node->name->name, 'Controller')
                || $this->checkIfUseCase($node->namespacedName->toString())
            )
            && ! $node->isFinal()
        ) {
            return [
                CentreonRuleErrorBuilder::message(
                    'Class ' . $node->name->name . ' must be final.'
                )->build(),
            ];
        }
        return [];
    }

    /**
     * This method checks if a class is a Use Case
     *
     * @param string $namespacedName
     * @return boolean
     */
    private function checkIfUseCase(string $namespacedName): bool
    {
        $namespacedNameArray = array_reverse(explode('\\', $namespacedName));
        if (str_contains($namespacedName, 'UseCase') && ($namespacedNameArray[0] == $namespacedNameArray[1])) {
            return true;
        }
        return false;
    }
}
