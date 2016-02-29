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

use Behat\Behat\Context\Context;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;
use Behat\MinkExtension\RawMinkContext;

class UtilsContext extends Context
{
    /**
     * @var array List of context parameters
     */
    protected $parameters;
    
    /**
     * @var RawMinkContext The mink context
     */
    protected $minkContext;
    
    /**
     * @var Session The browser session
     */
    protected $session;
    
    
    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        $this->parameters = $parameters;
    }
    
    /**
     * Initialize mink session
     *
     * @BeforeScenario
     */
    public function getMinkSession(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        $this->minkContext = $environment->getContext('Behat\MinkExtension\Context\RawMinkContext');
        $this->session = $this->minkContext->getSession();
    }
    
    /**
     * Take a screenshot on error
     *
     * @AfterStep
     */
    public function takeScreenshotOnError(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $filename = $scope->getSuite()->getName() . '-' . date('Y-m-d') . '.png';
            $filepath = isset($this->parameters['save_images']) : $this->parameters['save_images'] ? null;
            $this->minkContext->saveScreenshot();
        }
    }
    
    /**
     * Waiting an action
     *
     * @param closure $closure The function to execute for test the loading
     * @param int $wait The timeout in seconds
     * @param string $timeoutMsg The custom message on timeout
     */
    protected function spin($closure, $wait = 60, $timeoutMsg = 'Load timeout')
    {
        for ($i = 0; $i < $wait; $i++) {
            try {
                if ($closure($this)) {
                    return true;
                }
            } catch (\Exception $e) {}
            sleep(1);
        }
        throw new \Exception($timeoutMsg);
    }
    
    /**
     * Find an element on current page, if the element is not found throw an exception
     *
     * @param string $type The type for find
     * @param string $pattern The pattern for find
     * @param string $msg The exception message
     * @return Behat\Mink\Element\NodeElement The element
     */
    protected function findOrExcept($type, $pattern, $msg = 'Element not found.')
    {
        $page = $this->session->getPage();
        $element = $page->find($type, $path);
        if (is_null($element)) {
            throw \Exception($msg);
        }
        return $element;
    }
}