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

namespace Centreon\PHPStan\CustomRules\Traits;

/**
 * This trait implements checkIfInUseCase method to check if a file is
 * a Use Case.
 */
trait UseCaseTrait
{
    /**
     * This method checks if a file is a Use Case.
     *
     * @param string $fileNamePath
     * @return boolean
     */

    // rename method to 'FileIsUseCase'
    private function fileInUseCase(string $fileNamePath): bool
    {
        $fileNamespaced = str_replace('.php', '', $fileNamePath);
        $fileNameArray = array_reverse(explode(DIRECTORY_SEPARATOR, $fileNamespaced));

        return str_contains($fileNamePath, 'UseCase') && ($fileNameArray[0] === $fileNameArray[1]);
    }
}
