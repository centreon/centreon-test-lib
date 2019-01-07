<?php
/**
 * Copyright 2016-2018 Centreon
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

use GuzzleHttp\Client;

class CentreonAPIContext extends CentreonContext
{
    /**
     * @var Client
     */
    protected $client;

    /**
     * @var mixed
     */
    protected $response;

    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
        $baseUrl = $parameters['base_url'];
        $client = new Client(['base_url' => $baseUrl]);
        $this->client = $client;
    }

    /**
     * @return mixed
     */
    public function getResponse()
    {
        return $this->response;
    }

    /**
     * @param mixed $response
     */
    public function setResponse($response): void
    {
        $this->response = $response;
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient(Client $client): void
    {
        $this->client = $client;
    }

    /**
     * @When /^I make a GET request to "([^"]*)"$/
     */
    public function makeGetRequest($uri)
    {
        $response = $this->getClient()->get($uri);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a POST request to "([^"]*)"$/
     */
    public function makePostRequest($uri)
    {
        $response = $this->getClient()->get($uri);
        $this->setResponse($response);
    }

    /**
     * @Then /^the response code should be (\d+)$/
     */
    public function theResponseCodeShouldBe($code)
    {
        if (intval($this->getResponse()->getStatusCode()) !== $code) {
            throw new \Exception('HTTP response code does not match '.$code.
                ' (returned: '.$this->getResponse()->getStatusCode().')');
        }
    }
}