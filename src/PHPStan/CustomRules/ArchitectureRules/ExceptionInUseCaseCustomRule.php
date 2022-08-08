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

use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PhpParser\Node\Stmt\ClassMethod;
use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node\Stmt\Catch_;

class ExceptionInUseCaseCustomRule implements Rule
{
    public function getNodeType(): string
    {
        return Node\Stmt\Throw_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->checkIfUseCase($scope->getFile())) {
            while (! $node->getAttribute('parent') instanceof ClassMethod) {
                if ($node->getAttribute('parent') instanceof Catch_) {
                    return [];
                } else {
                    $node = $node->getAttribute('parent');
                }
            }

            return [
                CentreonRuleErrorBuilder::message(
                    'Exception thrown in Use Case must be in try/catch block and must be caught.'
                )->build(),
            ];
        }

        return [];
    }

    public function checkIfUseCase(string $fileName): bool
    {
        $fileNamespaced = str_replace('.php', '', $fileName);
        $fileNameArray = array_reverse(explode(DIRECTORY_SEPARATOR, $fileNamespaced));
        if (str_contains($fileName, 'UseCase') && ($fileNameArray[0] === $fileNameArray[1])) {
            return true;
        }

        return false;
    }
}