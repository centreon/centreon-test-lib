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
 * Mock class for resultset
 *
 * @author Centreon
 * @version 1.0.0
 * @package centreon-license-manager
 * @subpackage test
 */
class CentreonDBResultSet
{
    private $resultset = array();
    private $params = null;
    private $pos = 0;

    /**
     * Constructor
     *
     * @param array $resultset The resultset for a query
     * @param array $params The parameters of query, if not set :
     *   * the query has not parameters
     *   * the result is generic for the query
     */
    public function __construct($resultset, $params = null)
    {
        $this->resultset = $resultset;
        $this->params = $params;
        if (!is_null($this->params)) {
            ksort($this->params);
        }
    }

    /**
     * Return a result
     *
     * @return array
     */
    public function fetchRow()
    {
        if (!isset($this->resultset[$this->pos])) {
            return false;
        }
        return $this->resultset[$this->pos++];
    }

    /**
     * Return a result
     *
     * @return array
     */
    public function fetch()
    {
        return $this->fetchRow();
    }

    /**
     * Return all results
     *
     * @return array
     */
    public function fetchAll()
    {
        $this->pos = count($this->resultset);
        return $this->resultset;
    }

    /**
     * Reset the position of resultset
     */
    public function resetResultSet()
    {
        $this->pos = 0;
    }

    /**
     * Return the number of rows of the result set
     *
     * @return int
     */
    public function numRows()
    {
        return count($this->resultset);
    }

    /**
     * If the queries match
     *
     * @param array $params The parameters of current query
     * @return int The level of match
     *   * 0 - Not match
     *   * 1 - Match by default (the result set params is null by the query has $params)
     *   * 2 - Exact match
     */
    public function match($params = null)
    {
        if (is_null($params) && is_null($this->params)) {
            return 2;
        }
        if (is_null($this->params)) {
            return 1;
        }
        if (!is_null($params)) {
            ksort($params);
        }
        if ($this->params === $params) {
            return 2;
        }
        return 0;
    }
}
