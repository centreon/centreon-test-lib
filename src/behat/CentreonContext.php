<?php
/**
 * Copyright 2016-2017 Centreon
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
use WebDriver\WebDriver;
use Centreon\Test\Behat\External\LoginPage;
use Centreon\Test\Behat\Configuration\PollerConfigurationExportPage;
use Centreon\Test\Behat\Configuration\HostConfigurationPage;
use Centreon\Test\Behat\Configuration\ServiceConfigurationPage;
use Centreon\Test\Behat\Monitoring\ServiceMonitoringDetailsPage;
use Centreon\Test\Behat\Administration\ParametersCentreonUiPage;

class CentreonContext extends UtilsContext
{
    public $container;
    protected $pollerConfigurationPage;

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
            $scenarioTitle = preg_replace('/\s+/', '_', $scope->getScenario()->getTitle());
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
            $output = $this->container->execute('cat /var/log/centreon-engine/centengine.log 2>/dev/null', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Broker logs.
            $logTitle = "\n\n"
                . "###############\n"
                . "# Broker logs #\n"
                . "###############\n\n";
            $output = $this->container->execute('bash -c "cat /var/log/centreon-broker/*.log 2>/dev/null"', 'web', false);
            file_put_contents($filename, $logTitle, FILE_APPEND);
            file_put_contents($filename, $output['output'], FILE_APPEND);

            // Centreon Broker logs.
            $logTitle = "\n\n"
                . "#################\n"
                . "# Centcore logs #\n"
                . "#################\n\n";
            $output = $this->container->execute('cat /var/log/centreon/centcore.log 2>/dev/null', 'web', false);
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

        // Stop Mink.
        if ($this->getMink()->isSessionStarted()) {
            $this->getMink()->getSession()->stop();
        }

        // Destroy container.
        unset($this->container);
    }

    /**
     * @Given a Centreon server
     */
    public function aCentreonServer()
    {
        $this->launchCentreonWebContainer('web');
    }

    /**
     * @Given a freshly installed Centreon server
     */
    public function aFreshlyInstalledCentreonServer()
    {
        $this->launchCentreonWebContainer('web_fresh');
    }

    /**
     * Login to Centreon
     *
     * @Given I am logged in
     */
    public function iAmLoggedIn()
    {
        // Set Window Size
        $this->getSession()->resizeWindow(1600, 4000);

        // Prepare credentials.
        $user = 'admin';
        $password = 'centreon';
        if (isset($this->parameters['centreon_user'])) {
            $user = $this->parameters['centreon_user'];
        }
        if (isset($this->parameters['centreon_password'])) {
            $password = $this->parameters['centreon_password'];
        }

        // Login.
        $page = new LoginPage($this);
        $page->login($user, $password);
    }

    public function iAmLoggedOut()
    {
        // LoginPage constructor will automatically throw if we are
        // not on the login page.
        $this->visit('index.php?disconnect=1');
        return new LoginPage($this, false);
    }

    /**
     * Make sure we have a Centreon server and log in.
     *
     * @Given I am logged in a Centreon server
     */
    public function iAmLoggedInACentreonServer()
    {
        $this->aCentreonServer();
        $this->iAmLoggedIn();
    }

    /**
     * Log in a Centreon server and set timezone
     *
     * @Given /^I am logged in a Centreon server located at "(.+)"$/
     */
    public function iAmLoggedInACentreonServerLocatedAt($timezone)
    {
        $this->iAmLoggedInACentreonServer();
        $this->container->execute(
            "ln -snf /usr/share/zoneinfo/" . $timezone . " /etc/localtime",
            'web'
        );
        $this->container->execute(
            'bash -c "echo ' . $timezone . ' > /etc/timezone"',
            'web'
        );
    }

    /**
    * @Given I am logged in a Centreon server with a configured proxy
    */
    public function iAmLoggedInACentreonServerWithAConfiguredProxy()
    {
        $this->launchCentreonWebContainer('web_squid_simple');
        $this->iAmLoggedIn();
        $this->setConfiguredProxy();
    }

    /**
     * @Given I am logged in a Centreon server with a configured ldap
     */
    public function iAmLoggedInACentreonServerWithAConfiguredLdap()
    {
        $this->launchCentreonWebContainer('web_openldap');
        $this->iAmLoggedIn();
    }

    /**
     * @Given I am logged in a Centreon server with configured metrics
     */
    public function iAmLoggedInACentreonServerWithConfiguredMetrics()
    {
        $this->aCentreonServer();
        $this->iAmLoggedIn();
        $this->getServiceWithSeveralMetrics();
    }

    /**
     * Make sure we have a freshly installed Centreon server and log in.
     *
     * @Given I am logged in a freshly installed Centreon server
     */
    public function iAmLoggedInAFreshlyInstalledCentreonServer()
    {
        $this->aFreshlyInstalledCentreonServer();
        $this->iAmLoggedIn();
    }

    /**
     *  Execute a command.
     *
     * @param string $cmd Command to execute.
     * @param string $service Docker service to which this
     *                                command should be addressed.
     * @param boolean $throwOnError True to throw an error if the
     *                                command fails to execute.
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
            $dsn = 'mysql:dbname=centreon;host=' . $this->container->getHost() . ';port=' . $this->container->getPort(3306,
                    'web');
            $this->dbCentreon = new \PDO(
                $dsn,
                'root',
                'centreon'
            );
            $this->dbCentreon->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
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
            $dsn = 'mysql:dbname=centreon_storage;host=' . $this->container->getHost() . ';port=' . $this->container->getPort(3306,
                    'web');
            $this->dbStorage = new \PDO(
                $dsn,
                'root',
                'centreon'
            );
            $this->dbStorage->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        }
        return $this->dbStorage;
    }

    /**
     *  Launch Centreon Web container and setup context.
     *
     * @param $name Entry name.
     * @throws \Exception
     */
    public function launchCentreonWebContainer($name)
    {
        $composeFile = $this->getContainerComposeFile($name);
        if (empty($composeFile)) {
            throw new \Exception(
                'Could not launch containers without Docker Compose file for ' . $name . ': '
                . 'check the configuration of your ContainerExtension in behat.yml.'
            );
        }
        $this->container = new Container($composeFile);
        $this->setContainerWebDriver();

        // Set session parameters.
        $this->setMinkParameter(
            'base_url',
            'http://' . $this->container->getContainerId('web', false) . '/centreon'
        );

        // Real application test, create an API authentication token.
        $ch = curl_init(
            'http://' . $this->container->getHost() . ':' . $this->container->getPort(80, 'web') .
            '/centreon/api/index.php?action=authenticate'
        );
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array('username' => 'admin', 'password' => 'centreon'));
        $res = curl_exec($ch);
        $limit = time() + 60;
        while ((time() < $limit) && (($res === false) || empty($res))) {
            sleep(1);
            $res = curl_exec($ch);
        }

        if (time() >= $limit) {
            throw new \Exception(
                'Centreon Web did not respond within a 120 seconds time frame (API authentication test).'
            );
        }
    }

    /**
     * Set a proxy URL and port
     */
    public function setConfiguredProxy()
    {
        $proxyConfig = new ParametersCentreonUiPage($this);
        $proxyConfig->setProperties(array(
            'proxy_url'=> 'squid',
            'proxy_port'=> '3128'
        ));
        $proxyConfig->save();
    }

    /**
     * Submit a passive result for a host (and wait)
     *
     * @param string hostname
     * @param checkResult
     * @param string checkOutput
     * @param string performanceData
     */
    public function submitHostResult($hostname, $checkResult, $checkOutput = '', $performanceData = '')
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
        $this->getSession()->wait(5000, '');
    }

    /**
     * Submit a passive result for a service (and wait)
     *
     * @param string hostname
     * @param checkResult
     * @param string checkOutput
     * @param string performanceData
     */
    public function submitServiceResult(
        $hostname,
        $serviceDescription,
        $checkResult,
        $checkOutput = '',
        $performanceData = ''
    ) {
        // Page in : Monitoring > Status Details > Services
        $this->visit('/main.php?p=20201&o=svcpc&cmd=16&host_name=' . $hostname . '&service_description=' . $serviceDescription . '&is_meta=false');

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
        $this->getSession()->wait(5000, '');
    }

    /**
     *  Restart all pollers.
     */
    public function restartAllPollers()
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
     */
    public function reloadAllPollers()
    {
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
    }

    /**
     *  Run yum update centreon-web in the container.
     */
    public function yumUpdateCentreonWeb()
    {
        $this->context->container->execute(
            'yum clean all',
            'web'
        );
        $this->context->container->execute(
            'yum update -y --nogpgcheck centreon-web',
            'web'
        );
    }

    /**
     * Get polling state in top counter
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
     */
    public function reloadACL()
    {
        $this->container->execute(
            'su -s /bin/sh apache -c "/usr/bin/php -q /usr/share/centreon/cron/centAcl.php"',
            'web',
            false
        );
    }

    public function getServiceWithSeveralMetrics()
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
        $this->restartAllPollers();

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
     *
     * @param string $metricName
     * @param string $hostname
     * @param string $serviceDescription
     */
    public function checkForMetricAvaibility($hostname, $serviceDescription, $metricName)
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
     *
     * @return string
     * @throws \Exception
     */
    private function getRrdPath()
    {
        $query = "SELECT RRDdatabase_path FROM config";

        $stmt = $this->getStorageDatabase()->prepare($query);
        $stmt->execute();
        $res = $stmt->fetch();
        if ($res === false) {
            throw new \Exception('Cannot get RRD path in database.');
        }
        return $res['RRDdatabase_path'];
    }

    /**
     *
     * @param string $rrdMetricFile
     * @return boolean
     */
    private function checkRrdFilesAreAvalaible($rrdMetricFile)
    {
        $rrdFileExist = false;
        $output = $this->container->execute('ls ' . $rrdMetricFile .' 2>/dev/null', 'web', false);

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
     * @throws \Exception
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
        $stmt->bindParam(':hostname', $hostname, \PDO::PARAM_STR);
        $stmt->bindParam(':servicedescription', $serviceDescription, \PDO::PARAM_STR);
        $stmt->bindParam(':metricname', $metricName, \PDO::PARAM_STR);
        $stmt->execute();
        $res = $stmt->fetch();
         if ($res === false) {
            throw new \Exception('Cannot get metric id in database.');
        }

        return $res['metric_id'];
    }
}
