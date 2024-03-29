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

use Behat\Gherkin\Node\TableNode;
use GuzzleHttp\Client;
use Behat\Gherkin\Node\PyStringNode;

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

    /*
     * @var string
     */
    protected $requestPayload;

    /*
     * @var string
     */
    protected $responsePayload;

    /*
     * @var array
     */
    protected $files;

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
     * @return mixed
     */
    public function getFiles()
    {
        return $this->files;
    }

    /**
     * @param mixed $files
     */
    public function setFiles($files): void
    {
        $this->files = $files;
    }

    /**
     * @return mixed
     */
    public function getRequestPayload()
    {
        return $this->requestPayload;
    }

    /**
     * @param mixed $requestPayload
     */
    public function setRequestPayload($requestPayload): void
    {
        $this->requestPayload = $requestPayload;
    }

    /**
     * @return mixed
     */
    public function getResponsePayload()
    {
        return $this->responsePayload;
    }

    /**
     * @param mixed $responsePayload
     */
    public function setResponsePayload($responsePayload): void
    {
        $this->responsePayload = $responsePayload;
    }

    /**
     * Instantiate the http client for testing
     *
     * @Given I have a running instance of Centreon API
     * @throws \Exception
     */
    public function iHaveRunningAPI(): void
    {
        $base_url = $this->getMinkParameter('api_base');
        if (is_null($base_url)) {
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
     * @Given /^the response has a "([^"]*)" property$/
     */
    public function responseHasProperty($property): void
    {
        $data = json_decode($this->getResponse()->getBody(true));
        if (!empty($data)) {
            if (!isset($data->$property)) {
                throw new \Exception("Property '".$property."' is not found!\n");
            }
        } else {
            throw new \Exception("Response not JSON\n" . $this->getResponse()->getBody(true));
        }
    }

    /**
     * @Given /^the property "([^"]*)" has value$/
     */
    public function responseHasKeys($property, PyStringNode $propVal): void
    {
        $data = json_decode($this->getResponse()->getBody(true), true);
        if (!isset($data[$property])) {
            throw new \Exception(
                "Property '".$property."' is not found in the list ("
                . join(', ', array_keys($data))
                . ")\n"
            );
        }

        if ($data[$property] !== json_decode($propVal, true)) {
            throw new \Exception(
                "Value of '{$property}' is not correct!\n" .
                "Expected to be: " . json_encode($data[$property]) . "\n" .
                "Received: " . $propVal . "\n"
            );
        }
    }

    /**
     * @Given /^the property "([^"]*)" has value matched to the pattern$/
     */
    public function responseHasKeysByWildcard($property, PyStringNode $propVal): void
    {
        $data = json_decode($this->getResponse()->getBody(true), true);
        if (!isset($data[$property])) {
            throw new \Exception(
                "Property '".$property."' is not found in the list ("
                . join(', ', array_keys($data))
                . ")\n"
            );
        }

        if (fnmatch($propVal, json_encode($data[$property]))) {
            throw new \Exception(
                "Value of '{$property}' is not correct!\n" .
                "Expected to be: " . json_encode($data[$property]) . "\n" .
                "Received: " . $propVal . "\n"
            );
        }
    }

    /**
     * @Given /^the response matched to the pattern$/
     */
    public function responseByWildcard(PyStringNode $propVal): void
    {
        $data = json_decode($this->getResponse()->getBody(true), true);

        if (fnmatch($propVal, json_encode($data))) {
            throw new \Exception(
                "Response is not correct!\n" .
                "Expected to be: " . json_encode($data) . "\n" .
                "Received: " . $propVal . "\n"
            );
        }
    }

    /**
     * @Given I use request payload
     */
    public function iUseRequestPayload(PyStringNode $requestPayload): void
    {
        $this->setRequestPayload($requestPayload);
    }

    /**
     * @Given I use attach files
     */
    public function iAttachFiles(TableNode $filesTable): void
    {

        $files = [];
        foreach ($filesTable->getHash() as $fileHash) {
            $files[] = [
                'name' => $fileHash['name'],
                'path' => __DIR__ . '/../../../../../' . $fileHash['path']
            ];
        }
        $this->setFiles($files);
    }

    /**
     * @When /^I make a GET request to "([^"]*)"$/
     */
    public function makeGetRequest($uri): void
    {
        $jsonPayload = !empty($this->getRequestPayload())
            ? ['json' => json_decode($this->getRequestPayload()->getRaw(), true)]
            : [];

        $response = $this->getClient()->request('GET', $this->getMinkParameter('api_base') . $uri, $jsonPayload);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a DELETE request to "([^"]*)"$/
     */
    public function makeDeleteRequest($uri): void
    {
        $jsonPayload = !empty($this->getRequestPayload())
            ? ['json' => json_decode($this->getRequestPayload()->getRaw(), true)]
            : [];

        $response = $this->getClient()->request('DELETE', $this->getMinkParameter('api_base') . $uri, $jsonPayload);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a POST request to "([^"]*)"$/
     */
    public function makePostRequest($uri): void
    {
        $jsonPayload = !empty($this->getRequestPayload())
            ? ['json' => json_decode($this->getRequestPayload()->getRaw(), true)]
            : [];

        $response = $this->getClient()->post($this->getMinkParameter('api_base') . $uri, $jsonPayload);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a PUT request to "([^"]*)"$/
     */
    public function makePutRequest($uri): void
    {
        $jsonPayload = !empty($this->getRequestPayload())
            ? ['json' => json_decode($this->getRequestPayload()->getRaw(), true)]
            : [];

        $response = $this->getClient()->put($this->getMinkParameter('api_base') . $uri, $jsonPayload);
        $this->setResponse($response);
    }

    /**
     * @When /^I make a MULTIPART request to "([^"]*)"$/
     * @throws \Exception
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function makeMultipartRequest($uri): void
    {
        $files = $this->getFiles();
        $this->validateFiles($files);
        $payload = $this->getRequestPayload();
        $multipart = $this->buildMultipartArray($files, $payload);
        $response = $this->getClient()->request('POST', $this->getMinkParameter('api_base') . $uri, [
            'multipart' => $multipart
        ]);

        $this->setResponse($response);
    }

    /**
     * @Then /^the response code should be (\d+)$/
     * @throws \Exception
     */
    public function theResponseCodeShouldBe($code): void
    {
        if ((string) $this->getResponse()->getStatusCode() !== $code) {
            throw new \Exception('HTTP response code does not match '.$code.
                ' (returned: '.$this->getResponse()->getStatusCode().')');
        }
    }

    /**
     * @throws \Exception
     */
    private function authenticateToApi(): void
    {
        $client = $this->getClient();
        $response = $client->post($this->getMinkParameter('api_base') . '/api/index.php?action=authenticate', [
            'form_params' => [
                'username' => 'admin',
                'password' => 'Centreon!2021'
            ]
        ]);
        $responseObj = json_decode($response->getBody());
        if (empty($responseObj->authToken)) {
            throw new \Exception('Could not get authentication token from API.');
        }
        $this->setAuthToken($responseObj->authToken);
    }

    /**
     * Validate files existing
     * @throws \Exception
     */
    private function validateFiles(array $files): void
    {
        if (!empty($files)) {
            foreach ($files as $file) {
                if (!file_exists($file['path']) || !fopen($file['path'], 'r')) {
                    throw new \Exception('One or more files not found or unreadable to attach to request.');
                }
            }
        }
    }

    /**
     * build multipart request
     * @return array
     */
    private function buildMultipartArray(array $files, $payload = null): array
    {
        $output = [];
        foreach ($files as $file) {
            $output[] = [
                'name' => $file['name'],
                'contents' => fopen($file['path'], 'r'),
            ];
        }

        if (!empty($payload)) {
            foreach (json_decode($payload->getRaw(), true) as $fk => $fv) {
                $output[] = [
                    'name' => $fk,
                    'contents' => $fv
                ];
            }
        }

        return $output;
    }
}
