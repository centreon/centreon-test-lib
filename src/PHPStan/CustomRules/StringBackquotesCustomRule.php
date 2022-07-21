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

use Centreon\PHPStan\CustomRules\CustomRuleErrorMessage;
use PhpParser\Node;
use PHPStan\Analyser\Scope;
use PHPStan\Rules\Rule;
use PHPStan\Rules\RuleError;
use PHPStan\Rules\RuleErrorBuilder;

/**
 * This class implements custom rule for PHPStan to check if variable :db or :dbstg
 * are enclosed in backquotes.
 */
class StringBackquotesCustomRule implements Rule
{
    private const CENTREON_CONFIG_DATABASE = ':db';
    private const CENTREON_REALTIME_DATABASE = ':dbstg';

    /**
     * @inheritDoc
     */
    public function getNodeType(): string
    {
        return \PhpParser\Node\Scalar\String_::class;
    }

    /**
     * @inheritDoc
     */
    public function processNode(Node $node, Scope $scope): array
    {
        $errors = [];
        if (preg_match_all(
            '/(' . self::CENTREON_REALTIME_DATABASE . '|' . self::CENTREON_CONFIG_DATABASE . ')\./',
            $node->value,
            $matches
        )) {
            /**
             * $matches[0] = [':dbstg.',':db.']
             * $matches[1] = [':dbstg',':db']
             */
            if (! empty($matches[1])) {
                foreach ($matches[1] as $matchSubGroup) {
                    $errors[] = RuleErrorBuilder::message(
                        CustomRuleErrorMessage::buildErrorMessage($matchSubGroup, "must be enclosed in backquotes.")
                    )->build();
                }
            }
        }

        return $errors;
    }
}
