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
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check Repository naming requirements:
 * - it must start with data storage prefix, followed by action and context mentions
 * - it must match the Interface name it implements without 'Interface' ending and data storage prefix
 */
class RepositoryNameCustomRule implements Rule
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
        // if there's no implementation of Repository Interface it's RepositoryImplementsInterfaceCustomRule
        // that will return an error.
        if (strpos($node->name->name, 'Repository') !== false && ! empty($node->implements)) {
            $interfaceImplementations = [];
            foreach ($node->implements as $implementation) {
                $interfaceImplementations[] = end(explode('\\', $implementation->toString()));
            }

            if (! preg_match('/(^[a-zA-Z]{2,})(Read|Write)([a-zA-Z]{1,})(Repository)$/', $node->name->name, $matches)) {
                return [
                    CentreonRuleErrorBuilder::message(
                        'Repository name must start with data storage prefix(i.e. \'Db\', \'Redis\', etc.),' .
                        ' followed by \'Read\' or \'Write\' and context mention.'
                    )->tip(
                        'Repository name must be the same as implemented Repository Interface without \'Interface\'' .
                        ' suffix and data storage information prefix (i.e. \'Db\', \'Redis\', etc.).'
                    )->build(),
                ];
            }
            // remove $matches[0] = matched Repository name
            array_shift($matches);
            // remove $matches[1] = data storage prefix
            array_shift($matches);
            // construct a name that should match Interface name
            $nameToMatch = implode("", $matches) . 'Interface';

            if (! in_array($nameToMatch, $interfaceImplementations)) {
                return [
                    CentreonRuleErrorBuilder::message(
                        'Repository name must start with data storage prefix(i.e. \'Db\', \'Redis\', etc.),' .
                        ' followed by \'Read\' or \'Write\' and context mention.'
                    )->tip(
                        'Repository name must be the same as implemented Repository Interface without \'Interface\'' .
                        ' suffix and data storage information prefix (i.e. \'Db\', \'Redis\', etc.).'
                    )->build(),
                ];
            }

            return [];
        }

        return [];
    }
}