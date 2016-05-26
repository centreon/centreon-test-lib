<?php
/**
 * Copyright 2016 Centreon
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
use Centreon\Test\Behat\ServiceConfigurationPage;

class CentreonContext extends UtilsContext
{
    protected $container;
    protected $hostConfigurationPage;
    protected $serviceConfigurationPage;
    protected $pollerConfigurationPage;

    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($paramaters = array())
    {
        parent::__construct($paramaters);
    }

    /**
     *  Unset container.
     *
     *  This will effectively stop and remove the container attached to
     *  this context if one was launched.
     *
     *  @AfterScenario
     */
    public function unsetContainer(AfterScenarioScope $scope)
    {
        if (isset($this->container) && !$scope->getTestResult()->isPassed()) {
            $filename = '/tmp/' . date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '.txt';
            file_put_contents($filename, $this->container->getLogs());
        }
        if ($this->getMink()->isSessionStarted()) {
            $this->getMink()->getSession()->stop();
        }
        unset($this->container);
    }

    /**
     *  @Given a Centreon server
     */
    public function aCentreonServer()
    {
        $this->launchCentreonWebContainer('web');
    }

    /**
     *  @Given a freshly installed Centreon server
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
            $password = $this->paramaters['centreon_password'];
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
            },
            30
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
     *  @param string $cmd Command to execute.
     */
    public function execute($command, $service)
    {
        $returnCmd = $this->container->execute($command, $service);

        return $returnCmd;
    }

    /**
     *  Launch Centreon Web container and setup context.
     *
     *  @param $name Entry name.
     */
    public function launchCentreonWebContainer($name)
    {
        $composeFile = $this->getContainerComposeFile($name);
        if (empty($composeFile)) {
            throw new \Exception('Could not launch containers without Docker Compose file for ' . $name . ': check the configuration of your ContainerExtension in behat.yml.');
        }
        $this->container = new Container($composeFile);
        $this->setContainerWebDriver();
        $url = 'http://127.0.0.1:' . $this->container->getPort(80, 'web') . '/centreon';
        $this->container->waitForAvailableUrl($url);
        $this->setMinkParameter('base_url', 'http://web/centreon');
    }

    /**
     *  Properly set WebDriver driver.
     */
    public function setContainerWebDriver()
    {
        $url = 'http://localhost:' . $this->container->getPort(4444, 'webdriver') . '/wd/hub';
        $sessionName = $this->getMink()->getDefaultSessionName();
        $driver = new \Behat\Mink\Driver\Selenium2Driver('phantomjs', null, $url);
        $session = new \Behat\Mink\Session($driver);
        $this->getMink()->registerSession($sessionName, $session);
    }

    /**
     *  Get the host configuration page object.
     */
    public function getHostConfigurationPage()
    {
      if (!isset($this->hostConfigurationPage)) {
        $this->hostConfigurationPage = new HostConfigurationPage($this);
      }
      return ($this->hostConfigurationPage);
    }

    /**
     *  Get the service configuration page object.
     */
    public function getServiceConfigurationPage()
    {
      if (!isset($this->serviceConfigurationPage)) {
        $this->serviceConfigurationPage = new ServiceConfigurationPage($this);
      }
      return ($this->serviceConfigurationPage);
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
      if (! empty($checkOutput)) {
         $this->assertFindField('output')->setValue($checkOutput);
      }

      // Configure the "Performance data" field
      if (! empty($performanceData)) {
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
    public function submitServiceResult($hostname, $serviceDescription, $checkResult, $checkOutput = '', $performanceData = '')
    {
      // Page in : Monitoring > Status Details > Services
      $this->visit('/main.php?p=20201&o=svcpc&cmd=16&host_name=' . $hostname . '&service_description=' . $serviceDescription);

      // Configure the "Service" dropdown field
      $this->getSession()->getPage()->selectFieldOption('service_description', $serviceDescription);

      // Configure the "Check result" dropdown field
      $this->getSession()->getPage()->selectFieldOption('return_code', $checkResult);

      // Configure the "Check output" field
      if (! empty($checkOutput)) {
         $this->assertFindField('output')->setValue($checkOutput);
      }

      // Configure the "Performance data" field
      if (! empty($performanceData)) {
         $this->assertFindField('dataPerform')->setValue($performanceData);
      }

      // Submit global forms
      $this->assertFindButton('Save')->click();

      // Wait
      $this->getSession()->wait(5000, '');
    }
}
