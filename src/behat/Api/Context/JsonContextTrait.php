<?php

/*
 * Copyright 2005 - 2023 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
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

use Psr\Http\Message\ResponseInterface;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Gherkin\Node\TableNode;
use Webmozart\Assert\Assert;
use Centreon\Test\Behat\Api\Json\Json;
use Centreon\Test\Behat\Api\Json\JsonSchema;
use Centreon\Test\Behat\Api\Json\JsonInspector;

Trait JsonContextTrait
{
    /**
     * @var JsonInspector
     */
    protected $inspector;

    private int $evaluateJsonPathExceptionCode = 1000;

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
     * @param string $name
     * @param mixed $value
     * @return void
     */
    abstract protected function addCustomVariable(string $name, $value);

    /**
     * Replace custom variables
     *
     * @param string $value
     * @return string
     */
    abstract protected function replaceCustomVariables(string $value): string;

    /**
     * @return JsonInspector
     */
    private function getInspector()
    {
        $this->inspector = new JsonInspector();

        return $this->inspector;
    }

    /**
     * Checks, that the response is correct JSON
     *
     * @Then the response should be in JSON
     */
    public function theResponseShouldBeInJson(): void
    {
        $this->getJson();
    }

    /**
     * Checks, that the response is not correct JSON
     *
     * @Then the response should not be in JSON
     */
    public function theResponseShouldNotBeInJson(): void
    {
        Assert::throws(
            function () {
                return $this->theResponseShouldBeInJson();
            },
            'Exception',
            'The response is in JSON'
        );
    }

    /**
     * Checks, that given JSON node is equal to given value
     *
     * @Then the JSON node :node should be equal to :text
     */
    public function theJsonNodeShouldBeEqualTo($node, $text): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::eq(
            $text,
            $actual,
            sprintf("The node value is '%s'", json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON nodes are equal to givens values
     *
     * @Then the JSON nodes should be equal to:
     */
    public function theJsonNodesShouldBeEqualTo(TableNode $nodes): void
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldBeEqualTo($node, $text);
        }
    }

    /**
     * Checks, that given JSON node matches given pattern
     *
     * @Then the JSON node :node should match :pattern
     */
    public function theJsonNodeShouldMatch($node, $pattern): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::regex(
            $actual,
            $pattern,
            sprintf("The node value is '%s'", json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node is null
     *
     * @Then the JSON node :node should be null
     */
    public function theJsonNodeShouldBeNull($node): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::notNull(
            $actual,
            sprintf('The node value is `%s`', json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node is not null.
     *
     * @Then the JSON node :node should not be null
     */
    public function theJsonNodeShouldNotBeNull($node): void
    {
        Assert::false(
            $this->theJsonNodeShouldBeNull($node),
            sprintf('The node %s should not be null', $node)
        );
    }

    /**
     * Checks, that given JSON node is true
     *
     * @Then the JSON node :node should be true
     */
    public function theJsonNodeShouldBeTrue($node): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::true(
            $actual,
            sprintf('The node value is `%s`', json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node is false
     *
     * @Then the JSON node :node should be false
     */
    public function theJsonNodeShouldBeFalse($node): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::false(
            $actual,
            sprintf('The node value is `%s`', json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node is equal to the given string
     *
     * @Then the JSON node :node should be equal to the string :text
     */
    public function theJsonNodeShouldBeEqualToTheString($node, $text): void
    {
        $json = $this->getJson();

        $actual = trim($this->evaluateJsonPath($json, $node), '"');

        Assert::same(
            $actual,
            $text,
            sprintf('The node value is `%s`', json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node is equal to the given number
     *
     * @Then the JSON node :node should be equal to the number :number
     */
    public function theJsonNodeShouldBeEqualToTheNumber($node, $number): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::same(
            $actual,
            $number,
            sprintf('The node value is `%s`', json_encode($actual))
        );
    }

    /**
     * Checks, that given JSON node has N element(s)
     *
     * @Then the JSON node :node should have :count element(s)
     */
    public function theJsonNodeShouldHaveElements($node, int $count): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::count(json_decode($actual, true), $count);
    }

    /**
     * Checks, that given JSON node has at least N element(s)
     *
     * @Then the JSON node :node should have at least :count element(s)
     */
    public function theJsonNodeShouldHaveAtLeastElements($node, int $count): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::greaterThanEq(count(json_decode($actual, true)), $count);
    }

    /**
     * Checks, that given JSON node contains given value
     *
     * @Then the JSON node :node should contain :text
     */
    public function theJsonNodeShouldContain($node, $text): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::contains((string) $actual, $text);
    }

    /**
     * Checks, that given JSON nodes contains values
     *
     * @Then the JSON nodes should contain:
     */
    public function theJsonNodesShouldContain(TableNode $nodes): void
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldContain($node, $text);
        }
    }

    /**
     * Checks, that given JSON node does not contain given value
     *
     * @Then the JSON node :node should not contain :text
     */
    public function theJsonNodeShouldNotContain($node, $text): void
    {
        $json = $this->getJson();

        $actual = $this->evaluateJsonPath($json, $node);

        Assert::notContains((string) $actual, $text);
    }

    /**
     * Checks, that given JSON nodes does not contain given value
     *
     * @Then the JSON nodes should not contain:
     */
    public function theJsonNodesShouldNotContain(TableNode $nodes): void
    {
        foreach ($nodes->getRowsHash() as $node => $text) {
            $this->theJsonNodeShouldNotContain($node, $text);
        }
    }

    /**
     * Checks, that given JSON node exist
     *
     * @Then the JSON node :name should exist
     */
    public function theJsonNodeShouldExist($name)
    {
        $json = $this->getJson();

        $node = $this->evaluateJsonPath($json, $name);

        return $node;
    }

    /**
     * Checks, that given JSON node does not exist
     *
     * @Then the JSON node :name should not exist
     */
    public function theJsonNodeShouldNotExist($name): void
    {
        try {
            Assert::false(
                $this->theJsonNodeShouldExist($name),
                "The node '{$name}' exists."
            );
        } catch (\Exception $ex) {
            if ($this->evaluateJsonPathExceptionCode === $ex->getCode()) {
                return;
            }

            throw $ex;
        }
    }

    /**
     * @Then the JSON should be valid according to this schema:
     */
    public function theJsonShouldBeValidAccordingToThisSchema(PyStringNode $schema): void
    {
        $this->getInspector()->validate(
            $this->getJson(),
            new JsonSchema($schema)
        );
    }

    /**
     * @Then the JSON should be invalid according to this schema:
     */
    public function theJsonShouldBeInvalidAccordingToThisSchema(PyStringNode $schema): void
    {
        Assert::false(
            $this->theJsonShouldBeValidAccordingToThisSchema($schema),
            'Expected to receive invalid json, got valid one'
        );
    }

    /**
     * @Then the JSON should be valid according to the schema :filename
     */
    public function theJsonShouldBeValidAccordingToTheSchema($filename): void
    {
        $this->checkSchemaFile($filename);

        $this->getInspector()->validate(
            $this->getJson(),
            new JsonSchema(
                file_get_contents($filename),
                'file://' . str_replace(DIRECTORY_SEPARATOR, '/', realpath($filename))
            )
        );
    }

    /**
     * @Then the JSON should be invalid according to the schema :filename
     */
    public function theJsonShouldBeInvalidAccordingToTheSchema($filename): void
    {
        $this->checkSchemaFile($filename);

        Assert::false(
            $this->theJsonShouldBeValidAccordingToTheSchema($filename),
            'The schema was valid'
        );
    }

    /**
     * @Then the JSON should be equal to:
     */
    public function theJsonShouldBeEqualTo(PyStringNode $content): void
    {
        $actual = $this->getJson();

        try {
            $expected = new Json($this->replaceCustomVariables($content));
        } catch (\Exception $e) {
            throw new \Exception('The expected JSON is not a valid');
        }

        Assert::same(
            (string) $expected,
            (string) $actual,
            "The json is equal to:\n" . str_replace('%', '%%', $actual->encode())
        );
    }

    /**
     * @Then print last JSON response
     */
    public function printLastJsonResponse(): void
    {
        echo $this->getJson()
            ->encode();
    }

    /**
     * Checks, that response JSON matches with a swagger dump
     *
     * @Then the JSON should be valid according to swagger :dumpPath dump schema :schemaName
     */
    public function theJsonShouldBeValidAccordingToTheSwaggerSchema($dumpPath, $schemaName): void
    {
        $this->checkSchemaFile($dumpPath);

        $dumpJson = file_get_contents($dumpPath);
        $schemas = json_decode($dumpJson, true);
        $definition = json_encode(
            $schemas['definitions'][$schemaName]
        );
        $this->getInspector()->validate(
            $this->getJson(),
            new JsonSchema(
                $definition
            )
        );
    }
    /**
     *
     * Checks, that response JSON not matches with a swagger dump
     *
     * @Then the JSON should not be valid according to swagger :dumpPath dump schema :schemaName
     */
    public function theJsonShouldNotBeValidAccordingToTheSwaggerSchema($dumpPath, $schemaName): void
    {
        Assert::false(
            $this->theJsonShouldBeValidAccordingToTheSwaggerSchema($dumpPath, $schemaName),
            'JSON Schema matches but it should not'
        );
    }

    protected function getJson()
    {
        return new Json($this->getHttpResponse()->getBody()->__toString());
    }

    private function checkSchemaFile($filename): void
    {
        if (false === is_file($filename)) {
            throw new \RuntimeException(
                'The JSON schema doesn\'t exist'
            );
        }
    }

    /**
     * Validate response following json format file
     *
     * @Given the response use ":type" standard JSON format
     */
    public function theResponseUseStandardJsonFormat(string $type): void
    {
        $this->theResponseShouldBeFormattedLikeJsonFormat('standard/' . $type . '.json');
    }

    /**
     * Validate response following standard json format file
     *
     * @Given the response should be formatted like JSON format ":path"
     */
    public function theResponseShouldBeFormattedLikeJsonFormat(string $path): void
    {
        $possiblePaths = [
            getcwd() . '/tests/api/fixtures/validation/' . $path,
            __DIR__ . '/../fixtures/validation/' . $path,
        ];

        $fullPath = null;
        foreach ($possiblePaths as $possiblePath) {
            if (file_exists($possiblePath)) {
                $fullPath = $possiblePath;
            }
        }

        if ($fullPath === null) {
            throw new \Exception(
                'cannot find validation file "' . $path . '" in ' . implode(' | ', $possiblePaths)
            );
        }

        $this->theResponseShouldBeInJson();

        $content = json_decode($this->getHttpResponse()->getBody()->__toString());
        $validator = new \JsonSchema\Validator();
        $validator->validate(
            $content,
            ['$ref' => 'file://' . $fullPath],
            \JsonSchema\Constraints\Constraint::CHECK_MODE_EXCEPTIONS
        );
    }

    /**
     * Store custom variables
     *
     * @Given I store response values in:
     */
    public function iStoreResponseValuesIn(TableNode $table): void
    {
        $json = $this->getJson();

        foreach ($table as $row) {
            $value = $this->evaluateJsonPath($json, $row['path']);

            $this->addCustomVariable($row['name'], $value);
        }
    }

    /**
     * Evaluate json
     *
     * @param Json $json
     * @param string $expression
     * @return string
     * @throws \Exception
     */
    private function evaluateJsonPath(Json $json, $expression)
    {
        try {
            $actual = $this->getInspector()->evaluate($json, $expression);
        } catch (\Exception $e) {
            throw new \Exception(
                $e->getMessage() . "\n"
                . "Content is :\n"
                . $this->getHttpResponse()->getBody()->__toString(),
                $this->evaluateJsonPathExceptionCode,
                $e
            );
        }

        return $actual;
    }
}
