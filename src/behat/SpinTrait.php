<?php

/**
 * Copyright 2016-2023 Centreon
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
 */

namespace Centreon\Test\Behat;

use Centreon\Test\Behat\Exception\SpinStopException;

trait SpinTrait
{
    /**
     * Waiting an action
     *
     * @param callable $closure The function to execute for test the loading.
     * @param string $timeoutMsg The custom message on timeout.
     * @param int $wait The timeout in seconds.
     * @return bool
     * @throws \Exception
     */
    public function spin(callable $closure, string $timeoutMsg = 'Load timeout', int $wait = 60)
    {
        $limit = time() + $wait;
        $lastException = null;
        while (time() <= $limit) {
            try {
                if ($closure($this)) {
                    return true;
                }
            } catch (SpinStopException $e) {
                // stop spining
                throw $e;
            } catch (\Throwable $e) {
                $lastException = $e;
            }
            \usleep(100_000);
        }
        if (is_null($lastException)) {
            throw new \Exception($timeoutMsg);
        } else {
            throw new \Exception(
                $timeoutMsg . ': ' . $lastException->getMessage() . ' (code ' .
                $lastException->getCode() . ', file ' . $lastException->getFile() .
                ':' . $lastException->getLine() . ')'
            );
        }
    }
}
