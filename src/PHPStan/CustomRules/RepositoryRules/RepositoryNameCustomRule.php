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
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;

/**
 * This class implements a custom rule for PHPStan to check Repository naming requirement
 * it must start with data storage prefix, followed by action and context mentions, and finish
 * by 'Repository' mention.
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
        if (
            str_contains($node->name->name, 'Repository') &&
            ! preg_match('/^[a-zA-Z]{2,}(?:Read|Write)[a-zA-Z]+Repository$/', $node->name->name)
        ) {
            return [
                CentreonRuleErrorBuilder::message(
                    'Repository name must start with data storage prefix(i.e. \'Db\', \'Redis\', etc.), '
                        . 'followed by \'Read\' or \'Write\' and context mention.'
                )->build(),
            ];
        }

        return [];
    }
}