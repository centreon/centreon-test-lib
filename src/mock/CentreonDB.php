<?php
/**
 * Copyright 2016 Centreon
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
namespace Centreon\Test\Mock;

/**
 * Mock class for dbconn
 *
 * @author Centreon
 * @version 1.0.0
 * @package centreon-license-manager
 * @subpackage test
 */
class CentreonDB
{
    private $queries = array();
    
    /**
     * Stub for function query
     *
     * @param string $query The query to execute
     * @return CentreonDBResultSet The resultset
     */
    public function query($query)
    {
        if (!isset($this->queries[$query])) {
            throw new \Exception('Query is not set.' . "\nQuery : " . $query);
        }
        $this->queries[$query]->resetResultSet();
        return $this->queries[$query];
    }
    
    /**
     * Stub escape function
     *
     * @param string $stub The string to escape
     * @return string The string escaped
     */
    public function escape($string)
    {
        return $string;
    }
    
    /**
     * Add a resultset to the mock
     *
     * @param string $query The query to catch
     * @param array $result The resultset
     */
    public function addResultSet($query, $result)
    {
        $this->queries[$query] = new CentreonDBResultSet($result);
    }
}
