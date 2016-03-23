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
namespace Centreon\Test\Behat\Initializer;

use Behat\Behat\Context\Context;
use Behat\Behat\Context\Initializer\ContextInitializer;

/**
 * Database initializer
 */
class DatabaseInitializer implements ContextInitializer
{
    private $dbconn;
    private $parameters;
    
    public function __construct($parameters)
    {
        $this->parameters = $parameters;
        
        /* Build DSN */
        $dsn = $this->parameters['driver'] . ':dbname=' . $this->parameters['dbname'] . ';host=' .
            $this->parameters['host'];
        if (isset($this->parameters['port']) && false === is_null($this->parameters['port'])) {
            $dsn .= ';port=' . $this->parameters['port'];
        }
        $this->dbconn = new \PDO(
            $dsn,
            $this->parameters['username'],
            $this->parameters['password']
        );
        $this->dbconn->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
    }
    
    public function initializeContext(Context $context)
    {
        if (method_exists($context, 'setDatabase')) {
            $context->setDatabase($this->dbconn);
        }
        if (method_exists($context, 'setDatabaseParameters')) {
            $context->setDatabaseParameters($this->parameters);
        }
    }
}