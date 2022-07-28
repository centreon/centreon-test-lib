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

use PhpParser\Node;
use ReflectionClass;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;
use Centreon\Domain\Log\LoggerTrait;
use Centreon\PHPStan\CustomRules\CustomRuleErrorMessage;

class LogMethodInCatchCustomRule implements Rule
{
    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return Node\Stmt\Catch_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        $loggerMethods = $this->getLoggerTraitMethods(LoggerTrait::class);

        foreach ($node->stmts as $stmt) {
            // $stmt->expr correcponds to MethodCall node;
            // ->name->name gets method name string;
            // in case of other statement or expression null is passed to in_array()
            if (in_array($stmt->expr->name->name, $loggerMethods)) {
                return $errors;
            }
        }
        $errors[] = RuleErrorBuilder::message(
                        CustomRuleErrorMessage::buildErrorMessage(
                            'Catch block must contain a Logger trait method call.'
                        )
                    )->build();
        return $errors;
    }

    /**
     * This method creates a Reflection of Logger Trait, extract the list of its methods
     * and stores them as array of strings.
     *
     * @param string $class
     * @return array
     */
    public function getLoggerTraitMethods(string $class): array
    {
        $loggerMethods = [];
        $loggerTraitReflectionClass = new ReflectionClass($class);
        $loggerTraitReflectionMethods = $loggerTraitReflectionClass->getMethods();
        foreach ($loggerTraitReflectionMethods as $loggerTraitReflectionMethod) {
            $loggerMethods[] = $loggerTraitReflectionMethod->name;
        }

        return $loggerMethods;
    }
}