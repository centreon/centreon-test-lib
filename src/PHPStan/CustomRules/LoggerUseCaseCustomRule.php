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
use Centreon\PHPStan\CustomRules\Collectors\MethodCallCollector;
use Centreon\PHPStan\CustomRules\Traits\GetLoggerMethodsTrait;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check if a UseCase use LoggerTrait
 * and call its methods.
 */
class LoggerUseCaseCustomRule implements Rule
{
    use GetLoggerMethodsTrait;

    private const USE_CASE = 'UseCase';
    /**
     * @inheritDoc
     */
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    /**
     * @inheritDoc
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        $loggerMethods = $this->getLoggerTraitMethods();

        $methodCallData = $node->get(MethodCallCollector::class);
        foreach ($methodCallData as $file => $methodCalls) {
            $fileName = str_replace('.php', '', $file);
            $fileNameArray = array_reverse(explode(DIRECTORY_SEPARATOR, $fileName));
            // check if full file name contains 'UseCase' and the last two elements of file path are equal
            // check if the intersection of $loggerMethods and $methodCalls is empty
            if (
                strpos($file, self::USE_CASE) !== false &&
                ($fileNameArray[0] === $fileNameArray[1]) &&
                empty(array_intersect($loggerMethods, $methodCalls))
            ) {
                $errors[] = CentreonRuleErrorBuilder::message(
                    'Class must contain a Logger trait and call at least one of its methods.'
                )->file($file)->line(0)->build();
            }
        }

        return $errors;
    }
}
