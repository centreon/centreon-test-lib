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
use Centreon\Test\Behat\HostConfigurationPage;
use Centreon\Test\Behat\PollerConfigurationExportPage;
use Centreon\Test\Behat\ServiceConfigurationPage;
use Centreon\Test\Behat\UtilsContext;

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
        if (isset($this->container) && !$scope->getTestResult()->isPassed()) {
            $filename = $this->composeFiles['log_directory'] . '/' . date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '.txt';
            file_put_contents($filename, $this->container->getLogs());
        }
        if ($this->getMink()->isSessionStarted()) {
            $this->getMink()->getSession()->stop();
        }
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
        /* Prepare credentials */
        $user = 'admin';
        $password = 'centreon';
        if (isset($this->parameters['centreon_user'])) {
            $user = $this->parameters['centreon_user'];
        }
        if (isset($this->parameters['centreon_password'])) {
            $password = $this->parameters['centreon_password'];
        }

        $this->visit('/');

        /* Login to Centreon */
        $page = $this->getSession()->getPage();
        $userField = $this->assertFind('css', 'input[name="useralias"]');
        $userField->setValue($user);
        $passwordField = $this->assertFind('css', 'input[name="password"]');
        $passwordField->setValue($password);
        $formLogin = $this->assertFind('css', 'form[name="login"]');
        $formLogin->submit();
        $this->spin(
            function ($context) use ($page) {
                return $page->has('css', 'a[href="main.php?p=103"]');
            }
        );
    }

    public function iAmLoggedOut()
    {
        $this->visit('index.php?disconnect=1');

        $page = $this->getSession()->getPage();
        $mythis = $this;
        $this->spin(
            function ($mythis) use ($page) {
                return $page->has('css', 'input[name="useralias"]');
            }
        );
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
     */
    public function launchCentreonWebContainer($name)
    {
        $composeFile = $this->getContainerComposeFile($name);
        if (empty($composeFile)) {
            throw new \Exception('Could not launch containers without Docker Compose file for ' . $name . ': check the configuration of your ContainerExtension in behat.yml.');
        }
        $this->container = new Container($composeFile);
        $this->setContainerWebDriver();

        // Wait for WebDriver.
        $ch = curl_init('http://' . $this->container->getHost() . ':' . $this->container->getPort(4444, 'webdriver') . '/status');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $limit = time() + 60;
        while ((time() < $limit) && (($res === false) || empty($res))) {
            sleep(1);
            $res = curl_exec($ch);
        }
        if (time() >= $limit) {
            throw new \Exception('WebDriver did not respond within a 60 seconds time frame.');
        }

        // Real application test, create an API authentication token.
        $ch = curl_init('http://' . $this->container->getHost() . ':' . $this->container->getPort(80, 'web') . '/centreon/api/index.php?action=authenticate');
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt(
            $ch,
            CURLOPT_POSTFIELDS,
            array('username' => 'admin', 'password' => 'centreon'));
        $res = curl_exec($ch);
        $limit = time() + 120;
        while ((time() < $limit) && (($res === false) || empty($res))) {
            sleep(1);
            $res = curl_exec($ch);
        }
        if (time() >= $limit) {
            throw new \Exception('Centreon Web did not respond within a 120 seconds time frame (API authentication test).');
        }

        // Set Mink parameter.
        $this->setMinkParameter('base_url', 'http://web/centreon');
    }

    /**
     *  Properly set WebDriver driver.
     */
    public function setContainerWebDriver()
    {
        $url = 'http://' . $this->container->getHost() . ':' . $this->container->getPort(4444, 'webdriver') . '/wd/hub';
        $sessionName = $this->getMink()->getDefaultSessionName();
        $driver = new \Behat\Mink\Driver\Selenium2Driver('phantomjs', null, $url);
        $driver->setTimeouts(array(
            'page load' => 120000,
            'script' => 120000
        ));
        $session = new \Behat\Mink\Session($driver);
        $this->getMink()->registerSession($sessionName, $session);
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
}
