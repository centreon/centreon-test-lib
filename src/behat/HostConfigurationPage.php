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

class HostConfigurationPage
{
    const CONFIGURATION_TAB = 1;
    const NOTIFICATION_TAB = 2;
    const RELATIONS_TAB = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    protected $context;

    private static $properties = array(
        // Configuration tab.
        'name' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_name"]'),
        'alias' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_alias"]'),
        'address' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_address"]'),
        'templates' => array(
            self::CONFIGURATION_TAB,
            'custom',
            'Templates'),
        'max_check_attempts' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_max_check_attempts"]'),
        'normal_check_interval' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_check_interval"]'),
        'retry_check_interval' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="host_retry_check_interval"]'),
        'active_checks_enabled' => array(
            self::CONFIGURATION_TAB,
            'radio',
            'input[name="host_active_checks_enabled[host_active_checks_enabled]"]'),
        'passive_checks_enabled' => array(
            self::CONFIGURATION_TAB,
            'radio',
            'input[name="host_passive_checks_enabled[host_passive_checks_enabled]"]'),
        // Notification tab.
        'notifications_enabled' => array(
            self::NOTIFICATION_TAB,
            'radio',
            'input[name="host_notifications_enabled[host_notifications_enabled]"]'),
        'notify_on_recovery' => array(
            self::NOTIFICATION_TAB,
            'checkbox',
            'input[name="host_notifOpts[r]"]'),
        'notify_on_down' => array(
            self::NOTIFICATION_TAB,
            'checkbox',
            'input[name="host_notifOpts[d]"]'),
        'notification_interval' => array(
            self::NOTIFICATION_TAB,
            'text',
            'input[name="host_notification_interval"]'),
        'notification_period' => array(
            self::NOTIFICATION_TAB,
            'select2',
            'select#timeperiod_tp_id2'),
        'first_notification_delay' => array(
            self::NOTIFICATION_TAB,
            'text',
            'input[name="host_first_notification_delay"]'),
        'recovery_notification_delay' => array(
            self::NOTIFICATION_TAB,
            'text',
            'input[name="host_recovery_notification_delay"]'),
        'cs' => array(
            self::NOTIFICATION_TAB,
            'select2',
            'select#host_cs'),
        // Data tab.
        'acknowledgement_timeout' => array(
            self::DATA_TAB,
            'text',
            'input[name="host_acknowledgement_timeout"]')
    );

    /**
     *  Navigate to and/or check that we are on a host configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60101&o=a');
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
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="host_address"]');
    }

    /**
     *  Get host properties.
     *
     *  @return Host properties.
     */
    public function getProperties()
    {
        // Begin with first tab.
        $tab = self::CONFIGURATION_TAB;
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
                throw new \Behat\Behat\Tester\Exception\PendingException(__FUNCTION__);
                break ;
            case 'select2':
                $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                break ;
            case 'text':
                $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                break ;
            default:
                throw new \Exception(
                    'Unknown property type ' . $propertyType
                    . ' found while retrieving host properties.');
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
        $tab = self::CONFIGURATION_TAB;
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
            case 'custom':
                $setter = 'set' . $propertyLocator;
                $this->$setter($value);
                break ;
            case 'checkbox':
            case 'radio':
                $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                break ;
            case 'select2':
                if (!is_array($value)) {
                    $value = array($value);
                }
                foreach ($value as $element) {
                    $this->context->selectToSelectTwo($propertyLocator, $element);
                }
                break ;
            case 'text':
                $this->context->assertFind('css', $propertyLocator)->setValue($value);
                break ;
            default:
                throw new \Exception(
                    'Unknown property type ' . $propertyType
                    . ' found while setting host property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the current host configuration page.
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

    /**
     *  Set host templates.
     *
     *  @param $templates  Parent templates.
     */
    private function setTemplates($templates)
    {
        if (!is_array($templates)) {
            $templates = array($templates);
        }
        $i = 0;
        foreach ($templates as $tpl) {
            $this->context->assertFind('css', '#template_add span')->click();
            $this->context->selectInList('#tpSelect_' . $i, $tpl);
            ++$i;
        }
    }
}
