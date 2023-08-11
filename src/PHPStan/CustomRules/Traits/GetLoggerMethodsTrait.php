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

namespace Centreon\PHPStan\CustomRules\Traits;

use Centreon\Domain\Log\LoggerTrait;

/**
 * This trait implements getLoggerTraitMethods used in Log Custom Rules.
 */
trait GetLoggerMethodsTrait
{
    /** @var array<string>|null */
    private ?array $loggerTraitMethods = null;

    /**
     * This method creates a Reflection of Logger Trait, extract the list of its methods
     * and stores them as array of strings.
     *
     * @return string[]
     */
    public function getLoggerTraitMethods(): array
    {
        if (null === $this->loggerTraitMethods) {
            $this->loggerTraitMethods = [];

            $methods = (new \ReflectionClass(LoggerTrait::class))->getMethods();
            foreach ($methods as $method) {
                $this->loggerTraitMethods[] = $method->name;
            }
        }

        return $this->loggerTraitMethods;
    }
}
