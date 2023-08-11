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

namespace Centreon\PHPStan\CustomRules\ArchitectureRules;

use Centreon\PHPStan\CustomRules\CentreonRuleErrorBuilder;
use Centreon\PHPStan\CustomRules\Collectors\UseUseCollector;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Node\CollectedDataNode;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;

/**
 * This class implements a custom rule for PHPStan to check that classes in Domain layer
 * do not call namespaces from Application or Infrastructure layers.
 *
 * @implements Rule<CollectedDataNode>
 */
class DomainCallNamespacesCustomRule implements Rule
{
    public function getNodeType(): string
    {
        return CollectedDataNode::class;
    }

    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        $useUseData = $node->get(UseUseCollector::class);
        foreach ($useUseData as $file => $useUse) {
            if (str_contains((string) $file, DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR)) {
                foreach ($useUse as [$line, $useNamespace]) {
                    if (
                        str_contains((string) $useNamespace, '\\Application\\')
                        || str_contains((string) $useNamespace, '\\Infrastructure\\')
                    ) {
                        $errors[] = CentreonRuleErrorBuilder::message(
                            'Domain must not call Application or Infrastructure namespaces.'
                        )->line($line)->file($file)->build();
                    }
                }
            }
        }

        return $errors;
    }
}
