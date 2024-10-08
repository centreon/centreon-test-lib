<?php
/*
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

use Behat\Behat\Hook\Scope\AfterScenarioScope;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Tester\Exception\PendingException;
use Centreon\Test\Behat\ConfigurationPage;
use Centreon\Test\Behat\Administration\LdapConfigurationPage;
use Centreon\Test\Behat\External\LoginPage;
use Centreon\Test\Behat\Configuration\PollerConfigurationExportPage;
use Centreon\Test\Behat\Configuration\HostConfigurationPage;
use Centreon\Test\Behat\Configuration\ServiceConfigurationPage;
use Centreon\Test\Behat\Monitoring\ServiceMonitoringDetailsPage;
use Centreon\Test\Behat\Administration\ParametersCentreonUiPage;
use Behat\Gherkin\Node\TableNode;
use Behat\Gherkin\Node\PyStringNode;
use Behat\Mink\Mink;
use Behat\Mink\Session;
use Behat\Mink\Driver\PantherDriver;
use Exception;
use Exception\SpinStopException;
use PDO;
use Symfony\Component\Panther\PantherTestCase;
use Throwable;

/**
 * Class
 *
 * @class CentreonContext
 * @package Centreon\Test\Behat
 */
class CentreonContext extends UtilsContext
{
    /** @var PDO */
    protected $dbCentreon;
    /** @var PDO */
    protected $dbStorage;
    /** @var */
    protected $context;
    /** @var array */
    protected $output;
    /** @var Container */
    protected $container;
    /** @var string the service name of web container in docker compose file */
    protected $webService = 'web';
    /** @var string the service name of db container in docker compose file */
    protected $dbService = 'db';
    /** @var PollerConfigurationExportPage */
    protected $pollerConfigurationPage;

    /**
     * CentreonContext constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     *  Properly set WebDriver driver.
     *
     * @return void
     * @throws Exception
     */
    public function setContainerWebDriver(): void
    {
        try {
            $chromeArgs = [
                '--test-type',
                '--ignore-certificate-errors',
                '--start-maximized',
                '--silent-debugger-extension-api',
                '--no-default-browser-check',
                '--no-first-run',
                '--noerrdialogs',
                '--enable-fixed-layout',
                '--disable-popup-blocking',
                '--disable-password-generation',
                '--disable-single-click-autofill',
                '--disable-prompt-on-repos',
                '--disable-background-timer-throttling',
                '--disable-renderer-backgrounding',
                '--disable-renderer-throttling',
                '--disable-backgrounding-occluded-windows',
                '--disable-restore-session-state',
                '--disable-new-profile-management',
                '--disable-new-avatar-menu',
                '--allow-insecure-localhost',
                '--reduce-security-for-testing',
                '--enable-automation',
                '--disable-print-preview',
                '--disable-device-discovery-notifications',
                '--autoplay-policy=no-user-gesture-required',
                '--disable-site-isolation-trials',
                '--metrics-recording-only',
                '--disable-prompt-on-repost',
                '--disable-hang-monitor',
                '--disable-sync',
                '--disable-web-resources',
                '--safebrowsing-disable-download-protection',
                '--disable-client-side-phishing-detection',
                '--disable-component-update',
                "--simulate-outdated-no-au='Tue, 31 Dec 2099 23:59:59 GMT'",
                '--disable-default-apps',
                '--use-fake-ui-for-media-stream',
                '--use-fake-device-for-media-stream',
                '--disable-ipc-flooding-protection',
                '--disable-backgrounding-occluded-window',
                '--disable-breakpad',
                '--password-store=basic',
                '--use-mock-keychain',
                '--disable-dev-shm-usage',
                '--kiosk'
            ];

            $defaultOptions = [
                'external_base_uri' => 'http://' . $this->container->getHost() . ':'
                    . $this->container->getPort(80, $this->webService),
                'browser' => PantherTestCase::CHROME,
            ];

            $kernelOptions = []; # unused cause we do not extend class KernelTestCase

            $managerOptions = [
                'capabilities' => [
                    'goog:loggingPrefs' => [
                        'browser' => 'ALL', // calls to console.* methods
                    ],
                ],
            ];

            $_SERVER['PANTHER_NO_SANDBOX'] = 1;
            $_SERVER['PANTHER_CHROME_ARGUMENTS'] = implode(' ', $chromeArgs);

            $driver = new PantherDriver($defaultOptions, $kernelOptions, $managerOptions);
            $driver->start();
        } catch (Exception $e) {
            throw new Exception("Cannot instantiate panther driver : " . $e->getMessage(), (int) $e->getCode(), $e);
        }

        try {
            $session = new Session($driver);
            $mink = new Mink([
                'panther' => $session,
            ]);
            $mink->setDefaultSessionName('panther');
            $this->setMink($mink);
        } catch (Throwable $e) {
            throw new Exception("Cannot register mink session.\n" . $e->getMessage(), (int) $e->getCode(), $e);
        }
    }

