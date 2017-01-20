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

class TimeperiodsConfigurationPage implements ConfigurationPage
{
    const TAB_CONFIGURATION = 1;
    const TAB_EXCEPTION = 2;

    protected $context;

    private static $properties = array(
        // Configuration tab.
        'name' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_name"]'
        ),
        'alias' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_alias"]'
        ),
        'sunday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_sunday"]'
        ),
        'monday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_monday"]'
        ),
        'tuesday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_tuesday"]'
        ),
        'wednesday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_wednesday"]'
        ),
        'thursday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_thursday"]'
        ),
        'friday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_friday"]'
        ),
        'saturday' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="tp_saturday"]'
        ),
        'templates' => array(
            self::TAB_CONFIGURATION,
            'select2',
            'select#tp_include'
        ),
    );

    /**
     *  Navigate to and/or check that we are on a timeperiod configuration page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank timeperiods configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60304&o=c');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(function ($context) use ($mythis) {
            return $mythis->isPageValid();
        },
            5,
            'Current page does not match class ' . __CLASS__);
    }

    /**
     *  Check that the current page is matching this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="tp_name"]');
    }

    /**
     *  Get timeperiods properties.
     *
     * @return timeperiods properties.
     */
    public function getProperties()
    {
        // Begin with first tab.
        $tab = self::TAB_CONFIGURATION;
        $this->switchTab($tab);
        $properties = array();

        // Browse all properties.
        foreach (self::$properties as $property => $metadata) {
            // Set property meta-data in variables.
            $targetTab = $metadata[0];
            $propertyType = $metadata[1];
            $propertyLocator = $metadata[2];

            // Switch between tabs if required.
            if ($tab != $targetTab) {
                $this->switchTab($targetTab);
                $tab = $targetTab;
            }

            // Get properties.
            switch ($propertyType) {
                case 'select2':
                    $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break;
                case 'text':
                    $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while retrieving host properties.');
            }
        }
        return $properties;
    }

    /**
     *  Set timeperiods properties.
     *
     * @param $properties New timeperiods properties.
     */
    public function setProperties($properties)
    {
        // Begin with first tab.
        $tab = self::TAB_CONFIGURATION;
        $this->switchTab($tab);

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown host property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $targetTab = self::$properties[$property][0];
            $propertyType = self::$properties[$property][1];
            $propertyLocator = self::$properties[$property][2];

            // Switch between tabs if required.
            if ($tab != $targetTab) {
                $this->switchTab($targetTab);
                $tab = $targetTab;
            }

            // Set property with its value.
            switch ($propertyType) {
                case 'select2':
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                    break;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting host property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the current timeperiod configuration page.
     */
    public function save()
    {
        $button = $this->context->getSession()->getPage()->findButton('submitA');
        if (isset($button)) {
            $button->click();
        } else {
            $this->context->assertFindButton('submitC')->click();
        }
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
}
