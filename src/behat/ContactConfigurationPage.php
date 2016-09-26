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

class ContactConfigurationPage implements ConfigurationPage
{
    const TAB_CONFIGURATION = 1;
    const TAB_AUTHENTICATION = 2;
    const TAB_EXTENDED = 3;

    protected $context;

    private static $properties = array(
        // Configuration tab.
        'alias' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="contact_alias"]'),
        'name' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="contact_name"]'),
        'email' => array(
            self::TAB_CONFIGURATION,
            'text',
            'input[name="contact_email"]'),
        'location' => array(
            self::TAB_CONFIGURATION,
            'select2',
            'select#contact_location'),
        'notifications_enabled' => array(
            self::TAB_CONFIGURATION,
            'radio',
            'input[name="contact_enable_notifications[contact_enable_notifications]"]'),
        'host_notify_on_recovery' => array(
            self::TAB_CONFIGURATION,
            'checkbox',
            'input[name="contact_hostNotifOpts[r]"]'),
        'host_notify_on_down' => array(
            self::TAB_CONFIGURATION,
            'checkbox',
            'input[name="contact_hostNotifOpts[d]"]'),
        'host_notification_command' => array(
            self::TAB_CONFIGURATION,
            'select2',
            'select#contact_hostNotifCmds'),
        'service_notify_on_recovery' => array(
            self::TAB_CONFIGURATION,
            'checkbox',
            'input[name="contact_svNotifOpts[r]"]'),
        'service_notify_on_critical' => array(
            self::TAB_CONFIGURATION,
            'checkbox',
            'input[name="contact_svNotifOpts[c]"]'),
        'service_notification_command' => array(
            self::TAB_CONFIGURATION,
            'select2',
            'select#contact_svNotifCmds'),
    );

    /**
     *  Navigate to and/or check that we are on a contact configuration
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
            $this->context->visit('main.php?p=60301&o=a');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="contact_name"]');
    }

    /**
     *  Get host properties.
     *
     *  @return Host properties.
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
     *  Save the current contact configuration page.
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
