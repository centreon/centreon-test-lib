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
 *
 */

declare(strict_types=1);

namespace Centreon\PhpCsFixer;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

/**
 * Php-Cs-Fixer helper class to generate a shared configuration.
 */
class PhpCsFixerHelper
{
    /**
     * This method takes an instance of Finder class and Php-Cs-Fixer ruleset
     * and return a configuration to share between different projects.
     *
     * @param Finder $finder
     * @param array $rules
     * @return Config
     */
    public static function styles(Finder $finder, array $rules = []): Config
    {
        $rules = array_merge(require __DIR__ . '/ruleset.php', $rules);

        return (new Config())
            ->setFinder($finder)
            ->setRiskyAllowed(true)
            ->setRules($rules);
    }
}
