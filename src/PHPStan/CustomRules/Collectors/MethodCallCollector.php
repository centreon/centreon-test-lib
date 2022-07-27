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

namespace Centreon\PHPStan\CustomRules\Collectors;

use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Collectors\Collector;

/**
 * This class implements Collector interface to collect the information about
 * method calls in UseCases.
 */
class MethodCallCollector implements Collector
{
    private const USE_CASE = 'UseCase';

    /**
     * @inheritDoc
     *
     * @return string
     */
    public function getNodeType(): string
    {
        return \PhpParser\Node\Expr\MethodCall::class;
    }

    /**
     * @inheritDoc
     *
     * @param Node $node
     * @param Scope $scope
     * @return array|null
     */
    public function processNode(Node $node, Scope $scope): ?string
    {
        // i.e. "Centreon\PHPStan\UseCase\MyClass"
        $namespace = $scope->getNamespace();
        // i.e. the last "MyClass" in "Centreon\PHPStan\UseCase\MyClass"
        $namespaceEnd = end(explode('\\', $scope->getNamespace()));

        // i.e. the last "MyClass" in "Centreon\PHPStan\UseCase\MyClass\MyClass"
        $className = end(explode('\\', $scope->getClassReflection()->getName()));

        if (strpos($namespace, self::USE_CASE) && ($className === $namespaceEnd)) {
            // returns an array with method call string
            return $node->name->name;
        }

        return null;
    }
}