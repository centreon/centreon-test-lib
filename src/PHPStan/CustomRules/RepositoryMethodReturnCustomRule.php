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
use PhpParser\Node\Stmt\ClassMethod;
use PhpParser\Node\NullableType;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

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
        if (strpos($node->name->name, 'Repository') !== false) {
            foreach ($node->getMethods() as $classMethod) {
                if (
                    preg_match('/^find/', $classMethod->name->name) &&
                    ! (
                        (
                            $classMethod->getReturnType() instanceof NullableType &&
                            (
                                class_exists($classMethod->getReturnType()->type->toString()) ||
                                interface_exists($classMethod->getReturnType()->type->toString())
                            )
                        ) ||
                        $classMethod->getReturnType()->toString() === 'array'
                    )
                ) {
                    $errors[] =
                        CentreonRuleErrorBuilder::message(
                            $classMethod->name->name . " must return null, an object or an array of objects."
                        )->line($classMethod->getLine())->build();
                }

                if (
                    preg_match('/^get/', $classMethod->name->name) &&
                    ! (
                        (
                            class_exists($classMethod->getReturnType()->toString()) ||
                            interface_exists($classMethod->getReturnType()->toString())
                        ) ||
                        $classMethod->getReturnType()->toString() === 'array'
                    )
                ) {
                    $errors[] =
                        CentreonRuleErrorBuilder::message(
                            $classMethod->name->name . " must return and object or an array of objects."
                        )->line($classMethod->getLine())->build();
                }
            }
        }

        return $errors;
    }
}