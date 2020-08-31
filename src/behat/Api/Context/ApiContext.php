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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttpClient;
use Centreon\Test\Behat\Container;

/**
 * This context class contains the main definitions of the steps used by contexts to validate API
 */
class ApiContext implements Context
{
    use JsonContextTrait, RestContextTrait, CentreonClapiContextTrait, FileSystemContextTrait;

    public const ROOT_PATH = '/centreon/api';

    /**
     * @var Container
     */
    public $container;

    /**
     * @var array List of container Compose files.
     */
    protected $composeFiles;

    /**
     * @var HttpClientInterface
     */
    protected $httpClient;

    /**
     * @var array
     */
    protected $httpHeaders = [];

    /**
     * @var string
     */
    protected $baseUri;

    /**
     * @var string
     */
    protected $token;

    /**
     * @var ResponseInterface
     */
    protected $httpResponse;

    public function __construct()
    {
        $this->setHttpClient(HttpClient::create());
        $this->setHttpHeaders(['Content-Type' => 'application/json']);
    }

    /**
     * @return Container
     */
    protected function getContainer()
    {
        return $this->container;
    }

    /**
     * @param Container $container
     * @return void
     */
    protected function setContainer(Container $container)
    {
        $this->container = $container;
    }

    /**
     * @return HttpClientInterface
     */
    protected function getHttpClient()
    {
        return $this->httpClient;
    }

    /**
     * @param HttpClientInterface $httpClient
     * @return void
     */
    protected function setHttpClient(HttpClientInterface $httpClient)
    {
        $this->httpClient = $httpClient;
    }

    /**
     * @return array
     */
    protected function getHttpHeaders()
    {
        $httpHeaders = $this->httpHeaders;

        if (isset($this->token)) {
            $httpHeaders['X-AUTH-TOKEN'] = $this->token;
        }

        return $httpHeaders;
    }

    /**
     * @param array $httpHeaders
     * @return void
     */
    protected function setHttpHeaders(array $httpHeaders)
    {
        $this->httpHeaders = $httpHeaders;
    }

    /**
     * @param string $name
     * @param string $value
     * @return void
     */
    protected function addHttpHeader(string $name, string $value)
    {
        $this->httpHeaders[$name] = $value;
    }

    /**
     * @return string
     */
    protected function getBaseUri()
    {
        return $this->baseUri;
    }

    /**
     * @param string $baseUri
     * @return void
     */
    protected function setBaseUri(string $baseUri)
    {
        $this->baseUri = $baseUri;
    }

    /**
     * @return string
     */
    protected function getToken()
    {
        return $this->token;
    }

    /**
     * @param string $token
     * @return void
     */
    protected function setToken(string $token)
    {
        $this->token = $token;
    }

    /**
     * @return ResponseInterface
     */
    protected function getHttpResponse()
    {
        return $this->httpResponse;
    }

    /**
     * @param ResponseInterface $httpResponse
     * @return void
     */
    protected function setHttpResponse(ResponseInterface $httpResponse)
    {
        $this->httpResponse = $httpResponse;
    }

    /**
     * Store custom variables
     *
     * @param string $name
     * @param mixed $value
     * @return void
     */
    protected function addCustomVariable(string $name, $value)
    {
        $this->customVariables[$name] = $value;
    }

    /**
     * Get custom variable by name
     *
     * @param string $name
     * @return mixed
     * @throws \Exception
     */
    protected function getCustomVariable(string $name)
    {
        if (!isset($this->customVariables[$name])) {
            throw new \Exception('Variable "' . $name . '" is not stored');
        }

        return $this->customVariables[$name];
    }

    /**
     *  Set containers Compose files.
     */
    public function setContainersComposeFiles($files)
    {
        $this->composeFiles = $files;
    }

    /**
     *  Get a container Compose file.
     */
    public function getContainerComposeFile($name)
    {
        if (empty($this->composeFiles[$name])) {
            throw new \Exception("Can't get container compose file of " . $name);
        }
        return $this->composeFiles[$name];
    }

