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
     * @var array List of container Compose files.
     */
    protected $composeFiles;

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
     *  Set the value returned by the confirmbox.
     */
    public function setConfirmBox($bool)
    {
        if ($bool == true)
            $this->getSession()->getDriver()->executeScript('window.confirm = function(){return true;}');
        else
            $this->getSession()->getDriver()->executeScript('window.confirm = function(){return false;}');
    }

    /**
     * Take a screenshot on error
     *
     * @AfterStep
     */
    public function takeScreenshotOnError(AfterStepScope $scope)
    {
        if (!$scope->getTestResult()->isPassed()) {
            $scenario = 'unknown';

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
     * Waiting an action
     *
     * @param closure $closure The function to execute for test the loading.
     * @param string $timeoutMsg The custom message on timeout.
     * @param int $wait The timeout in seconds.
     * @return bool
     * @throws \Exception
     */
    public function spin($closure, $timeoutMsg = 'Load timeout', $wait = 60)
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
     * Find an element on current page, if the element is not found throw an exception
     *
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     */
    public function assertFind($type, $pattern, $msg = '')
    {
        return $this->assertFindIn($this->getSession()->getPage(), $type, $pattern, $msg);
    }

    /**
     * Find an element in a parent element, if the element is not found throw an exception
     *
     * @param Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     */
    public function assertFindIn($parent, $type, $pattern, $msg = '')
    {
        $element = $parent->find($type, $pattern);
        if (is_null($element)) {
            if (empty($msg))
                throw new \Exception("Element was not found (type '$type', pattern '" . print_r($pattern, TRUE) . "').");
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
    public function assertFindButton($locator, $msg = '')
    {
        return $this->assertFindButtonIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a button in a prent element. If the button is not found, throw an exception.
     *
     * @param Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param string $locator Button ID, value or alt.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     */
    public function assertFindButtonIn($parent, $locator, $msg = '')
    {
        $button = $parent->findButton($locator);
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
    public function assertFindField($locator, $msg = '')
    {
        return $this->assertFindFieldIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a form field on current page. If the field is not found, throw an exception.
     *
     * @param Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param $locator
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     * @internal param string $locate Input ID, name or label.
     */
    public function assertFindFieldIn($parent, $locator, $msg = '')
    {
        $field = $parent->findField($locator);
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
     * Find a link on current page. If the link is not found, throw an exception.
     *
     * @param string $locate Text of link.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     */
    public function assertFindLink($locator, $msg = '')
    {
        return $this->assertFindLinkIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a form link on current page. If the link is not found, throw an exception.
     *
     * @param Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param $locator
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     * @internal param string $locate Text of link.
     */
    public function assertFindLinkIn($parent, $locator, $msg = '')
    {
        $link = $parent->findLink($locator);
        if (is_null($link))
            {
                if (empty($msg))
                    throw new \Exception("Link '$locator' was not found.");
                else
                    throw new \Exception($msg);
            }
        return $link;
    }

    /**
     *  Select an element in list.
     *
     * @param $css_id  The ID of the select.
     * @param $value   The requested value.
     * @throws \Exception
     */
    public function selectInList($css_id, $value)
    {
        $found = FALSE;
        $elements = $this->getSession()->getPage()->findAll('css', $css_id . ' option');
        foreach ($elements as $element) {
            if ($element->getText() == $value) {
                $element->click();
                $found = TRUE;
                break ;
            }
        }
        if (!$found) {
            throw new \Exception(
                'Could not find value ' . $value
                . ' in selection list ' . $css_id . '.');
        }
    }

    /**
     * Select an element in advMultiSelect.
     *
     * @param $css_id
     * @param array $values
     * @throws \Exception
     */
    public function selectInAdvMultiSelect($css_id, $values = array())
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $elements = $this->getSession()->getPage()->findAll('xpath', '//tr[td/select]');
        $options = null;
        $addButton = null;
        foreach ($elements as $element) {
            if ($element->has('css', $css_id) && $element->has('css', 'input[type="button"][value="Add"]')) {
                $options = $element->findAll('css', $css_id . ' option');
                $addButton = $this->assertFindIn($element, 'css', 'input[type="button"][value="Add"]');
            }
        }
        if (is_null($options) || is_null($addButton)) {
            throw new \Exception('Cannot find advmultiselect ' . $css_id);
        }

        foreach ($values as $value) {
            $found = false;

            foreach ($options as $option) {
                if ($option->getText() == $value) {
                    $option->click();
                    $addButton->click();
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new \Exception(
                    'Could not find value ' . $value
                    . ' in selection list ' . $css_id . '.');
            }
        }
    }

    /**
     * Delete an element in advMultiSelect.
     *
     * @param $css_id
     * @param array $values
     * @throws \Exception
     */
    public function deleteInAdvMultiSelect($css_id, $values = array())
    {
        if (!is_array($values)) {
            $values = array($values);
        }

        $elements = $this->getSession()->getPage()->findAll('xpath', '//tr[td/select]');
        $options = null;
        $addButton = null;
        foreach ($elements as $element) {
            if ($element->has('css', $css_id) && $element->has('css', 'input[type="button"][value="Remove"]')) {
                $options = $element->findAll('css', $css_id . ' option');
                $addButton = $this->assertFindIn($element, 'css', 'input[type="button"][value="Remove"]');
            }
        }
        if (is_null($options) || is_null($addButton)) {
            throw new \Exception('Cannot find advmultiselect ' . $css_id);
        }

        foreach ($values as $value) {
            $found = false;

            foreach ($options as $option) {
                if ($option->getText() == $value) {
                    $option->click();
                    $addButton->click();
                    $found = true;
                    break;
                }
            }

            if (!$found) {
                throw new \Exception(
                    'Could not find value ' . $value
                    . ' in selection list ' . $css_id . '.');
            }
        }
    }

    /**
     * Empty select2 values
     *
     * @param $cssId the css locator
     */
    public function emptySelectTwo($cssId)
    {
        $object = $this->assertFind('css', $cssId);
        $parent = $object->getParent();
        $this->assertFindIn($parent, 'css', '.clearAllSelect2')->click();
    }

    /**
     * Select an element in a select two.
     *
     * @param $css_id
     * @param $what
     * @throws \Exception
     */
    public function selectToSelectTwo($css_id, $what)
    {
        // Open select2.
        $selectDiv = $this->assertFind('css', $css_id)->getParent();
        $this->assertFindIn($selectDiv, 'css', 'span.select2-selection')->click();
        $this->spin(
            function ($context) {
                return $context->assertFind('css', '.select2-container--open .select2-search__field')->isVisible();
            },
            'Cannot set select2 ' . $css_id . ' active'
        );
        sleep(1);
        $select2Input = $this->getSession()->getDriver()->getWebDriverSession()->activeElement();

        // Set search.
        $select2Input->clear();
        $select2Input->postValue(['value' => [$what]]);

        $chosenResults = array();
        $this->spin(
            function ($context) use ($css_id, &$chosenResults) {
                $chosenResults = $context->getSession()->getPage()->findAll(
                    'css',
                    'li.select2-results__option:not(.loading-results):not(.select2-results__message)'
                );
                return count($chosenResults) != 0;
            },
            'Cannot find results in select2 ' . $css_id,
            10
        );

        foreach ($chosenResults as $result) {
            $found = false;
            $this->spin(
                function ($context) use ($result, $what, $css_id, &$found) {
                    $html = $result->getHtml();
                    if (preg_match('/>(.+)</', $html, $matches)) {
                        if ($matches[1] == $what) {
                            $result->click();
                            $found = true;
                        }
                    }
                    return true;
                },
                'Cannot select "' . $what .  '" in select2 "' . $css_id . '"',
                3
            );
            if ($found) {
                break;
            }
        }

        // Click parent element to close select2 search field if select2 is not auto closed
        if ($this->getSession()->getPage()->has('css','.select2-container--open .select2-search__field')) {
            $this->assertFindIn($selectDiv, 'css', 'span.select2-selection')->click();
        }
        // Wait select2 search field is totally closed
        $this->spin(
            function ($context) {
                return !$context->getSession()->getPage()->has(
                    'css',
                    '.select2-container--open .select2-search__field'
                );
            },
            'select2 ' . $css_id . ' search field is not closed',
            10
        );
    }

    /**
     * Visit a page
     *
     * @param string $page The url page to visit
     */
    public function visit($page, $iframeCheck = true)
    {
        $this->visitPath($page);

        if ($iframeCheck === true) {
            try {
                $this->spin(
                    function ($context) {
                        if ($context->getSession()->getPage()->has('css', "iframe#main-content")) {
                            $iframeHeight = $context->getSession()->evaluateScript(
                                "document.getElementById('main-content').clientHeight"
                            );

                            // we consider iframe hase been resized once its height is superior than 50px
                            if ($iframeHeight > 50) {
                                $context->getSession()->getDriver()->switchToIFrame("main-content");
                            }
                            return true;
                        }
                        return false;
                    },
                    'this error will no be displayed',
                    3
                );
            } catch (\Exception $e) {
                // do not throw error cause it is possible than there is no iframe in the page
            }
        }
    }

    /**
     * Check a radio button on current page, if the radio button is not found throw an exception
     *
     * @param string $labelText The label of the radio button to search.
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return empty
     * @throws \Exception
     */
    public function checkRadioButton($labelText, $type, $pattern, $msg = '') {
        $page = $this->getSession()->getPage();

        $group = $page->find($type, $pattern);

        foreach ($group->findAll('css', 'label') as $label) {
            exec("echo '" . $label->getText() . "' 1>&2");
            if ($labelText === $label->getText()) {
                $radioButton = $page->find('css', '#'.$label->getAttribute('for'));

                // Select the radio button
                $radioButton->click();
                return;
            }
        }

        if (empty($msg)) {
            throw new \Exception("Radio button with label {$labelText} not found");
        } else {
            throw new \Exception($msg);
        }
    }

    /**
     * Check a radio button on current page, if the radio button is not found throw an exception
     *
     * @param $value The value of the radio button to search.
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return empty
     * @throws \Exception
     */
    public function checkRadioButtonByValue($value, $type, $pattern, $msg = '') {
        $page = $this->getSession()->getPage();

        $group = $page->findAll($type, $pattern);
        foreach ($group as $button) {
            if ($value === $button->getAttribute('value')) {
                // Select the radio button
                $button->click();
                return;
            }

        }

        if (empty($msg)) {
            throw new \Exception("Radio button with value {$value} not found");
        } else {
            throw new \Exception($msg);
        }
    }

    /**
     *  Properly set WebDriver driver.
     */
    public function setContainerWebDriver()
    {
        // Wait for WebDriver container.
        $url = 'http://' . $this->container->getHost() . ':4444/grid/api/hub';
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        $res = curl_exec($ch);
        $limit = time() + 60;
        while ((time() < $limit) &&
               (($res === false) ||
               empty($res)) ||
               (json_decode($res, true)['slotCounts']['free'] < 1)) {
            sleep(1);
            $res = curl_exec($ch);
        }
        if (time() >= $limit) {
            throw new \Exception(
                'WebDriver did not respond within a 60 seconds time frame (url: ' . $url . ').'
            );
        }

        try {
            $url = 'http://' . $this->container->getHost() . ':4444/wd/hub';
            $driver = new \Behat\Mink\Driver\Selenium2Driver(
                'chrome',
                array(
                    'chrome' => array(
                        'args' => array(
                            '--window-size=1600,2000',
                            '--disable-sync',
                            '--single-process',
                            '--disable-remote-fonts',
                            '--disable-canvas-aa',
                            '--disable-lcd-text',
                            '--no-sandbox'
                        ),
                        /*
                        'prefs' => array(
                            'profile.default_content_setting_values.images' => 2
                        )
                        */
                    ),
                    'browserName' => 'chrome',
                    'platform' => 'ANY',
                    'browser' => 'chrome',
                    'name' => 'Behat Test'
                ),
                $url
            );
            $driver->setTimeouts(array(
                'page load' => 120000,
                'script' => 120000
            ));
        } catch (\Exception $e) {
            throw new \Exception("Cannot instantiate mink driver.\n" . $e->getMessage());
        }

        try {
            $sessionName = $this->getMink()->getDefaultSessionName();
            $session = new \Behat\Mink\Session($driver);
            $this->getMink()->registerSession($sessionName, $session);
        } catch (\Exception $e) {
            throw new \Exception("Cannot register mink session.\n" . $e->getMessage());
        }
    }
}
