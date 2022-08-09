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
use Centreon\PHPStan\CustomRules\Traits\CheckIfInUseCaseTrait;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;

/**
 * This class implements a custom rule for PHPStan to check if thrown Exception is in
 * try/catch block and if it is caught.
 */
class ExceptionInUseCaseCustomRule implements Rule
{
    use CheckIfInUseCaseTrait;

    /**
     * @inheritDoc
     */
    public function getNodeType(): string
    {
        return Node\Stmt\Throw_::class;
    }

    /**
     * @inheritDoc
     */
    public function processNode(Node $node, Scope $scope): array
    {
        if ($this->checkIfInUseCase($scope->getFile())) {
            $exceptionThrown = $node->expr->class->toString();
            $parentTryCatchArray = $this->getAllParentTryCatch($node);
            if (empty($parentTryCatchArray)) {
                return [
                    CentreonRuleErrorBuilder::message(
                        'Exception thrown in UseCase should be in a try catch block, and must be caught.'
                    )->build(),
                ];
            }

            foreach ($parentTryCatchArray as $parentTryCatchNode) {
                foreach ($parentTryCatchNode->catches as $catch) {
                    foreach ($catch->types as $type) {
                        if ($this->exceptionThrownCanBeCaught($exceptionThrown, $type->toString())) {
                            return [];
                        }
                    }
                }
            }

            return [
                CentreonRuleErrorBuilder::message(
                    'Exception thrown in UseCase should be in a try catch block, and must be caught.'
                )->build(),
            ];
        }

        return [];
    }

    /**
     * This method gets all the parent TryCatch nodes of a give node and
     * stores then in array.
     *
     * @param Node $node
     * @return TryCatch[]
     */
    private function getAllParentTryCatch(Node $node): array
    {
        $parentTryCatchArray = [];
        while (! $node->getAttribute('parent') instanceof ClassMethod) {
            if ($node->getAttribute('parent') instanceof TryCatch) {
                $parentTryCatchArray[] = $node->getAttribute('parent');
            }
            $node = $node->getAttribute('parent');
        }
        return $parentTryCatchArray;
    }

    /**
     * This method checks if an Exception thrown can be caught by catch block.
     *
     * @param string $exceptionThrown
     * @param string $exceptionCaught
     * @return boolean
     */
    private function exceptionThrownCanBeCaught(string $exceptionThrown, string $exceptionCaught): bool
    {
        $instanceExceptionThrown = new $exceptionThrown;
        $instanceExceptionCaught = new $exceptionCaught;
        if ($instanceExceptionThrown instanceof $instanceExceptionCaught) {
            return true;
        }

        return false;
    }
}
