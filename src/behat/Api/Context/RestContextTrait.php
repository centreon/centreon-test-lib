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
use Symfony\Component\HttpClient\Response\CurlResponse;

Trait RestContextTrait
{
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
     * @return CurlResponse
     */
    abstract protected function getHttpResponse();

    /**
     * @param CurlResponse $httpResponse
     * @return void
     */
    abstract protected function setHttpResponse(CurlResponse $httpResponse);

    /**
     * @return string
     */
    abstract protected function getBaseUri();

    /**
     * Sends a HTTP request
     * @return CurlResponse
     *
     * @Given I send a :method request to :url
     */
    public function iSendARequestTo($method, $url, $body = null)
    {
        if ($body !== null) {
            if (is_string($body)) {
                $body = new PyStringNode([$body], 1);
            } elseif (is_array($body)) {
                $body = new PyStringNode($body, 1);
            } elseif (!($body instanceof PyStringNode)) {
                throw new \Exception('body format not supported');
            }
        }

        $this->setHttpResponse(
            $this->getHttpClient()->request(
                $method,
                $this->locatePath($url),
                [
                    'headers' => $this->getHttpHeaders(),
                    'body' => $body !== null ? $body->getRaw() : null
                ]
            )
        );

        return $this->getHttpResponse();
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
        $actual   = $this->getHttpResponse()->getContent();
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
        $actual = $this->getHttpResponse()->getContent();
        $message = "The response of the current page is not empty, it is: $actual";
        Assert::true(null === $actual || "" === $actual, $message);
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
        $actual = $this->getHttpResponse()->getHeaders()['name'];

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
        $actual = $this->getHttpResponse()->getHeaders()['name'];

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
        $actual = $this->getHttpResponse()->getHeaders()['name'];

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
        Assert::notContains(
            $value,
            $this->getHttpResponse()->getHeaders()[$name],
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

        return isset($this->$this->getHttpResponse()->getHeaders()[$name]);
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
        $actual = $this->getHttpResponse()->getHeaders()['name'];

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
        $date = new \DateTime($this->getHttpResponse()->getHeaders()['Date'][0]);
        $expires = new \DateTime($this->getHttpResponse()->getHeaders()['Expires'][0]);

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
        $content = $this->getHttpResponse()->getContent();
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
