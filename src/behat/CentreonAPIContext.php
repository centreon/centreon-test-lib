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
     * @var string
     */
    protected $authToken;

    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     * @return string
     */
    public function getAuthToken(): string
    {
        return $this->authToken;
    }

    /**
     * @param string $authToken
     */
    public function setAuthToken(string $authToken): void
    {
        $this->authToken = $authToken;
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
     * Instantiate the http client for testing
     *
     * @Given I have a running instance of Centreon API
     * @throws \Exception
     */
    public function iHaveRunningAPI()
    {
        $base_url = $this->getMinkParameter('api_base');
        if (is_null($base_url))
        {
            throw new \Exception('Unable to find a running container with Centreon Web');
        }

        $client = new Client(['base_url' => $base_url]);
        $this->setClient($client);
        $this->authenticateToApi();
        $headers = [
            'centreon-auth-token' => $this->getAuthToken(),
            'Content-Type' => 'application/json'
        ];
        $client = new Client(['headers' => $headers]);
        $this->setClient($client);
    }

    /**
     * @When /^I make a GET request to "([^"]*)"$/
     */
    public function makeGetRequest($uri)
    {
        $response = $this->getClient()->request('GET', $this->getMinkParameter('api_base') . $uri);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a POST request to "([^"]*)"$/
     */
    public function makePostRequest($uri)
    {
        return true;
    }

    /**
     * @Then /^the response code should be (\d+)$/
     * @throws \Exception
     */
    public function theResponseCodeShouldBe($code)
    {
        if ((string) $this->getResponse()->getStatusCode() !== $code) {
            throw new \Exception('HTTP response code does not match '.$code.
                ' (returned: '.$this->getResponse()->getStatusCode().')');
        }
    }

    /**
     * @Given /^the response has a "([^"]*)" property$/
     */
    public function responseHasProperty($property)
    {
        $data = json_decode($this->getResponse()->getBody(true));
        if (!empty($data)) {
            if (!isset($data->$property)) {
                throw new Exception("Property '".$property."' is not found!\n");
            }
        } else {
            throw new Exception("Response not JSON\n" . $this->getResponse()->getBody(true));
        }
    }
    /**
     * @throws \Exception
     */
    private function authenticateToApi()
    {
        $client = $this->getClient();
        $response = $client->post($this->getMinkParameter('api_base') . '/api/index.php?action=authenticate', [
            'form_params' => [
                'username' => 'admin',
                'password' => 'centreon'
            ]
        ]);
        $responseObj = json_decode($response->getBody());
        if (empty($responseObj->authToken)){
            throw new \Exception('Could not get authentication token from API.');
        }
        $this->setAuthToken($responseObj->authToken);
    }
}