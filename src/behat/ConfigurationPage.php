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

abstract class ConfigurationPage implements \Centreon\Test\Behat\Interfaces\ConfigurationPage
{
    protected $context;

    protected $validField;

    /*
    ** $properties should be an array of elements that can be retrieved (getProperties)
    ** and set (setProperties). This associative array associates a property name
    ** (string) to an array containing the property meta-data, composed of three
    ** elements in the following order: property type, property locator and tab ID.
    **
    ** Here is an example of a fake but properly constructed properties array.
    **
    ** protected $properties = array(
    **     'alias' => array(
    **         'input',
    **         'input[name="contact_alias"]',
    **         self::TAB_CONFIGURATION
    **     ),
    **     'host_notify_on_down' => array(
    **         'checkbox',
    **         'input[name="contact_hostNotifOpts[r]"]',
    **         self::TAB_NOTIFICATION
    **
    ** Available types are as follow.
    **
    **   - advmultiselect = advanced multi-select, specific to Centreon
    **   - checkbox       = input[type="checkbox"]
    **   - custom         = custom type, get$locator() and set$locator() methods
    **                      must be defined
    **   - input          = input, textarea, ...
    **   - radio          = input[type="radio"]
    **   - select         = select
    **   - select2        = select2
    **
    ** Proper field type is required for the getProperties() and setProperties()
    ** methods to work properly, as navigator control can be tricky.
    */
    protected $properties = array();

    /**
     *  Check that the current page is valid for this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', $this->validField);
    }

    /**
     * Get properties
     *
     * @return array
     * @throws \Exception
     */
    public function getProperties($properties = array())
    {
        if (empty($properties)) {
            $properties = array_keys($this->properties);
        }

        $values = array();
        foreach ($properties as $propertyName) {
            $values[$propertyName] = $this->getProperty($propertyName);
        }

        return $values;
    }

    /**
     * Set properties
     *
     * @param $properties
     * @throws \Exception
     */
    public function setProperties($properties)
    {
        $tab = '';

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, $this->properties)) {
                throw new \Exception('Unknown property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = $this->properties[$property][0];
            $propertyLocator = $this->properties[$property][1];

            // Switch between tabs if required.
            if (isset($this->properties[$property][2]) && !empty($this->properties[$property][2]) &&
                $tab != $this->properties[$property][2]
            ) {
                $this->switchTab($this->properties[$property][2]);
                $tab = $this->properties[$property][2];
            }

            // Set property with its value.
            switch ($propertyType) {
                case 'advmultiselect':
                    $object = $this->getProperty($property);
                    if (!empty($object)) {
                        $this->context->deleteInAdvMultiSelect('select[name="' . $propertyLocator . '-t[]"]', $object);
                    }
                    $this->context->selectInAdvMultiSelect('select[name="' . $propertyLocator . '-f[]"]', $value);
                    break;
                case 'checkbox':
                    $checkboxValue = $this->context->assertFind('css', $propertyLocator)->getValue();
                    var_dump('checkbox value : ' . $checkboxValue);
                    if (($value && !$checkboxValue) || (!$value && $checkboxValue)) {
                        $this->context->assertFind('css', $propertyLocator . '+ label')->click();
                    }
                    break;
                case 'custom':
                    $setter = 'set' . $propertyLocator;
                    $this->$setter($value);
                    break;
                case 'input':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                case 'radio':
                    try {
                        $this->context->assertFind(
                            'css',
                            $propertyLocator . '[value="' . $value . '"] + label'
                        )->click();
                    } catch (\Exception $e) {
                        $this->context->assertFind(
                            'css',
                            $propertyLocator . '[value="' . $value . '"]'
                        )->click();
                    }
                    break;
                case 'select':
                    $this->context->selectInList($propertyLocator, $value);
                    break;
                case 'select2':
                    $value = is_array($value) ? $value : array($value);
                    $this->context->emptySelectTwo($propertyLocator);
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting property ' . $property . '.'
                    );
            }
        }
    }

    /**
     * Get property
     *
     * @return string
     * @throws \Exception
     */
    public function getProperty($propertyName)
    {
        if (!isset($this->properties[$propertyName])) {
            throw new \Exception('Unknow property name : ' . $propertyName);
        }

        $metadata = $this->properties[$propertyName];
        $tab = '';

        // Set property meta-data in variables.
        $propertyType = $metadata[0];
        $propertyLocator = $metadata[1];
        $mandatory = isset($metadata[3]) ? $metadata[3] : true;

        // Switch between tabs if required.
        if (isset($metadata[2]) && !empty($metadata[2]) && $tab != $metadata[2]) {
            $this->switchTab($metadata[2]);
            $tab = $metadata[2];
        }

        try {
            switch ($propertyType) {
                case 'checkbox':
                case 'input':
                case 'radio':
                    $property = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break;
                case 'custom':
                    $methodName = 'get' . $propertyLocator;
                    $property = $this->$methodName();
                    break;
                case 'advmultiselect':
                    $options = $this->context->getSession()->getPage()->findAll(
                        'css',
                        'select[name="' . $propertyLocator . '-t[]"] option'
                    );
                    $property = array_filter(
                        array_map(
                            function ($option) {
                                return trim($option->getText());
                            },
                            $options
                        ),
                        function ($option) {
                            return !empty($option);
                        }
                    );
                    break;
                case 'select':
                    $selector = $this->context->getSession()->getPage()->find('css', $propertyLocator . ' option:selected');
                    if (isset($selector)) {
                        $property = $selector->getText();
                    } else {
                        $property = "";
                    }
                    break;
                case 'select2':
                    $property = $this->context->assertFind('css', $propertyLocator)->getText();
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while retrieving properties.'
                    );
            }
        } catch (\Exception $e) {
            if ($mandatory) {
                throw new \Exception($e);
            } else {
                $property = '';
            }
        }

        // Check if there is not ajax query, to ensure all macros are loaded
        $this->context->getSession()->wait(2000, '(0 === jQuery.active)');

        return $property;
    }

    /**
     *  Switch between tabs.
     *
     * @param $tab  Tab ID.
     */
    public function switchTab($tab)
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
    }

    /**
     *  Save the current configuration page.
     */
    public function save()
    {
        $button = $this->context->getSession()->getPage()->findButton('submitA');
        if (isset($button)) {
            $button->click();
        } else {
            $button = $this->context->getSession()->getPage()->findButton('submitC');
            if (isset($button)) {
                $button->click();
            } else {
                $this->context->assertFindButton('submitMC')->click();
            }
        }

        if (isset($this->listingClass)) {
            $listingClass = $this->listingClass;
            return new $listingClass($this->context, false);
        }
    }
}
