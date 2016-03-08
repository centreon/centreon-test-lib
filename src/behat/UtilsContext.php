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

use Behat\MinkExtension\Context\RawMinkContext;
use Behat\Behat\Hook\Scope\AfterStepScope;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

class UtilsContext extends RawMinkContext
{
    /**
     * @var array List of context parameters
     */
    protected $parameters;

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
     * Take a screenshot on error
     *
     * @AfterStep
     */
    public function takeScreenshotOnError(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $filename = $scope->getSuite()->getName() . '-' . date('Y-m-d') . '.png';
            $filepath = isset($this->parameters['save_images']) ? $this->parameters['save_images'] : null;
            $this->saveScreenshot($filename, $filepath);
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
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element
     */
    protected function assertFind($type, $pattern, $msg = '')
    {
        $page = $this->getSession()->getPage();
        $element = $page->find($type, $pattern);
        if (is_null($element)) {
            if (empty($msg))
                throw new \Exception("Element was not found (type '$type', pattern '$pattern').");
            else
                throw new \Exception($msg);
        }
        return $element;
    }

    /**
     * Find a button on current page. If the button is not found, throw an exception.
     *
     * @param string $locator Button ID, value or alt.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     */
    protected function assertFindButton($locator, $msg = '')
    {
        $button = $this->getSession()->getPage()->findButton($locator);
        if (is_null($button))
        {
            if (empty($msg))
                throw new \Exception("Button '$locator' was not found.");
            else
                throw new \Exception($msg);
        }
        return $button;
    }

    /**
     * Find a form field on current page. If the field is not found, throw an exception.
     *
     * @param string $locate Input ID, name or label.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     */
    protected function assertFindField($locator, $msg = '')
    {
        $field = $this->getSession()->getPage()->findField($locator);
        if (is_null($field))
        {
            if (empty($msg))
                throw new \Exception("Field '$locator' was not found.");
            else
                throw new \Exception($msg);
        }
        return $field;
    }

    /**
     * Visit a page
     *
     * @param string $page The url page to visit
     */
    protected function visit($page)
    {
        $this->visitPath($page);
    }
}