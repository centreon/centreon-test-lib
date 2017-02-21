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

class BrokerConfigurationPage implements ConfigurationPage
{
    const TAB_GENERAL = 1;
    const TAB_INPUT = 2;
    const TAB_LOGGER = 3;
    const TAB_OUTPUT = 4;

    protected $context;

    private static $properties = array(
        // Configuration tab.
        'requester' => array(
            self::TAB_GENERAL,
            'select',
            'select[name="ns_nagios_server"]'),
        'name' => array(
            self::TAB_GENERAL,
            'text',
            'input[name="name"]'),
        'daemon' => array(
            self::TAB_GENERAL,
            'radio',
            'input[name="activate_watchdog[activate_watchdog]"]'),
        'filename' => array(
            self::TAB_GENERAL,
            'text',
            'input[name="filename"]'),
        'cache_directory' => array(
            self::TAB_GENERAL,
            'text',
            'input[name="cache_directory"]'),
        'status' => array(
            self::TAB_GENERAL,
            'radio',
            'input[name="activate[activate]"]'),
        'timestamp' => array(
            self::TAB_GENERAL,
            'radio',
            'input[name="write_timestamp[write_timestamp]"]'),
        'thread_id' => array(
            self::TAB_GENERAL,
            'radio',
            'input[name="write_thread_id[write_thread_id]"]'),
        'statistics' => array(
            self::TAB_GENERAL,
            'radio',
            'input[name="stats_activate[stats_activate]"]'),
        'event_queue_max_size' => array(
            self::TAB_GENERAL,
            'text',
            'input[name="event_queue_max_size"]'),
        'command_file' => array(
            self::TAB_GENERAL,
            'text',
            'input[name="command_file"]'),
    );

    /**
     * BrokerConfigurationPage constructor.
     * @param $context
     * @param bool $visit
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60909&o=a');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page is matching this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'select[name="ns_nagios_server"]');
    }

    /**
     * @return array
     * @throws \Exception
     */
    public function getProperties()
    {
        // Begin with first tab.
        $tab = self::TAB_GENERAL;
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
                case 'radio':
                case 'select':
                case 'text':
                    $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break ;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while retrieving broker properties.');
            }
        }

        return $properties;
    }

    /**
     *  Set host properties.
     *
     *  @param $properties  New host properties.
     */
    public function setProperties($properties)
    {
        // Begin with first tab.
        $tab = self::TAB_GENERAL;
        $this->switchTab($tab);

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown broker property ' . $property . '.');
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
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break ;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break ;
                case 'select':
                    $this->context->selectInList($propertyLocator, $value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting broker property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the current broker configuration page.
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
     *  @param $tab  Tab ID.
     */
    public function switchTab($tab)
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
    }
}
