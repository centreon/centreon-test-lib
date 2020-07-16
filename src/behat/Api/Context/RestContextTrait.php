<?php

/*
 * Copyright 2005 - 2020 Centreon (https://www.centreon.com/)
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
 *
 * For more information : contact@centreon.com
 *
 */

namespace Centreon\Test\Behat\Api\Context;

use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;
use Symfony\Component\HttpClient\CurlHttpClient;
use Symfony\Component\HttpClient\Psr18Client;
use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;
use League\OpenAPIValidation\PSR7\ValidatorBuilder;
use \Nyholm\Psr7\Stream;
use \Nyholm\Psr7\Uri;

Trait RestContextTrait
{
    /**
     * @var ValidatorBuilder
     */
    private $apiValidator;

    /**
     * @return CurlHttpClient
     */
    abstract protected function getHttpClient();

    /**
     * @param CurlHttpClient $httpClient
     * @return void
     */
    abstract protected function setHttpClient(CurlHttpClient $httpClient);

    /**
     * @return array
     */
    abstract protected function getHttpHeaders();

    /**
     * @param array $httpHeaders
     * @return void
     */
    abstract protected function setHttpHeaders(array $httpHeaders);

    /**
     * @param array $httpHeader
     * @return void
     */
    abstract protected function addHttpHeader(string $name, string $value);

    /**
     * @return ResponseInterface
     */
    abstract protected function getHttpResponse();

    /**
     * @param ResponseInterface $httpResponse
     * @return void
     */
    abstract protected function setHttpResponse(ResponseInterface $httpResponse);

    /**
     * @return string
     */
    abstract protected function getBaseUri();

    /**
     * Parse URI path
     *
     * @param string $path
     * @return string
     */
    public function locatePath($path)
    {
        return 0 !== strpos($path, 'http')
            ? rtrim($this->getBaseUri(), '/') . '/' . ltrim($path, '/')
            : $path;
    }

    /**
     * Initialize validator according to centreon web api documentation
     *
     * @Given the endpoints are described in Centreon Web API documentation
     */
    public function theCentreonApiDocumentation()
    {
        $this->apiValidator = (new ValidatorBuilder())
            ->fromYamlFile(__DIR__ . '/../../../../../../../doc/API/centreon-api-v2.yaml');
    }

    /**
     * Sends a HTTP request
     * @return ResponseInterface
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, $body = null)
    {
        if ($body !== null) {
            if ($body instanceof PyStringNode) {
                $body = $body->getRaw();
            } elseif (is_array($body)) {
                $body = implode('', $body);
            }
        }

        $client = new Psr18Client($this->getHttpClient());

        if (preg_match('#^(?:' . $this->getBaseUri() . ')?(/[\w\d\.]+)(/.+)$#', $url, $matches)) {
            $validate = true;
            $url = ApiContext::ROOT_PATH . $matches[1] . $matches[2];
            $uri = new Uri($this->getBaseUri() . $matches[1] . $matches[2]);
        } else {
            $validate = false;
            $url = $this->locatePath($url);
            $uri = new Uri($url);
        }

        $request = $client->createRequest($method, $url);
        foreach ($this->getHttpHeaders() as $header => $value) {
            $request = $request->withHeader($header, $value);
        }
        if ($body !== null) {
            $request = $request->withBody(Stream::create($body));
        }

        $request = $request->withUri($uri);

        $response = $client->sendRequest($request);

        if ($validate === true) {
            $this->validateRequestAndResponse($request, $response);
        }

        $this->setHttpResponse($response);

        return $this->getHttpResponse();
    }

    public function validateRequestAndResponse(RequestInterface $request, ResponseInterface $response): void
    {
        if (isset($this->apiValidator)) {
            $requestValidator = $this->apiValidator->getRequestValidator();
            $responseValidator = $this->apiValidator->getResponseValidator();

            $operation = $requestValidator->validate($request);
            $responseValidator->validate($operation, $response);
        }
    }

    /**
     * Sends a HTTP request with a some parameters
     *
     * @Given I send a :method request to :url with parameters:
     */
    public function iSendARequestToWithParameters($method, $url, TableNode $data)
    {
        $parameters = [];

        foreach ($data->getHash() as $row) {
            if (!isset($row['key']) || !isset($row['value'])) {
                throw new \Exception("You must provide a 'key' and 'value' column in your table node.");
            }

            $parameters[$row['key']] = $row['value'];
        }

        return $this->getHttpClient()->request(
            $method,
            $this->locatePath($url),
            $parameters
        );
    }

    /**
     * Sends a HTTP request with a body
     *
     * @Given I send a :method request to :url with body:
     */
    public function iSendARequestToWithBody($method, $url, $body)
    {
        return $this->iSendARequestTo($method, $url, $body);
    }

    /**
     * Checks, whether the response content is equal to given text
     *
     * @Then the response should be equal to
     * @Then the response should be equal to:
     */
    public function theResponseShouldBeEqualTo(PyStringNode $expected)
    {
        $expected = str_replace('\\"', '"', $expected);
        $actual   = $this->getHttpResponse()->getBody()->__toString();
        $message = "Actual response is '$actual', but expected '$expected'";
        Assert::eq($expected, $actual, $message);
    }

    /**
     * Checks, whether the response content is null or empty string
     *
     * @Then the response should be empty
     */
    public function theResponseShouldBeEmpty()
    {
        $actual = $this->getHttpResponse()->getBody()->__toString();
        $message = "The response of the current page is not empty, it is: $actual";
        Assert::isEmpty($actual, $message);
    }

    /**
     * Checks, whether the header name is equal to given text
     *
     * @Then the header :name should be equal to :value
     */
    public function theHeaderShouldBeEqualTo(string $name, string $value)
    {
        $this->theHeaderShouldExist($name);

        /**
         * @var string
         */
        $actual = $this->getHttpResponse()->getHeader($name);

        Assert::eq(
            strtolower($value),
            strtolower($actual),
            "The header '$name' should be equal to '$value', but it is: '$actual'"
        );
    }

    /**
    * Checks, whether the header name is not equal to given text
    *
    * @Then the header :name should not be equal to :value
    */
    public function theHeaderShouldNotBeEqualTo($name, $value) {
        $this->theHeaderShouldExist($name);

        /**
         * @var string
         */
        $actual = $this->getHttpResponse()->getHeader($name);

        Assert::notEq(
            strtolower($value),
            strtolower($actual),
            "The header '$name' is equal to '$actual'"
        );
    }

    /**
     * Checks, whether the header name contains the given text
     *
     * @Then the header :name should contain :value
     */
    public function theHeaderShouldContain($name, $value)
    {
        $this->theHeaderShouldExist($name);

        /**
         * @var string
         */
        $actual = $this->getHttpResponse()->getHeader($name);

        Assert::contains(
            $value,
            $actual,
            "The header '$name' should contain value '$value', but actual value is '$actual'"
        );
    }

    /**
     * Checks, whether the header name doesn't contain the given text
     *
     * @Then the header :name should not contain :value
     */
    public function theHeaderShouldNotContain(string $name, string $value)
    {
        $this->theHeaderShouldExist($name);

        /**
         * @var string
         */
        $actual = $this->getHttpResponse()->getHeader($name);

        Assert::notContains(
            $value,
            $actual,
            "The header '$name' contains '$value'"
        );
    }

    /**
     * Checks, whether the header not exists
     *
     * @Then the header :name should not exist
     */
    public function theHeaderShouldNotExist(string $name)
    {
        $headers = $this->getHttpResponse()->getHeaders();
        Assert::keyNotExists(
            $headers,
            $name,
            "Header '$name' exists."
        );
    }

    /**
     * Checks, whether the header exists
     *
     * @Then the header :name should exist
     */
    public function theHeaderShouldExist(string $name)
    {
        $headers = $this->getHttpResponse()->getHeaders();

        Assert::keyExists(
            $headers,
            $name,
            "Header '$name' does not exist."
        );
    }

    /**
     * @Then the header :name should match :regex
     */
    public function theHeaderShouldMatch(string $name, string $regex)
    {
        $this->theHeaderShouldExist($name);

        /**
         * @var string
         */
        $actual = $this->getHttpResponse()->getHeader($name);

        Assert::eq(
            1,
            preg_match($regex, $actual),
            "The header '$name' should match '$regex', but it is: '$actual'"
        );
    }

    /**
     * @Then the header :name should not match :regex
     */
    public function theHeaderShouldNotMatch($name, $regex)
    {
        Assert::false(
            $this->theHeaderShouldMatch($name, $regex),
            "The header '$name' should not match '$regex'"
        );
    }

   /**
     * Checks, that the response header expire is in the future
     *
     * @Then the response should expire in the future
     */
    public function theResponseShouldExpireInTheFuture()
    {
        $date = new \DateTime($this->getHttpResponse()->getHeader('Date')[0]);
        $expires = new \DateTime($this->getHttpResponse()->getHeader('Expires')[0]);

        Assert::same(
            1,
            $expires->diff($date)->invert,
            sprintf('The response doesn\'t expire in the future (%s)', $expires->format(DATE_ATOM))
        );
    }

    /**
     * Add an header element in a request
     *
     * @Then I add :name header equal to :value
     */
    public function iAddHeaderEqualTo($name, $value)
    {
        $this->addHttpHeader($name, $value);
    }

    /**
     * @Then the response should be encoded in :encoding
     */
    public function theResponseShouldBeEncodedIn($encoding)
    {
        $content = $this->getHttpResponse()->getBody()->__toString();
        if (!mb_check_encoding($content, $encoding)) {
            throw new \Exception("The response is not encoded in $encoding");
        }

        $this->theHeaderShouldContain('Content-Type', "charset=$encoding");
    }

    /**
     * @Then the response code should be :code
     */
    public function theResponseCodeShouldBe(int $expectedCode)
    {
        $actualCode = $this->getHttpResponse()->getStatusCode();
        $message = "Actual response is '$actualCode', but expected '$expectedCode'";
        Assert::eq($expectedCode, $actualCode, $message);
    }
}
