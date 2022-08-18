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
use Centreon\PHPStan\CustomRules\Traits\UseCaseTrait;
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node;
use PhpParser\Node\Stmt\TryCatch;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleError;

/**
 * This class implements a custom rule for PHPStan to check if thrown Exception is in
 * try/catch block and if it is caught.
 */
class ExceptionInUseCaseCustomRule implements Rule
{
    use UseCaseTrait;

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
        // check if file is UseCase or check if Exception is thrown in constructor
        if (! $this->fileInUseCase($scope->getFile()) || $this->isInConstructor($scope)) {
            return [];
        }

        // get string representation of Exception class
        $exceptionThrown = $node->expr->class->toCodeString();
        $parentTryCatchNodes = $this->getAllParentTryCatchNodes($node);
        $caughtExceptionTypes = $this->getCaughtExceptionTypes($parentTryCatchNodes);

        if (empty($parentTryCatchNodes)) {
            return [
                $this->getCentreonCustomExceptionError(),
            ];
        }

        foreach ($caughtExceptionTypes as $caughtExceptionType) {
            if (is_a($exceptionThrown, $caughtExceptionType, true)) {
                return [];
            }
        }

        return [
            $this->getCentreonCustomExceptionError(),
        ];
    }

    /**
     * This method gets all the parent TryCatch nodes of a give node and
     * stores then in array.
     *
     * @param Node $node
     * @return TryCatch[]
     */
    private function getAllParentTryCatchNodes(Node $node): array
    {
        $parentTryCatchNodes = [];
        while (! $node->getAttribute('parent') instanceof ClassMethod) {
            if ($node->getAttribute('parent') instanceof TryCatch) {
                $parentTryCatchNodes[] = $node->getAttribute('parent');
            }
            $node = $node->getAttribute('parent');
        }

        return $parentTryCatchNodes;
    }

    /**
     * This method return an array of Exception types caught in all TryCatch nodes.
     *
     * @param TryCatch[] $parentTryCatchNodes
     * @return string[]
     */
    private function getCaughtExceptionTypes(array $parentTryCatchNodes): array
    {
        $caughtExceptionTypes = [];
        foreach ($parentTryCatchNodes as $parentTryCatchNode) {
            foreach ($parentTryCatchNode->catches as $catch) {
                foreach ($catch->types as $type) {
                    $caughtExceptionTypes[] = $type->toCodeString();
                }
            }
        }

        return $caughtExceptionTypes;
    }

    /**
     * This method returns Centreon Custom error for Exception Custom Rule.
     *
     * @return RuleError
     */
    private function getCentreonCustomExceptionError(): RuleError
    {
        return CentreonRuleErrorBuilder::message(
            'Exception thrown in UseCase should be in a try catch block, and must be caught.'
        )->build();
    }

    /**
     * This function checks if an Exception is thrown in the constructor method.
     *
     * @param Scope $scope
     * @return boolean
     */
    private function isInConstructor(Scope $scope): bool
    {
        return ($scope->getFunction() !== null && $scope->getFunctionName() === '__construct');
    }
}
