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
namespace Centreon\Test\Behat;

use Behat\Behat\Context\Context;
use GuzzleHttp\Client;

/**
 * Context for rest call in acceptance tests
 */
class RestContext implements Context
{
    protected $restClient;
    protected $parameters;
    protected $restParameters = array();
    
    /**
     * Constructor
     *
     * @param array $parameters The list of parameters for the context
     */
    public function __construct($parameters = array())
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Get the rest client
     *
     * @return GuzzleHttp\Client The http object
     */
    public function getRestClient()
    {
        if (!isset($this->restClient)) {
            $this->restClient = new Client(
                array(
                    'base_uri' => $this->restParameters['base_url']
                )
            );
        }
        return $this->restClient;
    }
    
    /**
     * Set extension parameters
     *
     * @param array $parameters The extension parameters
     */
    public function setRestParameters($parameters)
    {
        $this->restParameters = $parameters;
    }
    
    /**
     * Get extension parameters
     *
     * @return array The extension parameters
     */
    public function getRestParamaters()
    {
        return $this->restParameters;
    }
}
