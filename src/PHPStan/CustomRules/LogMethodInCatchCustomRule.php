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

use Centreon\PHPStan\CustomRules\AbstractGetLoggerMethodsClass;
use Centreon\PHPStan\CustomRules\CustomRuleErrorMessage;
use PhpParser\Node;
use PHPStan\Rules\Rule;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * This class implements a custom rule for PHPStan to check if a catch block contains
 * Logger trait method call.
 */
class LogMethodInCatchCustomRule extends AbstractGetLoggerMethodsClass implements Rule
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
        $loggerMethods = $this->getLoggerTraitMethods();

        foreach ($node->stmts as $stmt) {
            // $stmt->expr corresponds to MethodCall node;
            // ->name->name gets method name string;
            // in case of other statement or expression null is passed to in_array()
            if (in_array($stmt->expr->name->name, $loggerMethods)) {
                return $errors;
            }
        }

        return RuleErrorBuilder::message(
            CustomRuleErrorMessage::buildErrorMessage(
                'Catch block must contain a Logger trait method call.'
            )
        )->build();
    }
}