    /**
     *  Unset container.
     *
     *  This will effectively stop and remove the container attached to
     *  this context if one was launched.
     *
     * @AfterScenario
     */
    public function unsetContainer(AfterScenarioScope $scope)
    {
        // Failure logs.
        if (isset($this->container) && !$scope->getTestResult()->isPassed()) {
            $scenarioTitle = preg_replace('/(\s|\/)+/', '_', $scope->getScenario()->getTitle());
            $filename = $this->composeFiles['log_directory'] . '/'
                . date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '-' . $scenarioTitle . '.txt';

            // Container logs.
            $logTitle = "\n"
                . "##################\n"
                . "# Container logs #\n"
                . "##################\n\n";
            file_put_contents($filename, $logTitle);
            file_put_contents($filename, $this->container->getLogs(), FILE_APPEND);

            // Centreon Engine logs.
            $logTitle = "\n\n"
                . "###############\n"
                . "# Engine logs #\n"
                . "###############\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon-engine/centengine.log 2>/dev/null',
                'web',
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Broker logs.
            $logTitle = "\n\n"
                . "###############\n"
                . "# Broker logs #\n"
                . "###############\n\n";
            $output = $this->container->execute(
                'bash -c "cat /var/log/centreon-broker/*.log 2>/dev/null"',
                'web',
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Broker logs.
            $logTitle = "\n\n"
                . "#################\n"
                . "# Gorgone logs #\n"
                . "#################\n\n";
            $output = $this->container->execute('cat /var/log/centreon-gorgone/gorgoned.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon SQL errors.
            $logTitle = "\n\n"
                . "#######################\n"
                . "# Centreon sql errors #\n"
                . "#######################\n\n";
            $output = $this->container->execute('cat /var/log/centreon/sql-error.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL errors.
            $logTitle = "\n\n"
                . "################\n"
                . "# Mysql errors #\n"
                . "################\n\n";
            $output = $this->container->execute('bash -c "cat /var/lib/mysql/*.err 2>/dev/null"', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon LDAP logs.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Centreon LDAP logs #\n"
                . "######################\n\n";
            $output = $this->container->execute('cat /var/log/centreon/ldap.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL process list.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Mysql process list #\n"
                . "######################\n\n";
            $output = $this->container->execute('mysql -e "SHOW FULL PROCESSLIST" 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL slow queries.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Mysql slow queries #\n"
                . "######################\n\n";
            $output = $this->container->execute('cat /var/lib/mysql/slow_queries.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL queries.
            $logTitle = "\n\n"
                . "#################\n"
                . "# Mysql queries #\n"
                . "#################\n\n";
            $output = $this->container->execute('cat /var/lib/mysql/queries.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);
        }

        // Destroy container.
        unset($this->container);
    }

    /**
     * launch Centreon container
     *
     * @param string $name name of the service container
     */
    public function launchCentreonWebContainer(string $name = 'web')
    {
        $composeFile = $this->getContainerComposeFile($name);
        if (empty($composeFile)) {
            throw new \Exception(
                'Could not launch containers without Docker Compose file for ' . $name . ': '
                . 'check the configuration of your ContainerExtension in behat.yml.'
            );
        }
        $this->container = new Container($composeFile);
        $this->setBaseUri(
            'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, 'web') . self::ROOT_PATH
        );

        $this->spin(
            function() {
                $response = $this->iSendARequestTo('GET', $this->getBaseUri() . '/latest/');
                if ($response->getStatusCode() === 500) {
                    // it means symfony router is up and do not handle this route
                    return true;
                }
            },
            'timeout',
            15
        );
    }

    /**
     * Waiting an action
     *
     * @param \Closure $closure The function to execute for test the loading.
     * @param string $timeoutMsg The custom message on timeout.
     * @param int $wait The timeout in seconds.
     * @return bool
     * @throws \Exception
     */
    public function spin(\Closure $closure, string $timeoutMsg = 'Load timeout', int $wait = 60)
    {
        $limit = time() + $wait;
        $lastException = null;
        while (time() <= $limit) {
            try {
                if ($closure($this)) {
                    return true;
                }
            } catch (\Exception $e) {
                $lastException = $e;
            }
            sleep(1);
        }
        if (is_null($lastException)) {
            throw new \Exception($timeoutMsg);
        } else {
            throw new \Exception(
                $timeoutMsg . ': ' . $lastException->getMessage() . ' (code ' .
                $lastException->getCode() . ', file ' . $lastException->getFile() .
                ':' . $lastException->getLine() . ')'
            );
        }
    }

    /**
     * launch Centreon Web container
     *
     * @Given a running instance of Centreon Web API
     */
    public function aRunningInstanceOfCentreonApi()
    {
        $this->launchCentreonWebContainer('web');
    }

    /**
     * Log in API
     *
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        $this->setHttpHeaders(['Content-Type' => 'application/json']);
        $response = $this->iSendARequestToWithBody(
            'POST',
            $this->getBaseUri() . '/latest/login',
            json_encode([
                'security' => [
                    'credentials' => [
                        'login' => 'admin',
                        'password' => 'centreon',
                    ],
                ],
            ])
        );

        $response = json_decode($response->getBody()->__toString(), true);
        $this->setToken($response['security']['token']);
    }

    /**
     * Validate response following json format file
     *
     * @Then the response should use :type JSON format
     */
    public function theResponseShouldUseJsonFormat(string $type)
    {
        $this->theResponseCodeShouldBe(200);
        $this->theResponseShouldBeFormattedLikeJsonFormat("monitoring/service/" . $type . ".json");
    }

    /**
     * Wait host to be monitored
     *
     * @Given /^I wait until host "(\S+)" is monitored(?: \(tries: \d+\))?$/
     */
    public function iWaitUntilHostIsMonitored(string $host, int $tries = 15)
    {
        $hostId = null;
        $this->spin(
            function() use ($host, &$hostId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/beta/monitoring/hosts?search={"host.name":"' . $host . '"}'
                );
                $this->theJsonNodeShouldHaveElements('result', 1);
                $response = json_decode($response->getBody()->__toString(), true);
                $hostId = $response["result"][0]['id'];

                return true;
            },
            'the host ' . $host . ' seems not monitored',
            $tries
        );

        return $hostId;
    }

    /**
     * Wait service to be monitored
     *
     * @Given /^I wait until service "(\S+)" from host "(\S+)" is monitored(?: \(tries: \d+\))?$/
     */
    public function iWaitUntilServiceIsMonitored(string $service, string $host, int $tries = 15)
    {
        $hostId = null;
        $serviceId = null;

        $this->spin(
            function() use ($host, $service, &$hostId, &$serviceId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/beta/monitoring/services?search='
                        . '{"host.name":"' . $host . '","service.description":"' . $service . '"}'
                );
                $this->theJsonNodeShouldHaveElements('result', 1);
                $response = json_decode($response->getBody()->__toString(), true);

                $hostId = $response['result'][0]['host']['id'];
                $serviceId = $response['result'][0]['id'];

                return true;
            },
            'the service ' . $host . ' - ' . $service . ' seems not monitored',
            $tries
        );

        return [$hostId, $serviceId];
    }

    /**
     * Wait hostgroup to be monitored
     *
     * @param string $hostgroup the hostgroup name to search
     * @param int $tries Count of tries
     * @return int|null the hostgroup id if found
     *
     * @Given /^I wait until hostgroup "(\S+)" is monitored(?: \(tries: \d+\))?$/
     */
    public function iWaitUntilHostGroupIsMonitored(string $hostgroup, int $tries = 15): ?int
    {
        $hostgroupId = null;
        $this->spin(
            function() use ($hostgroup, &$hostgroupId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/beta/monitoring/hostgroups?search={"name":"' . $hostgroup . '"}'
                );
                $this->theJsonNodeShouldHaveElements('result', 1);
                $response = json_decode($response->getBody()->__toString(), true);
                $hostgroupId = $response["result"][0]['id'];

                return true;
            },
            'the hostgroup ' . $hostgroup . ' seems not monitored',
            $tries
        );

        return $hostgroupId;
    }

    /**
     * Wait to get some results from a listing endpoint
     *
     * @param int $count expected count of results
     * @param string $url the listing endpoint
     * @param int $tries Count of tries
     * @return int the count of results
     *
     * @Given /^I wait to get (\d+) results? from "(\S+)"(?: \(tries: \d+\))?$/
     */
    public function iWaitToGetSomeResultsFrom(int $count, string $url, int $tries = 15): int
    {
        $resultCount = 0;

        $url = $this->replaceCustomVariables($url);

        $this->spin(
            function() use ($count, $url, &$resultCount) {
                $response = $this->iSendARequestTo('GET', $url);
                $response = json_decode($response->getBody()->__toString(), true);
                $resultCount = count($response["result"]);
                $this->theJsonNodeShouldHaveAtLeastElements('result', $count);

                return true;
            },
            'the count of result(s) is : ' . $resultCount,
            $tries
        );

        return $resultCount;
    }

    /**
     * Sleep to be able to connect to the instantiated container while test is running
     *
     * @Then I wait for :time seconds
     * @param int $time
     */
    public function iWaitForNSeconds(int $time)
    {
        sleep($time);
    }
}
