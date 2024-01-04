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
use Behat\Behat\Hook\Scope\AfterStepScope;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpClient\HttpClient;
use Centreon\Test\Behat\Container;
use Centreon\Test\Behat\SpinTrait;

/**
 * This context class contains the main definitions of the steps used by contexts to validate API
 */
class ApiContext implements Context
{
    use SpinTrait, JsonContextTrait, RestContextTrait, CentreonClapiContextTrait, FileSystemContextTrait;

    public const ROOT_PATH = '/centreon';

    /**
     * @var Container
     */
    public $container;

    /**
     * @var string the service name of web container in docker compose file
     */
    protected $webService = 'web';

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
     * @var string
     */
    protected $phpSessionId;

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

        if (isset($this->phpSessionId)) {
            $httpHeaders['Cookie'] = 'PHPSESSID=' . $this->phpSessionId;
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
     * @return string
     */
    protected function getPhpSessionId()
    {
        return $this->phpSessionId;
    }

    /**
     * @param string $phpSessionId
     * @return void
     */
    protected function setPhpSessionId(string $phpSessionId)
    {
        $this->phpSessionId = $phpSessionId;
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
     * Replace custom variables
     *
     * @param string $value
     * @return string
     */
    protected function replaceCustomVariables(string $value): string
    {
        if (preg_match_all('/<([\w\d]+)>/', $value, $matches)) {
            foreach ($matches[1] as $match) {
                $value = str_replace('<' . $match . '>', $this->getCustomVariable($match), $value);
            }
        }

        return $value;
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
     * after step hook
     *
     * @AfterStep
     *
     * @param AfterStepScope $scope
     */
    public function afterStep(AfterStepScope $scope): void
    {
        if (isset($this->container)) {
            $containerLogs = $this->container->getLogs();
            if (preg_match_all('/(php (?:warning|fatal|notice|deprecated).+$)/mi', $containerLogs, $matches)) {
                throw new \Exception('PHP log issues: ' . implode(', ', $matches[0]));
            }
        }
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

            $logTitle = "\n\n"
                . "################\n"
                . "# Web App logs #\n"
                . "################\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon/centreon-web.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Engine logs.
            $logTitle = "\n\n"
                . "###############\n"
                . "# Engine logs #\n"
                . "###############\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon-engine/centengine.log 2>/dev/null',
                $this->webService,
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
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Broker logs.
            $logTitle = "\n\n"
                . "#################\n"
                . "# Gorgone logs #\n"
                . "#################\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon-gorgone/gorgoned.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon SQL errors.
            $logTitle = "\n\n"
                . "#######################\n"
                . "# Centreon sql errors #\n"
                . "#######################\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon/sql-error.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL errors.
            $logTitle = "\n\n"
                . "################\n"
                . "# Mysql errors #\n"
                . "################\n\n";
            $output = $this->container->execute(
                'bash -c "cat /var/lib/mysql/*.err 2>/dev/null"',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon LDAP logs.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Centreon LDAP logs #\n"
                . "######################\n\n";
            $output = $this->container->execute(
                'cat /var/log/centreon/ldap.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL process list.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Mysql process list #\n"
                . "######################\n\n";
            $output = $this->container->execute(
                'mysql -e "SHOW FULL PROCESSLIST" 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL slow queries.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Mysql slow queries #\n"
                . "######################\n\n";
            $output = $this->container->execute(
                'cat /var/lib/mysql/slow_queries.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL queries.
            $logTitle = "\n\n"
                . "#################\n"
                . "# Mysql queries #\n"
                . "#################\n\n";
            $output = $this->container->execute(
                'cat /var/lib/mysql/queries.log 2>/dev/null',
                $this->webService,
                false
            );
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);
        }

        // Destroy container.
        unset($this->container);
    }

    /**
     * Launch Centreon Web container and setup context.
     *
     * @param string $composeBehatProperty Bind property to docker-compose.yml path
     * @param string[] $profiles docker-compose profiles to activate
     * @param array<string,string|int|boolean> $envVars docker composer environment variables
     * @throws \Exception
     */
    public function launchCentreonWebContainer(
        string $composeBehatProperty,
        array $profiles,
        array $envVars = []
    ): void {
        foreach ($profiles as $profile) {
            if (preg_match('/^web(?!driver)/', $profile)) {
                $this->webService = $profile;
            }
        }

        if (!isset($this->composeFiles[$composeBehatProperty])) {
            throw new \Exception('Property "' . $composeBehatProperty . '" does not exist in behat.yml');
        }

        $this->container = new Container($this->composeFiles[$composeBehatProperty], $profiles, $envVars);

        $this->setBaseUri(
            'http://' . $this->container->getHost() . ':'
            . $this->container->getPort(80, $this->webService) . self::ROOT_PATH
        );

        $this->spin(
            function() {
                $requestUri = $this->getBaseUri() . '/api/latest/';
                $response = $this->iSendARequestTo('GET', $requestUri);
                if ($response->getStatusCode() === 404) {
                    // it means symfony router is up and do not handle this route
                    return true;
                } else {
                    throw new \Exception(
                        'Centreon web container seems not started. '
                        . 'Cannot request "' . $requestUri . '" '
                        . '(http code : ' . $response->getStatusCode() . ', '
                        . 'content: "' . $response->getBody()->__toString() . '")'
                    );
                }
            },
            'timeout',
            15
        );
    }

    /**
     * launch Centreon Web container
     *
     * @Given a running instance of Centreon Web API
     */
    public function aRunningInstanceOfCentreonApi()
    {
        $this->launchCentreonWebContainer('docker_compose_web');
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
            $this->getBaseUri() . '/api/latest/login',
            json_encode([
                'security' => [
                    'credentials' => [
                        'login' => 'admin',
                        'password' => 'Centreon!2021',
                    ],
                ],
            ])
        );

        $response = json_decode($response->getBody()->__toString(), true);
        $this->setToken($response['security']['token']);
    }

    /**
     * Internal login
     *
     * @Given I am logged in with local provider
     */
    public function iAmLoggedInWithLocalProvider()
    {
        $this->setHttpHeaders(['Content-Type' => 'application/json']);
        $this->iSendARequestToWithBody(
            'POST',
            $this->getBaseUri() . '/authentication/providers/configurations/local',
            json_encode([
                'login' => 'admin',
                'password' => 'Centreon!2021',
            ])
        );

        if (preg_match('/PHPSESSID=(\S+)/', $this->getHttpResponse()->getHeader('set-cookie')[0], $matches)) {
            $this->setPhpSessionId($matches[1]);
        } else {
            throw new \Exception('Php session id not found in cookies');
        }
    }

    /**
     * Wait x seconds
     * @param int $seconds
     *
     * @Given /^I wait (\d+) seconds$/
     */
    public function iWaitXSeconds(int $seconds = 5): void
    {
        sleep($seconds);
    }

    /**
     * Wait host to be monitored
     *
     * @Given /^I wait until host "(\S+)" is monitored(?: \(tries: (\d+)\))?$/
     */
    public function iWaitUntilHostIsMonitored(string $host, int $tries = 15)
    {
        $hostId = null;
        $this->spin(
            function() use ($host, &$hostId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/api/latest/monitoring/hosts?search={"host.name":"' . $host . '"}'
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
     * @Given /^I wait until service "(\S+)" from host "(\S+)" is monitored(?: \(tries: (\d+)\))?$/
     */
    public function iWaitUntilServiceIsMonitored(string $service, string $host, int $tries = 15)
    {
        $hostId = null;
        $serviceId = null;

        $this->spin(
            function() use ($host, $service, &$hostId, &$serviceId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/api/latest/monitoring/services?search='
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
     * @Given /^I wait until hostgroup "(\S+)" is monitored(?: \(tries: (\d+)\))?$/
     */
    public function iWaitUntilHostGroupIsMonitored(string $hostgroup, int $tries = 15): ?int
    {
        $hostgroupId = null;
        $this->spin(
            function() use ($hostgroup, &$hostgroupId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/api/latest/monitoring/hostgroups?search={"name":"' . $hostgroup . '"}'
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
     * @Given /^I wait to get (\d+) results? from ['"](\S+)['"](?: \(tries: (\d+)\))?$/
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

    /**
     *
     * @Given /^I want to generate the monitoring server configuration #(\d+)$/
     */
    public function iWantToGenerateTheMonitoringServerConfiguration(int $monitoringServerId)
    {
        $this->spin(
            function() use ($monitoringServerId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/api/latest/configuration/monitoring-servers/' . $monitoringServerId . '/generate'
                );
                return true;
            },
            'The monitoring server configuration of #' . $monitoringServerId . ' is not generated',
            1
        );
    }

    /**
     *
     * @Given /^I want to reload the monitoring server configuration #(\d+)$/
     */
    public function iWantToReloadTheMonitoringServerConfiguration(int $monitoringServerId)
    {
        $this->spin(
            function() use ($monitoringServerId) {
                $response = $this->iSendARequestTo(
                    'GET',
                    '/api/latest/configuration/monitoring-servers/' . $monitoringServerId . '/reload'
                );
                return true;
            },
            'The monitoring server configuration of #' . $monitoringServerId . ' is not reloaded',
            1
        );
    }

    /**
     * @Given I am logged in with :username\/:password
     */
    public function iAmLoggedInWith(string $username, string $password)
    {
        $this->setHttpHeaders(['Content-Type' => 'application/json']);
        $response = $this->iSendARequestToWithBody(
            'POST',
            $this->getBaseUri() . '/api/latest/login',
            json_encode([
                'security' => [
                    'credentials' => [
                        'login' => $username,
                        'password' => $password,
                    ],
                ],
            ])
        );

        $response = json_decode($response->getBody()->__toString(), true);
        $this->setToken($response['security']['token']);
    }
}
