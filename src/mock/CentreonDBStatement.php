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
class CentreonDBStatement
{
    private $query;
    private $resultsets;
    protected $params = null;
    protected $currentResultSet = null;

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

    /*
     * Bind parameter
     */
    public function bindParam($param, $value)
    {
        $this->bindValue($param, $value);
    }

    /*
     * Bind value
     */
    public function bindValue($param, $value)
    {
        if (is_null($this->params)) {
            $this->params = array();
        }
        if (is_int($param)) {
            $this->params[$param - 1] = $value;
        } else {
            $this->params[$param] = $value;
        }
    }

    /*
     * Execute statement
     */
    public function execute()
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

        return true;
    }

    /*
     * Count of updated lines
     */
    public function rowCount()
    {
        return 1;
    }

    /**
     * Return a result
     *
     * @return array
     */
    public function fetchRow()
    {
        if (!is_null($this->currentResultSet)) {
            return $this->currentResultSet->fetchRow();
        }
        return false;
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
     * Reset the position of resultset
     */
    public function resetResultSet()
    {
        if (!is_null($this->currentResultSet)) {
            $this->currentResultset->fetchRow();
        }
    }

    /**
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
     *
     */
    public function closeCursor()
    {
        return ;
    }
}
