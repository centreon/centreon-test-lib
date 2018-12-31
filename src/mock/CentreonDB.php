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
class CentreonDB extends \CentreonDB
{
    private $queries = array();

    /**
     * Constructor
     *
     * @param string $db
     * @param int $retry
     * @param bool $silent
     */
    public function __construct($db = "centreon", $retry = 3, $silent = false)
    {
    }

    /**
     * Stub for function query
     *
     * @param string $query The query to execute
     * @param array $parameters
     * @return CentreonDBResultSet The resultset
     */
    public function query($queryString = NULL, $parameters = NULL)
    {
        return $this->execute($queryString, null);
    }

    /**
     * Stub escape function
     *
     * @param string $string The string to escape
     * @param bool $htmlSpecialChars
     * @return string The string escaped
     */
    public static function escape($string, $htmlSpecialChars = false)
    {
        return $string;
    }

    /**
     * Stub quote function
     *
     * @param string $string The string to escape
     * @param string $paramtype
     * @return string The string escaped
     */
    public function quote($string, $paramtype = NULL)
    {
        return "'" . $string . "'";
    }

    /**
     * Add a resultset to the mock
     *
     * @param string $query The query to catch
     * @param array $result The resultset
     * @param array $params The parameters of query, if not set :
     *   * the query has not parameters
     *   * the result is generic for the query
     */
    public function addResultSet($query, $result, $params = null)
    {
        if (!isset($this->queries[$query])) {
            $this->queries[$query] = array();
        }
        $this->queries[$query][] = new CentreonDBResultSet($result, $params);
        
        return $this;
    }

    /**
     * @param $query
     * @param array $options
     * @return CentreonDBStatement
     * @throws \Exception
     */
    public function prepare($statement, $options = NULL)
    {
        if (!isset($this->queries[$statement])) {
            throw new \Exception('Query is not set.' . "\nQuery : " . $statement);
        }
        return new CentreonDBStatement($statement, $this->queries[$statement]);
    }

    /**
     * Execute a query with values
     *
     * @param string $query The query to execute
     * @param array $values The list of values for the query
     * @return CentreonDBResultSet The resultset
     */
    public function execute($query, $values = null)
    {
        if (!isset($this->queries[$query])) {
            throw new \Exception('Query is not set.' . "\nQuery : " . $query);
        }
        /* Find good query */
        $matching = null;
        foreach ($this->queries[$query] as $resultSet) {
            $result = $resultSet->match($values);
            if ($result === 2) {
                return $resultSet;
            } else if  ($result === 1 && is_null($matching)) {
                $matching = $resultSet;
            }
        }
        if (is_null($matching)) {
            throw new \Exception('Query is not set.' . "\nQuery : " . $query);
        }
        return $matching;
    }

    /**
     *
     * @param type $enable
     * @return type
     */
    public function autoCommit($enable)
    {
        return;
    }

    /**
     *
     * @return type
     */
    public function commit()
    {
        return;
    }

    /**
     *
     * @return type
     */
    public function rollback()
    {
        return;
    }

}