    /**
     * after step hook
     *
     * @AfterStep
     *
     * @param AfterStepScope $scope
     *
     * @throws Exception
     */
    public function afterStep(AfterStepScope $scope): void
    {
        $this->takeScreenshotOnError($scope);

        if (isset($this->container)) {
            $containerLogs = $this->container->getLogs();
            if (preg_match_all('/(php (?:warning|fatal|notice|deprecated).+$)/mi', $containerLogs, $matches)) {
                throw new Exception('PHP log issues: ' . implode(', ', $matches[0]));
            }
        }
    }

    /**
     * Take a screenshot on error
     *
     * @param AfterStepScope $scope
     * @return void
     */
    private function takeScreenshotOnError(AfterStepScope $scope): void
    {
        $testResult = $scope->getTestResult();
        if (!$testResult->isPassed()) {
            $scenario = 'unknown';

            if ($scope->getTestResult()->hasException()
                && !$scope->getTestResult()->getException() instanceof PendingException) {
                echo $scope->getTestResult()->getException()->getFile()
                    . '('
                    . $scope->getTestResult()->getException()->getLine()
                    . ")\n\n"
                    . $scope->getTestResult()->getException()->getTraceAsString()
                    ;
            }

            $feature = $scope->getFeature();
            $step = $scope->getStep();
            $line = $step->getLine();

            foreach ($feature->getScenarios() as $tmp) {
                if ($tmp->getLine() > $line) {
                    break;
                }

                $scenario = $tmp->getTitle();
            }

            $scenarioTitle = preg_replace('/(\s|\/)+/', '_', $scenario);
            $filename = date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '-' . $scenarioTitle . '.png';
            $this->saveScreenshot($filename, $this->composeFiles['log_directory']);
        }
    }

