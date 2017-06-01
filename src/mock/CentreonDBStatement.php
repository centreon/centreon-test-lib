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
    private $resultset;
    private $pos = 0;
    
    /**
     * Constructor
     *
     * @param array $resultset The resultset for a query
     */
    public function __construct($query, $resultset)
    {
        $this->query = $query;
        $this->resultset = $resultset;
    }

    /*
     * Bind parameter
     */
    public function bindParam()
    {

    }

    /*
     * Bind value
     */
    public function bindValue()
    {

    }

    /*
     * Execute statement
     */
    public function execute()
    {
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
     * Reset the position of resultset
     */
    public function resetResultSet()
    {
        $this->pos = 0;
    }
    
    /**
     * 
     * @return int
     */
    public function numRows()
    {
        return count($this->resultset);
    }
}