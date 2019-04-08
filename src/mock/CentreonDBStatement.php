<?php
/**
 * Copyright 2019 Centreon
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
 * @package centreon-test-lib
 * @subpackage test
 */
class CentreonDBStatement extends \PDOStatement
{

    private $query;
    private $resultsets;
    protected $params = null;
    protected $currentResultSet = null;
    protected $fetchObjectName;

    /**
     * Constructor
     *
     * @param array $resultset The resultset for a query
     */
    public function __construct($query, $resultsets)
    {
        $this->query = $query;
        $this->resultsets = $resultsets;
    }

    /**
     * Bind parameter
     */
    public function bindParam($paramno, &$param, $type = NULL, $maxlen = NULL, $driverdata = NULL)
    {
        $this->bindValue($paramno, $param);
    }

    /**
     * Bind value
     */
    public function bindValue($paramno, $param, $type = NULL)
    {
        if (is_null($this->params)) {
            $this->params = array();
        }
        if (is_int($paramno)) {
            $this->params[$paramno - 1] = $param;
        } else {
            $this->params[$paramno] = $param;
        }
    }

    /**
     * Execute statement
     */
    public function execute($bound_input_params = NULL)
    {
        $matching = null;

        foreach ($this->resultsets as $resultset) {
            $result = $resultset->match($this->params);
            if ($result === 2 && is_null($this->currentResultSet)) {
                $this->currentResultSet = $resultset;
            } else if ($result === 1 && is_null($matching)) {
                $matching = $resultset;
            }
        }

        if (is_null($this->currentResultSet)) {
            $this->currentResultSet = $matching;
        }

        if (is_null($this->currentResultSet)) {
            throw new \Exception('The query has not match');
        }

        // trigger callback
        $this->currentResultSet->executeCallback($this->params);

        return true;
    }

    /**
     * Count of updated lines
     *
     * @return int
     */
    public function rowCount()
    {
        return $this->currentResultSet->rowCount();
    }

    /**
     * Return a result
     *
     * @return array
     */
    public function fetchRow()
    {
        if (!is_null($this->currentResultSet)) {
            $data = $this->currentResultSet->fetchRow();

            if ($this->fetchObjectName !== null && is_array($data)) {
                $result = new $this->fetchObjectName;
                $reflection = new \ReflectionClass($result);

                foreach ($data as $key => $val) {
                    $property = $reflection->getProperty($key);
                    $property->setAccessible(true);
                    $property->setValue($result, $val);
                }
            } else {
                $result = $data;
            }

            return $result;
        }

        return false;
    }

    /**
     * Return a result
     *
     * @return array
     */
    public function fetch($how = NULL, $orientation = NULL, $offset = NULL)
    {
        return $this->fetchRow();
    }

    /**
     * Return a result with all rows
     *
     * @return array
     */
    public function fetchAll($how = NULL, $class_name = NULL, $ctor_args = NULL)
    {
        $result = [];
        while ($row = $this->fetch()) {
            $result[] = $row;
        }

        return $result;
    }

    /**
     * Reset the position of resultset
     */
    public function resetResultSet()
    {
        if (!is_null($this->currentResultSet)) {
            $this->currentResultset->fetchRow();
        }
    }

    /**
     * Get count of rows
     *
     * @return int
     */
    public function numRows()
    {
        if (!is_null($this->currentResultSet)) {
            return $this->currentResultSet->numRows();
        }

        return 0;
    }

    /**
     * Close cursor
     */
    public function closeCursor()
    {
        return;
    }

    /**
     * Set fetch mode
     *
     * @param mixed $mode
     * @param mixed $params
     * @return bool
     */
    public function setFetchMode($mode, $params = NULL): bool
    {
        $this->fetchObjectName = $params;

        return true;
    }
}