    /**
     *  Unset container.
     *
     *  This will effectively stop and remove the container attached to
     *  this context if one was launched.
     *
     * @AfterScenario
     *
     * @param AfterScenarioScope $scope
     *
     * @return void
     */
    public function unsetContainer(AfterScenarioScope $scope): void
    {
        if (isset($this->container) && !$scope->getTestResult()->isPassed()) {
            $scenarioTitle = preg_replace('/(\s|\/)+/', '_', $scope->getScenario()->getTitle());
            $filename = $this->composeFiles['log_directory'] . '/'
                . date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '-' . $scenarioTitle . '.txt';

            // Container logs.
            $logTitle = ''
                . "##################\n"
                . "# Container logs #\n"
                . "##################\n\n";
            file_put_contents($filename, $logTitle);
            file_put_contents($filename, $this->container->getLogs(), FILE_APPEND);

            $driver = $this->getSession()->getDriver();
            if ($driver instanceof PantherDriver) {
                $logTitle = "\n"
                    . "########################\n"
                    . "# Browser console logs #\n"
                    . "########################\n\n";
                file_put_contents($filename, $logTitle, FILE_APPEND);
                file_put_contents(
                    $filename,
                    var_export(
                        $driver->getClient()->getWebDriver()->manage()->getLog('browser'),
                        true
                    ),
                    FILE_APPEND
                );
            }

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
            $output = $this->container->execute('cat /var/log/centreon/ldap.log 2>/dev/null', $this->webService, false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // MySQL process list.
            $logTitle = "\n\n"
                . "######################\n"
                . "# Mysql process list #\n"
                . "######################\n\n";
            $output = $this->container->execute('mysql -e "SHOW FULL PROCESSLIST" 2>/dev/null', $this->webService, false);
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

        // Stop Mink.
        if ($this->getMink()->isSessionStarted()) {
            $this->getMink()->getSession()->stop();
        }

        // Destroy container.
        unset($this->container);
    }

    /**
     * @Given a Centreon server
     *
     * @return void
     * @throws Exception
     */
    public function aCentreonServer(): void
    {
        $this->launchCentreonWebContainer('docker_compose_web');
    }

    /**
     * @Given a freshly installed Centreon server
     *
     * @return void
     * @throws Exception
     */
    public function aFreshlyInstalledCentreonServer(): void
    {
        $this->launchCentreonWebContainer('docker_compose_web', [], ['CENTREON_DATASET' => '0']);
    }

    /**
     * Login to Centreon
     *
     * @Given I am logged in
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedIn(): void
    {
        // Mandatory with the new version of behat/mink
        // A call on the 'visit' method must be perform to start a session.
        $page = new LoginPage($this);

        // Prepare credentials.
        $user = 'admin';
        $password = 'Centreon!2021';
        if (isset($this->parameters['centreon_user'])) {
            $user = $this->parameters['centreon_user'];
        }
        if (isset($this->parameters['centreon_password'])) {
            $password = $this->parameters['centreon_password'];
        }

        // Login.
        $page->login($user, $password);

        // Handle feature flipping
        $this->enableNewFeature();
    }

    /**
     * @param bool $confirm
     * @return void
     */
    public function enableNewFeature($confirm = true): void
    {
        if ($this->getSession()->getPage()->has('css', '#btcActivateFf')) {
            if ($confirm) {
                $this->assertFind('css', '#btcActivateFf')->click();
            } else {
                $this->assertFind('css', 'btcDisableFf')->click();
            }
        }
    }

    /**
     * @return LoginPage
     */
    public function iAmLoggedOut()
    {
        // LoginPage constructor will automatically throw if we are
        // not on the login page.
        $logoutUrl = 'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, $this->webService)
            . '/centreon/authentication/logout';
        $sessionId = $this->getSession()->getDriver()->getCookie('PHPSESSID');

        $curlLogout = curl_init($logoutUrl);
        curl_setopt($curlLogout, CURLOPT_COOKIE, 'PHPSESSID=' . $sessionId);
        curl_setopt($curlLogout, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($curlLogout, CURLOPT_TIMEOUT, 5);
        curl_setopt($curlLogout, CURLOPT_RETURNTRANSFER, true);
        curl_exec($curlLogout);
        curl_close($curlLogout);

        return new LoginPage($this, true);
    }

    /**
     * Make sure we have a Centreon server and log in.
     *
     * @Given I am logged in a Centreon server
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInACentreonServer(): void
    {
        $this->aCentreonServer();
        $this->iAmLoggedIn();
    }

    /**
     * Log in a Centreon server and set timezone
     *
     * @Given /^I am logged in a Centreon server located at "(.+)"$/
     *
     * @param $timezone
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInACentreonServerLocatedAt($timezone): void
    {
        $this->iAmLoggedInACentreonServer();
        $this->container->execute(
            "ln -snf /usr/share/zoneinfo/" . $timezone . " /etc/localtime",
            $this->webService
        );
        $this->container->execute(
            'bash -c "echo ' . $timezone . ' > /etc/timezone"',
            $this->webService
        );
    }

    /**
     * @Given I am logged in a Centreon server with a configured proxy
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInACentreonServerWithAConfiguredProxy(): void
    {
        $this->launchCentreonWebContainer('docker_compose_web', ['squid-simple']);
        $this->iAmLoggedIn();
        $this->setConfiguredProxy();
    }

    /**
     * @Given I am logged in a Centreon server with a configured ldap
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInACentreonServerWithAConfiguredLdap(): void
    {
        // Launch container.
        $this->launchCentreonWebContainer('docker_compose_web', ['openldap']);
        $this->iAmLoggedIn();

        // Configure LDAP parameters.
        $page = new LdapConfigurationPage($this);
        $page->setProperties(array(
            'configuration_name' => 'OpenLDAP',
            'description' => 'LDAP service provided by an OpenLDAP container.',
            'enable_authentication' => '1',
            'auto_import' => '1',
            'servers_host_address' => 'openldap',
            'servers_host_port' => '389',
            'bind_user' => 'cn=admin,dc=centreon,dc=com',
            'bind_password' => 'centreon',
            'protocol_version' => '3',
            'template' => 'Posix',
            'search_user_base_dn' => 'dc=centreon,dc=com',
            'search_group_base_dn' => 'dc=centreon,dc=com',
            'user_filter' => '(&(uid=%s)(objectClass=posixAccount))'
        ));
        $page->save();
    }

    /**
     * @Given I am logged in a Centreon server with configured metrics
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInACentreonServerWithConfiguredMetrics(): void
    {
        $this->aCentreonServer();
        $this->iAmLoggedIn();
        $this->getServiceWithSeveralMetrics();
    }

    /**
     * Make sure we have a freshly installed Centreon server and log in.
     *
     * @Given I am logged in a freshly installed Centreon server
     *
     * @return void
     * @throws Exception
     */
    public function iAmLoggedInAFreshlyInstalledCentreonServer(): void
    {
        $this->aFreshlyInstalledCentreonServer();
        $this->iAmLoggedIn();
    }

    /**
     * Execute a command.
     *
     * @param string $command Command to execute.
     * @param string $service Docker service to which this
     *                                command should be addressed.
     * @param boolean $throwOnError True to throw an error if the
     *                                command fails to execute.
     * @return mixed
     */
    public function execute($command, $service, $throwOnError = true)
    {
        $returnCmd = $this->container->execute($command, $service, $throwOnError);

        return $returnCmd;
    }


    /**
     * Get Centreon database connection
     *
     * @return PDO The database connection
     */
    public function getCentreonDatabase()
    {
        if (!isset($this->dbCentreon)) {
            $dsn = 'mysql:dbname=centreon;host=' . $this->container->getHost() . ';port=' .
                $this->container->getPort(3306, $this->dbService);
            $this->dbCentreon = new PDO(
                $dsn,
                'root',
                'centreon'
            );
            $this->dbCentreon->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->dbCentreon;
    }

    /**
     *  Get Centreon Storage database connection.
     *
     * @return PDO The database connection.
     */
    public function getStorageDatabase()
    {
        if (!isset($this->dbStorage)) {
            $dsn = 'mysql:dbname=centreon_storage;host=' . $this->container->getHost() . ';port=' .
                $this->container->getPort(3306, $this->dbService);
            $this->dbStorage = new PDO(
                $dsn,
                'root',
                'centreon'
            );
            $this->dbStorage->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        }
        return $this->dbStorage;
    }

    /**
     * Launch Centreon Web container and setup context.
     *
     * @param string $composeBehatProperty Bind property to docker-compose.yml path
     * @param string[] $profiles docker-compose profiles to activate
     * @param array<string,string|int|boolean> $envVars docker composer environment variables
     * @throws Exception
     */
    public function launchCentreonWebContainer(
        string $composeBehatProperty,
        array $profiles = [],
        array $envVars = []
    ): void {
        foreach ($profiles as $profile) {
            if (preg_match('/^web(?!driver)/', $profile)) {
                $this->webService = $profile;
            }
        }

        if (!isset($this->composeFiles[$composeBehatProperty])) {
            throw new Exception('Property "' . $composeBehatProperty . '" does not exist in behat.yml');
        }

        $this->container = new Container($this->composeFiles[$composeBehatProperty], $profiles, $envVars);

        $this->setContainerWebDriver();

        // Set session parameters.
        $this->setMinkParameter(
            'base_url',
            'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, $this->webService)
                . '/centreon'
        );

        /**
         * set api base url param
         */
        $this->setMinkParameter(
            'api_base',
            'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, $this->webService)
                . '/centreon'
        );

        // Real application test, create an API authentication token.
        $ch = curl_init(
            'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, $this->webService) .
            '/centreon/api/latest/platform/versions'
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        $limit = time() + 60;
        while (time() < $limit) {
            curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            if ($httpCode === 200) {
                break;
            }
            sleep(1);
        }

        if (time() >= $limit) {
            throw new Exception(
                'Centreon Web did not respond within a 60 seconds time frame (API authentication test).'
            );
        }
    }

    /**
     * Set a proxy URL and port
     *
     * @return void
     * @throws Exception
     */
    public function setConfiguredProxy(): void
    {
        $proxyConfig = new ParametersCentreonUiPage($this);
        $proxyConfig->setProperties(array(
            'proxy_url'=> 'squid-simple',
            'proxy_port'=> '3128'
        ));
        $proxyConfig->save();
    }

    /**
     * Submit a passive result for a host (and wait)
     *
     * @param string $hostname
     * @param string $checkResult
     * @param string $checkOutput
     * @param string $performanceData
     *
     * @return void
     */
    public function submitHostResult($hostname, $checkResult, $checkOutput = '', $performanceData = ''): void
    {
        // Page in : Monitoring > Status Details > Hosts
        $this->visit('/main.php?p=20202&o=hpc&cmd=16&host_name=' . $hostname);

        // Configure the "Check result" dropdown field
        $this->getSession()->getPage()->selectFieldOption('return_code', $checkResult);

        // Configure the "Check output" field
        if (!empty($checkOutput)) {
            $this->assertFindField('output')->setValue($checkOutput);
        }

        // Configure the "Performance data" field
        if (!empty($performanceData)) {
            $this->assertFindField('dataPerform')->setValue($performanceData);
        }

        // Submit global forms
        $this->assertFindButton('Save')->click();

        // Wait
        $this->getSession()->wait(5000);
    }

    /**
     * Submit a passive result for a service (and wait)
     *
     * @param string $hostname
     * @param string $serviceDescription
     * @param $checkResult
     * @param string $checkOutput
     * @param string $performanceData
     * @return void
     */
    public function submitServiceResult(
        $hostname,
        $serviceDescription,
        $checkResult,
        $checkOutput = '',
        $performanceData = ''
    ): void {
        // Page in : Monitoring > Status Details > Services
        $this->visit(
            '/main.php?p=20201&o=svcpc&cmd=16&host_name=' . $hostname .
            '&service_description=' . $serviceDescription . '&is_meta=false'
        );

        // Configure the "Check result" dropdown field
        $this->getSession()->getPage()->selectFieldOption('return_code', $checkResult);

        // Configure the "Check output" field
        if (!empty($checkOutput)) {
            $this->assertFindField('output')->setValue($checkOutput);
        }

        // Configure the "Performance data" field
        if (!empty($performanceData)) {
            $this->assertFindField('dataPerform')->setValue($performanceData);
        }

        // Submit global forms
        $this->assertFindButton('Save')->click();

        // Wait
        $this->getSession()->wait(5000);
    }

    /**
     *  Restart all pollers.
     *
     * @return void
     * @throws SpinStopException
     */
    public function restartAllPollers(): void
    {
        $page = new PollerConfigurationExportPage($this);
        $page->setProperties(array(
            'pollers' => 'all',
            'generate_files' => true,
            'run_debug' => true,
            'move_files' => true,
            'restart_engine' => true,
            'restart_method' => PollerConfigurationExportPage::METHOD_RESTART
        ));
        $page->export();
    }

    /**
     *  Reload all pollers.
     *
     * @return void
     * @throws SpinStopException
     */
    public function reloadAllPollers(): void
    {
        $reloadCount = $this->getEngineReloadCount();

        $page = new PollerConfigurationExportPage($this);
        $page->setProperties(array(
            'pollers' => 'all',
            'generate_files' => true,
            'run_debug' => true,
            'move_files' => true,
            'restart_engine' => true,
            'restart_method' => PollerConfigurationExportPage::METHOD_RELOAD
        ));
        $page->export();

        $this->spin(
            function($context) use ($reloadCount) {
               return $context->getEngineReloadCount() > $reloadCount;
            },
            'centreon engine is not reloaded',
            60
        );
    }

    /**
     * Get count of reload from centreon engine logs
     *
     * @return int
     */
    private function getEngineReloadCount(): int
    {
        $getEngineLogsCommand = 'grep "Configuration reloaded" /var/log/centreon-engine/centengine.log | wc -l';

        $output = $this->container->execute(
            $getEngineLogsCommand,
            $this->webService,
            true
        );

        return is_numeric($output['output']) ? (int) $output['output'] : 0;
    }

    /**
     *  Run yum update centreon-web in the container.
     *
     * @return void
     */
    public function yumUpdateCentreonWeb(): void
    {
        $this->context->container->execute(
            'yum clean all',
            $this->webService
        );
        $this->context->container->execute(
            'yum update -y --nogpgcheck centreon-web',
            $this->webService
        );
    }

    /**
     * Get polling state in top counter
     *
     * @return bool
     */
    public function getPollingState()
    {
        $title = $this->assertFind('css', 'img#img_pollingState')->getAttribute('title');
        if (preg_match('/^OK/', $title)) {
            return true;
        }
        return false;
    }

    /**
     * Reload ACL with command line
     *
     * @return void
     */
    public function reloadACL(): void
    {
        $this->container->execute(
            'su -s /bin/sh apache -c "/usr/bin/env php -q /usr/share/centreon/cron/centAcl.php"',
            $this->webService,
            false
        );
    }

    /**
     * @return void
     * @throws Exception
     */
    public function getServiceWithSeveralMetrics(): void
    {
        // Create host.
        $hostConfig = new HostConfigurationPage($this);
        $hostProperties = array(
            'name' => 'MetricTestHostname',
            'alias' => 'MetricTestHostname',
            'address' => 'localhost',
            'max_check_attempts' => 1,
            'normal_check_interval' => 1,
            'retry_check_interval' => 1,
            'active_checks_enabled' => "0",
            'passive_checks_enabled' => "1"
        );
        $hostConfig->setProperties($hostProperties);
        $hostConfig->save();

        // Create service.
        $serviceConfig = new ServiceConfigurationPage($this);
        $serviceProperties = array(
            'description' => 'MetricTestService',
            'hosts' => 'MetricTestHostname',
            'templates' => 'generic-service',
            'check_command' => 'check_centreon_dummy',
            'check_period' => '24x7',
            'active_checks_enabled' => "0",
            'passive_checks_enabled' => "1"
        );
        $serviceConfig->setProperties($serviceProperties);
        $serviceConfig->save();

        // Ensure service is monitored.
        $this->reloadAllPollers();

        // Send multiple perfdata.
        $perfdata = '';
        for ($i = 0; $i < 20; $i++) {
            $perfdata .= 'test' . $i . '=1s ';
        }
        $this->submitServiceResult('MetricTestHostname', 'MetricTestService', 'OK', 'OK', $perfdata);

        // Ensure perfdata were processed.
        $this->spin(
            function ($context) {
                $page = new ServiceMonitoringDetailsPage(
                    $context,
                    'MetricTestHostname',
                    'MetricTestService'
                );
                $properties = $page->getProperties();
                if (count($properties['perfdata']) < 20) {
                    return false;
                }
                return true;
            },
            'Cannot get performance data of MetricTestHostname / MetricTestService'
        );

        $this->checkForMetricAvaibility('MetricTestHostname', 'MetricTestService', 'test10');
    }

    /**
     * @param string $hostname
     * @param string $serviceDescription
     * @param string $metricName
     *
     * @throws Exception
     */
    public function checkForMetricAvaibility($hostname, $serviceDescription, $metricName): void
    {
        $metricId = $this->getMetricId($hostname, $serviceDescription, $metricName);
        $rrdMetricFile = $this->getRrdPath() . $metricId . '.rrd';

        $this->spin(
            function($context) use ($rrdMetricFile) {
               return $context->checkRrdFilesAreAvalaible($rrdMetricFile);
            },
            'No Metrics available or check rrd files!'
        );
    }

    /**
     * @return string
     * @throws Exception
     */
    private function getRrdPath()
    {
        $query = "SELECT RRDdatabase_path FROM config";

        $stmt = $this->getStorageDatabase()->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch();
        if ($res === false) {
            throw new Exception('Cannot get RRD path in database.');
        }
        return $res['RRDdatabase_path'];
    }

    /**
     * @param string $rrdMetricFile
     * @return bool
     */
    private function checkRrdFilesAreAvalaible($rrdMetricFile)
    {
        $rrdFileExist = false;
        $output = $this->container->execute('ls ' . $rrdMetricFile .' 2>/dev/null', $this->webService, false);

        if ($output['output'] === $rrdMetricFile) {
            $rrdFileExist = true;
        }

        return $rrdFileExist;
    }

    /**
     *
     * @param string $metricName
     * @param string $hostname
     * @param string $serviceDescription
     * @return int
     * @throws Exception
     */
    private function getMetricId($hostname, $serviceDescription, $metricName)
    {
        // Get Metrics Id From Hostname - Service Descriptionn and Metric name
        $query = "SELECT m.metric_id "
            . "FROM index_data i, metrics m "
            . "WHERE i.host_name = :hostname "
            . "AND i.service_description = :servicedescription "
            . "AND m.metric_name = :metricname "
            . "AND m.index_id = i.id";

        $stmt = $this->getStorageDatabase()->prepare($query);
        $stmt->bindParam(':hostname', $hostname, PDO::PARAM_STR);
        $stmt->bindParam(':servicedescription', $serviceDescription, PDO::PARAM_STR);
        $stmt->bindParam(':metricname', $metricName, PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();
         if ($res === false) {
            throw new Exception('Cannot get metric id in database.');
        }

        return $res['metric_id'];
    }

    /**
     * @When I generate configuration files without export
     *
     * @return void
     * @throws SpinStopException
     */
    public function exportConfigurationFiles(): void
    {
        $page = new PollerConfigurationExportPage($this);
        $page->setProperties([
            'pollers' => 'all',
            'generate_files' => true,
            'run_debug' => true,
        ]);
        $page->export();
    }

    /**
     * @Then use the page object :pageObject and set the properties below
     *
     * @param string $pageObject
     * @param TableNode $table
     *
     * @return void
     * @throws Exception
     */
    public function usePageObjectAndSetProperties(string $pageObject, TableNode $table): void
    {
        if (!class_exists($pageObject)) {
            throw new Exception("Page object didn't exists {$pageObject}");
        }

        $data = [];
        foreach ($table as $row) {
            $data[] = str_replace(
                ['\\t', '\\n', '\\r'],
                ["\t", "\n", "\r"],
                $row
            );
        }

        foreach ($data as $properties) {
            try {
                $page = new $pageObject($this);
                $page->setProperties($properties);
                $page->save();
            } catch (Exception $e) {
                throw new Exception('Failure when trying to save page object "'
                    . $pageObject.'" with the properties '
                    . json_encode($properties), $e->getCode(), $e);
            }
        }
    }

    /**
     * @When execute in console of service :service
     *
     * @param string $service
     * @param PyStringNode $command
     *
     * @return void
     */
    public function executeInConsole(string $service, PyStringNode $command): void
    {
        $this->output = $this->execute($command->getRaw(), $service);
    }

    /**
     * @Then the expected result have to be
     *
     * @param PyStringNode $result
     *
     * @return void
     * @throws Exception
     */
    public function theExpectedResultHaveToBe(PyStringNode $result): void
    {
        $output = $this->output['output'] ?? '';

        if ($result->getRaw() !== $output) {
            throw new Exception("The result doesn't match: {$output}");
        }
    }

    /**
     * @Then the expected result matched to the pattern
     *
     * @param PyStringNode $result
     *
     * @return void
     * @throws Exception
     */
    public function theExpectedResultMatchedToThePattern(PyStringNode $result): void
    {
        $output = $this->output['output'] ?? '';

        if (!fnmatch($result->getRaw(), $output)) {
            throw new Exception("The result doesn't match: {$output}");
        }
    }

    /**
     * check if expected properties match current page properties
     *
     * @param ConfigurationPage $currentPage
     * @param array $expectedProperties
     * @throws Exception
     */
    protected function comparePageProperties(ConfigurationPage $currentPage, array $expectedProperties): void
    {
        $this->spin(
            function () use ($currentPage, $expectedProperties)  {
                $wrongProperties = [];
                $currentProperties = $currentPage->getProperties();
                foreach ($expectedProperties as $key => $value) {
                    if ($value != $currentProperties[$key]) {
                        if (is_array($value)) {
                            $value = implode(' ', $value);
                        }
                        if ($value != $currentProperties[$key]) {
                            $wrongProperties[] = $key;
                        }
                    }
                }

                if (!empty($wrongProperties)) {
                    throw new Exception(
                        "Some properties are not being updated : " . implode(',', array_unique($wrongProperties))
                    );
                }

                return true;
            },
            "Load Timeout",
            60
        );
    }
}
