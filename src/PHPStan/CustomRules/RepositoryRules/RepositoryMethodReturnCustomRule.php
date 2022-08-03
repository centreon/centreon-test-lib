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

namespace Centreon\PHPStan\CustomRules\RepositoryRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use PhpParser\Node;
use PhpParser\Node\NullableType;
use PhpParser\Node\Stmt\ClassMethod;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;

/**
 * This class implements a custom rule for PHPStan to check if a Repository method find
 * returns null, an object or an array of objects and if method get returns an object or
 * an array of objects.
 */
class RepositoryMethodReturnCustomRule implements Rule
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
        $errors = [];
        if (str_contains($node->name->name, 'Repository')) {
            foreach ($node->getMethods() as $classMethod) {
                $errors = [
                    ...$errors,
                    ...$this->getErrorsForReturnTypeInFindMethod($classMethod),
                    ...$this->getErrorsForReturnTypeInGetMethod($classMethod),
                ];
            }
        }

        return $errors;
    }

    /**
     * This method checks if return statement in find method signature returns null,
     * an object or an array. Otherwise it returns an array of PHPStan RuleErrors.
     *
     * @param ClassMethod $classMethod
     * @return RuleError[]
     */
    private function getErrorsForReturnTypeInFindMethod(ClassMethod $classMethod): array
    {
        if (
            preg_match('/^find/', $classMethod->name->name) &&
            ! (
                (
                    $classMethod->getReturnType() instanceof NullableType &&
                    (
                        // $classMethod->getReturnType()->type->toString() get a string of a class name (i.e. "Host")
                        // in case of a nullable object return statement in method signature.
                        class_exists($classMethod->getReturnType()->type->toString()) ||
                        interface_exists($classMethod->getReturnType()->type->toString())
                    )
                ) ||
                $classMethod->getReturnType()->toString() === 'array'
            )
        ) {
            return [
                CentreonRuleErrorBuilder::message(
                    $classMethod->name->name . " must return null, an object or an array of objects."
                )->line($classMethod->getLine())->build(),
            ];
        }

        return [];
    }

    /**
     * This method checks if return statement in get method signature returns an object or an array.
     * Otherwise it returns an array of PHPStan RuleErrors.
     *
     * @param ClassMethod $classMethod
     * @return RuleError[]
     */
    private function getErrorsForReturnTypeInGetMethod(ClassMethod $classMethod): array
    {
        if (
            preg_match('/^get/', $classMethod->name->name) &&
            ! (
                (
                    // $classMethod->getReturnType()->toString() get a string of a class name (i.e. "Host")
                    // in case of a non-nullable object return statement in method signature.
                    class_exists($classMethod->getReturnType()->toString()) ||
                    interface_exists($classMethod->getReturnType()->toString())
                ) ||
                $classMethod->getReturnType()->toString() === 'array'
            )
        ) {
            return [
                CentreonRuleErrorBuilder::message(
                    $classMethod->name->name . " must return an object or an array of objects."
                )->line($classMethod->getLine())->build(),
            ];
        }

        return [];
    }
}
