<?php

/**
 * Copyright 2016-2021 Centreon
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
use Centreon\Test\Behat\SpinTrait;

class UtilsContext extends RawMinkContext
{
    use SpinTrait;

    const TIMEOUT_REACT = 3;

    /**
     * @var string Used to compare with the current iFrame page
     */
    protected static $lastUri;

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
        if ($bool == true) {
            $this->getSession()->getDriver()->executeScript('window.confirm = function(){return true;}');
        } else {
            $this->getSession()->getDriver()->executeScript('window.confirm = function(){return false;}');
        }
    }

    /**
     * Find an element on current page, if the element is not found throw an exception
     *
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     */
    public function assertFind($type, $pattern, $msg = '')
    {
        return $this->assertFindIn($this->getSession()->getPage(), $type, $pattern, $msg);
    }

    /**
     * Find an element in a parent element, if the element is not found throw an exception
     *
     * @param \Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param string $type The type for find.
     * @param string $pattern The pattern for find.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     */
    public function assertFindIn($parent, $type, $pattern, $msg = '')
    {
        $element = $parent->find($type, $pattern);
        if (is_null($element)) {
            if (empty($msg)) {
                throw new \Exception(
                    "Element was not found (type '$type', pattern '" . print_r($pattern, true) . "')."
                );
            } else {
                throw new \Exception($msg);
            }
        }
        return $element;
    }

    /**
     * Find a button on current page. If the button is not found, throw an exception.
     *
     * @param string $locator Button ID, value or alt.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     */
    public function assertFindButton($locator, $msg = '')
    {
        return $this->assertFindButtonIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a button in a prent element. If the button is not found, throw an exception.
     *
     * @param \Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param string $locator Button ID, value or alt.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     */
    public function assertFindButtonIn($parent, $locator, $msg = '')
    {
        $button = $parent->findButton($locator);
        if (is_null($button)) {
            if (empty($msg)) {
                throw new \Exception("Button '$locator' was not found.");
            } else {
                throw new \Exception($msg);
            }
        }
        return $button;
    }

    /**
     * Find a form field on current page. If the field is not found, throw an exception.
     *
     * @param string $locate Input ID, name or label.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     */
    public function assertFindField($locator, $msg = '')
    {
        return $this->assertFindFieldIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a form field on current page. If the field is not found, throw an exception.
     *
     * @param \Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param $locator
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     * @internal param string $locate Input ID, name or label.
     */
    public function assertFindFieldIn($parent, $locator, $msg = '')
    {
        $field = $parent->findField($locator);
        if (is_null($field)) {
            if (empty($msg)) {
                throw new \Exception("Field '$locator' was not found.");
            } else {
                throw new \Exception($msg);
            }
        }
        return $field;
    }

    /**
     * Find a link on current page. If the link is not found, throw an exception.
     *
     * @param string $locate Text of link.
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     */
    public function assertFindLink($locator, $msg = '')
    {
        return $this->assertFindLinkIn($this->getSession()->getPage(), $locator, $msg);
    }

    /**
     * Find a form link on current page. If the link is not found, throw an exception.
     *
     * @param \Behat\Mink\Element\NodeElement $parent Returned element will be a child of $parent.
     * @param $locator
     * @param string $msg The exception message. If empty, use a default message.
     * @return \Behat\Mink\Element\NodeElement The element.
     * @throws \Exception
     * @internal param string $locate Text of link.
     */
    public function assertFindLinkIn($parent, $locator, $msg = '')
    {
        $link = $parent->findLink($locator);
        if (is_null($link)) {
            if (empty($msg)) {
                throw new \Exception("Link '$locator' was not found.");
            } else {
                throw new \Exception($msg);
            }
        }
        return $link;
    }

    /**
     *  Select an element in list.
     *
     * @param $cssId  The ID of the select.
     * @param $value   The requested value.
     * @throws \Exception
     */
    public function selectInList($cssId, $value)
    {
        $found = false;
        $elements = $this->getSession()->getPage()->findAll('css', $cssId . ' option');
        foreach ($elements as $element) {
            if ($element->getText() == trim($value)) {
                $element->getParent()->selectOption($element->getValue());
                $found = true;
                break;
            }
        }
        if (!$found) {
            throw new \Exception('Could not find value ' . $value . ' in selection list ' . $cssId . '.');
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
                throw new \Exception('Could not find value ' . $value . ' in selection list ' . $css_id . '.');
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
                throw new \Exception('Could not find value ' . $value . ' in selection list ' . $css_id . '.');
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
     * @param $cssId
     * @param $what
     * @throws \Exception
     */
    public function selectToSelectTwo($cssId, $what)
    {
        $this->getSession()->evaluateScript(
            <<<JS
                (function() {
                    $('{$cssId}').select2('close');
                    //$('{$cssId}').select2('open');

                    // Get the search box within the dropdown or the selection
                    // Dropdown = single, Selection = multiple
                    let \$search = $('{$cssId}').data('select2').dropdown.\$search || $('{$cssId}').data('select2').selection.\$search;
                    // This is undocumented and may change in the future

                    \$search.val('{$what}');
                    \$search.trigger('input');

                    //$('{$cssId}').val(['{$what}']);
                    //$('{$cssId}').trigger('change');
                })();
            JS
        );

        /*
        // Open select2.
        $this->assertFind('css', $cssId)->getParent()->find('css', 'span.select2-selection')->click();

        $this->spin(
            function ($context) {
                return $context->assertFind('css', '.select2-container--open .select2-search__field')->isVisible();
            },
            'Cannot set select2 ' . $cssId . ' active'
        );

        // Set search.
        $this->getSession()->evaluateScript(
            'jQuery(".select2-container--open .select2-search__field").val(`' . $what . '`).trigger("keyup")'
        );
        */

        //sleep(3);

        $this->spin(
            function ($context) use ($what, $cssId) {
                if (
                    $context->getSession()->getPage()->has(
                        'css',
                        'span.select2-results '
                        . 'li.select2-results__option:not(.loading-results):not(.select2-results__message)'
                        . '[aria-selected="true"] > div[title="' . $what . '"]'
                    )
                ) {
                    $this->getSession()->evaluateScript(
                        <<<JS
                            (function() {
                                $('{$cssId}').select2('close');
                            })();
                        JS
                    );

                    return true;
                }
                //$select2Span = $context->assertFind('css', 'span.select2-results');
                $option = $context->assertFind(
                    'css',
                    'span.select2-results '
                    . 'li.select2-results__option:not(.loading-results):not(.select2-results__message)'
                    . '[aria-selected="false"] > div[title="' . $what . '"]'
                );

                $option->focus();

                $context->spin(
                    function ($context) {
                        return $context->getSession()->getPage()->has('css', 'li.select2-results__option--highlighted');
                    },
                    'select2 option ' . $what . ' is not focused',
                    5,
                );

                $context->getSession()->evaluateScript(
                    <<<JS
                        (function() {
                            /*
                            let e = $.Event("keypress");
                            e.which = 13;
                            e.keyCode = 13;
                            $('li.select2-results__option--highlighted > div').focus();
                            //$('li.select2-results__option--highlighted > div').trigger(e);
                            $('li.select2-results__option--highlighted > div').click();
                            $('{$cssId}').trigger('change');
                            */
                            $('.select2-results__option > div[title="{$what}"]').trigger('mouseup');
                            //$('{$cssId}').select2('close');
                        })();
                    JS
                );

                //$option->click();

                return true;
                /*
                $chosenResults = $select2Span->findAll(
                    'css',
                    'li.select2-results__option:not(.loading-results):not(.select2-results__message)[aria-selected="false"] > div[title="' . $what . '"]'
                );
                if (count($chosenResults) === 0) {
                    return false;
                }
                foreach ($chosenResults as $result) {
                    if (preg_match('/>(.+)</', $result->getHtml(), $matches) && $matches[1] == $what) {
                        $result->click();
                        $context->assertFind('css', $cssId)->blur();
                        return true;
                    }
                }
                return false;
                */
            },
            'Cannot find results in select2 ' . $cssId,
            10
        );

        $this->spin(
            function ($context) use ($cssId) {
                return $context->assertFind('css', $cssId)->getParent()
                    ->has('css', '.select2-container--open') === false;
            },
            'select2 ' . $cssId . ' is not closed'
        );
    }

    /**
     * Visit a page
     *
     * @param string $page The url page to visit
     * @param boolean $iframeCheck If it's an iframe
     */
    public function visit($page, $iframeCheck = true)
    {
        //checking if the page is an iFrame or not
        if ($iframeCheck && $page && $page != "/") {
            list($url, $parameters) = explode('?', $page);


            //checking if the same iFrame wasn't previously loaded
            // (ex : calling a second time the same form after saving it)
            if (self::$lastUri == $parameters) {
                //if so, then calling a new page to be sure that the iFrame is refreshed between two loads
                $this->visitPath("/");
                //then saving the called page to the static variable
            } elseif ($url == "index.php") {
                $iframeCheck = false;
                $parameters = $page;
            }
            self::$lastUri = $parameters;

        } else {
            //as page value is "/" (not an iFrame), $parameters is empty
            self::$lastUri = "";
        }
        $this->visitPath($page);
        if ($iframeCheck === true) {
            $this->switchToIframe();
        }
    }

    /**
     * Used to wait until the chosen iFrame is launched
     */
    public function switchToIframe()
    {
        try {
            $this->spin(
                function ($context) {
                    if ($context->getSession()->getPage()->has('css', "iframe#main-content")) {
                        // getting the current loaded iFrame URI
                        $uri = $this->getSession()->getPage()->find('css', "iframe#main-content")->getAttribute('src');
                        list($url, $parameters) = explode('?', $uri);

                        //getting the iFrame current height
                        $iframeHeight = $context->getSession()->evaluateScript(
                            "document.getElementById('main-content').clientHeight"
                        );

                        // we consider the iFrame was resized once its height is greater than 50px
                        // we also check if $lastUri is part of the iFrame URL ($parameters)
                        if (strstr($parameters, self::$lastUri) !== false && $iframeHeight > 50) {
                            $context->getSession()->getDriver()->switchToIFrame("main-content");
                            return true;
                        }
                    }
                    return false;
                },
                'this error will not be displayed',
                10
            );
        } catch (\Throwable $e) {
            // do not throw error cause it is possible that there is no iframe in the page
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
    public function checkRadioButton($labelText, $type, $pattern, $msg = '')
    {
        $page = $this->getSession()->getPage();

        $group = $page->find($type, $pattern);

        foreach ($group->findAll('css', 'label') as $label) {
            exec("echo '" . $label->getText() . "' 1>&2");
            if ($labelText === $label->getText()) {
                $radioButton = $page->find('css', '#' . $label->getAttribute('for'));

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
    public function checkRadioButtonByValue($value, $type, $pattern, $msg = '')
    {
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
