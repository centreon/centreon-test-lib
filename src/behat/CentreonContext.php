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

use WebDriver\WebDriver;

class CentreonContext extends UtilsContext
{
    protected $container;

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
    public function unsetContainer()
    {
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
        $url = 'http://localhost:' . $this->container->getPort(80, 'web') . '/centreon';
        $this->container->waitForAvailableUrl($url);
        $this->setMinkParameter('base_url', $url);
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
}
