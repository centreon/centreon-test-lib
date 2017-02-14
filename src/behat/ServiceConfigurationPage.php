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

class ServiceConfigurationPage implements ConfigurationPage
{
    const GENERAL_TAB = 1;
    const NOTIFICATIONS_TAB = 2;
    const RELATIONS_TAB = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    protected $context;

    private static $properties = array(
        // General tab.
        'hosts' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'select2',
            'select#service_hPars'),
        'description' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'text',
            'input[name="service_description"]'),
        'templates' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'select2',
            'select#service_template_model_stm_id'),
        'check_command' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'select2',
            'select#command_command_id'),
        'check_period' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'select2',
            'select#timeperiod_tp_id'),
        'max_check_attempts' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'text',
            'input[name="service_max_check_attempts"]'),
        'normal_check_interval' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'text',
            'input[name="service_normal_check_interval"]'),
        'retry_check_interval' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'text',
            'input[name="service_retry_check_interval"]'),
        'active_checks_enabled' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'radio',
            'input[name="service_active_checks_enabled[service_active_checks_enabled]"]'),
        'passive_checks_enabled' => array(
            ServiceConfigurationPage::GENERAL_TAB,
            'radio',
            'input[name="service_passive_checks_enabled[service_passive_checks_enabled]"]'),
        // Notifications tab.
        'notifications_enabled' => array(
            self::NOTIFICATIONS_TAB,
            'radio',
            'input[name="service_notifications_enabled[service_notifications_enabled]"]'),
        'notification_interval' => array(
            self::NOTIFICATIONS_TAB,
            'text',
            'input[name="service_notification_interval"]'),
        'notification_period' => array(
            self::NOTIFICATIONS_TAB,
            'select2',
            'select#timeperiod_tp_id2'),
        'notify_on_recovery' => array(
            self::NOTIFICATIONS_TAB,
            'checkbox',
            'input[name="service_notifOpts[r]"]'),
        'notify_on_critical' => array(
            self::NOTIFICATIONS_TAB,
            'checkbox',
            'input[name="service_notifOpts[c]"]'),
        'first_notification_delay' => array(
            self::NOTIFICATIONS_TAB,
            'text',
            'input[name="service_first_notification_delay"]'),
        'recovery_notification_delay' => array(
            self::NOTIFICATIONS_TAB,
            'text',
            'input[name="service_recovery_notification_delay"]'),
        'cs' => array(
            self::NOTIFICATIONS_TAB,
            'select2',
            'select#service_cs'),
        // Data tab.
        'acknowledgement_timeout' => array(
            ServiceConfigurationPage::DATA_TAB,
            'text',
            'input[name="service_acknowledgement_timeout"]')
    );

    /**
     *  Navigate to and/or check that we are on a service configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank service
     *                   configuration page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60201&o=a');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="service_description"]');
    }

    /**
     *  Get properties of the service.
     *
     *  @return Properties of the service.
     */
    public function getProperties()
    {
        // Begin with first tab.
        $tab = self::GENERAL_TAB;
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
                throw new \Behat\Behat\Tester\Exception\PendingException(__METHOD__);
            case 'select2':
                $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                break ;
            case 'text':
                $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                break ;
            }
        }
        return $properties;
    }

    /**
     *  Set properties of the service.
     *
     *  @param $properties  Properties to set.
     */
    public function setProperties($properties)
    {
        // Begin with first tab.
        $tab = self::GENERAL_TAB;
        $this->switchTab($tab);

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown service property ' . $property . '.');
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
            case 'checkbox':
            case 'radio':
                $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                break ;
            case 'select2':
                if (is_array($value)) {
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                }
                else {
                    $this->context->selectToSelectTwo($propertyLocator, $value);
                }
                break ;
            case 'text':
                $this->context->assertFind('css', $propertyLocator)->setValue($value);
                break ;
            default:
                throw new \Exception(
                    'Unknown property type ' . $propertyType
                    . ' found while setting service property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the service.
     */
    public function save()
    {
        $button = $this->context->getSession()->getPage()->findButton('submitA');
        if (isset($button)) {
            $button->click();
        }
        else {
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
