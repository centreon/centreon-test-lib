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

namespace Centreon\PHPStan\CustomRules\LoggerRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\CentreonRuleTrait;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check if a catch block contains
 * Logger trait method call.
 *
 * @implements Rule<Node\Stmt\Catch_>
 */
class LogMethodInCatchCustomRule implements Rule
{
    use CentreonRuleTrait;

    public function getNodeType(): string
    {
        return Node\Stmt\Catch_::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $loggerMethods = $this->getLoggerTraitMethods();

        foreach ($node->stmts as $stmt) {
            if (! isset($stmt->expr)) {
                continue;
            }

            // $stmt->expr corresponds to MethodCall node;
            // ->name->name gets method name string;
            // in case of other statement or expression null is passed to in_array()
            if (in_array($stmt->expr->name->name, $loggerMethods, true)) {
                return [];
            }
        }

        return [
            CentreonRuleErrorBuilder::message('Catch block must contain a Logger trait method call.')->build(),
        ];
    }
}
