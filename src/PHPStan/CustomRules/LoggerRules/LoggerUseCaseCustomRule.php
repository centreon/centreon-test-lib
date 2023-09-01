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
use Centreon\PHPStan\CustomRules\Collectors\MethodCallCollector;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check if a UseCase use LoggerTrait
 * and call its methods.
 *
 * @implements Rule<CollectedDataNode>
 */
class LoggerUseCaseCustomRule implements Rule
{
    use CentreonRuleTrait;

    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $loggerMethods = $this->getLoggerTraitMethods();
        $methodCallsByFile = $node->get(MethodCallCollector::class);

        $errors = [];
        foreach ($methodCallsByFile as $file => $methodCalls) {
            if (
                $this->fileIsUseCase($file)
                && empty(array_intersect($loggerMethods, $methodCalls))
            ) {
                $errors[] = CentreonRuleErrorBuilder::message(
                    'Class must contain a Logger trait and call at least one of its methods.'
                )->file($file)->line(0)->build();
            }
        }

        return $errors;
    }
}
