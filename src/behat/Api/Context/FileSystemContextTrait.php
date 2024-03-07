<?php

/*
 * Copyright 2005 - 2020 Centreon (https://www.centreon.com/)
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

namespace Centreon\Test\Behat\Api\Context;

use Centreon\Test\Behat\Container;

Trait FileSystemContextTrait
{
    /**
     * @return Container
     */
    abstract protected function getContainer();

    /**
     * Waiting an action
     *
     * @param \Closure $closure The function to execute for test the loading.
     * @param string $timeoutMsg The custom message on timeout.
     * @param int $wait The timeout in seconds.
     * @return bool
     * @throws \Exception
     */
    abstract public function spin(\Closure $closure, string $timeoutMsg = 'Load timeout', int $wait = 60);

    /**
     * Check content of a file
     *
     * @param string $filePath Path of file to check
     * @param string $regexp Regular expression to use for matching
     * @param int $tries Count of tries
     *
     * @Then /^the content of file "(\S+)" should match "(\S+)"(?: \(tries: (\d+)\))?$/
     */
    public function theContentOfFileShouldMatch(string $filePath, string $regexp, int $tries = 10): void
    {
        $this->spin(
            function() use ($filePath, $regexp) {
                $content = $this->getContainer()->execute(
                    'cat ' . $filePath,
                    $this->webService
                )['output'];

                return preg_match($regexp, $content);
            },
            'Cannot find "' . $regexp . '"  in "' . $filePath . '"',
            $tries
        );
    }
}
