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
            $filename = date('Y-m-d-H-i') . '-' . $scope->getSuite()->getName() . '.png';
            $this->saveScreenshot($filename, $this->composeFiles['log_directory']);
        }
    }

    /**
     * Waiting an action
     *
     * @param closure $closure The function to execute for test the loading
     * @param int $wait The timeout in seconds
     * @param string $timeoutMsg The custom message on timeout
     */
    public function spin($closure, $wait = 30, $timeoutMsg = 'Load timeout')
    {
        $limit = time() + $wait;
        while (time() <= $limit) {
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
     * @param string $locate Input ID, name or label.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
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
     * @param string $locate Text of link.
     * @param string $msg The exception message. If empty, use a default message.
     * @return Behat\Mink\Element\NodeElement The element.
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
     *  @param $css_id  The ID of the select.
     *  @param $value   The requested value.
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
     *  Select an element in a select two.
     *
     *  @param $css_id  The id of the select two.
     *  @param $what    What to select.
     */
    public function selectToSelectTwo($css_id, $what)
    {
        $inputField = $this->assertFind('css', $css_id);
        $choice = $inputField->getParent()->find('css', '.select2-selection');
        if (!$choice) {
            throw new \Exception('No select2 choice found');
        }
        $choice->press();
        $select2Input = $this->getSession()->getDriver()->getWebDriverSession()->activeElement(
            'xpath',
            "//html/descendant-or-self::*[@class and contains(concat(' ', normalize-space(@class), ' '), ' select2-search__field ')]"
        );
        $select2Input->postValue(['value' => [$what]]);
        $this->spin(
            function ($context) {
                return count($context->getSession()->getPage()->findAll(
                    'css',
                    'li.select2-results__option:not(.loading-results):not(.select2-results__message)'
                )) != 0;
            },
            30
        );
        $chosenResults = $this->getSession()->getPage()->findAll(
            'css',
            'li.select2-results__option:not(.loading-results):not(.select2-results__message)'
        );
        foreach ($chosenResults as $result) {
            $html = $result->getHtml();
            if (preg_match('/>(.+)</', $html, $matches)) {
                if (isset($matches[1]) && $matches[1] == $what) {
                    $result->click();
                    break;
                }
            }
        }
    }

    /**
     * Visit a page
     *
     * @param string $page The url page to visit
     */
    public function visit($page)
    {
        $this->visitPath($page);
    }

    /**
     * Check a radio button on current page, if the radio button is not found throw an exception
     *
     * @param string $labelText The label of the radio button to search.
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return empty
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
}
