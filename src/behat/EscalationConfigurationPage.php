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

class EscalationConfigurationPage implements ConfigurationPage
{
    const TAB_UNKNOWN = 0;
    const TAB_INFORMATIONS = 1;
    const TAB_IMPACTED_RESOURCES = 2;

    private $context;

    private static $properties = array(
        // Configuration tab.
        'name' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="esc_name"]'),
        'alias' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="esc_alias"]'),
        'first_notification' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="first_notification"]'),
        'last_notification' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="last_notification"]'),
        'notification_interval' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="notification_interval"]'),
        'escalation_period' => array(
            self::TAB_INFORMATIONS,
            'select2',
            'select#escalation_period'),
        'host_notify_on_down' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options1[d]'),
        'host_notify_on_unreachable' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options1[u]'),
        'host_notify_on_recovery' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options1[r]'),
        'service_notify_on_warning' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options2[w]'),
        'service_notify_on_unknown' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options2[u]'),
        'service_notify_on_critical' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options2[c]'),
        'service_notify_on_recovery' => array(
            self::TAB_INFORMATIONS,
            'checkbox',
            'input[name="escalation_options2[r]'),
        'contactgroups' => array(
            self::TAB_INFORMATIONS,
            'select2',
            'select#esc_cgs'),
        'comment' => array(
            self::TAB_INFORMATIONS,
            'text',
            'input[name="esc_comment"]'),
        // Rescources tab.
        'host_inheritance_to_services' => array(
            self::TAB_IMPACTED_RESOURCES,
            'checkbox',
            'input[name="host_inheritance_to_services'),
        'hosts' => array(
            self::TAB_IMPACTED_RESOURCES,
            'select2',
            'select#esc_hosts'),
        'services' => array(
            self::TAB_IMPACTED_RESOURCES,
            'select2',
            'select#esc_hServices'),
        'hostgroup_inheritance_to_services' => array(
            self::TAB_IMPACTED_RESOURCES,
            'checkbox',
            'input[name="hostgroup_inheritance_to_services'),
        'hostgroups' => array(
            self::TAB_IMPACTED_RESOURCES,
            'select2',
            'select#esc_hgs'),
        'servicegroups' => array(
            self::TAB_IMPACTED_RESOURCES,
            'select2',
            'select#esc_sgs'),
        'metaservices' => array(
            self::TAB_IMPACTED_RESOURCES,
            'select2',
            'select#esc_metas')
    );

    /**
     *  Navigate to and/or check that we are on a escalation configuration
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
            $this->context->visit('main.php?p=60401&o=a');
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
     *  Check that the current page is valid for this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="esc_name"]');
    }

    /**
     *  Get host properties.
     *
     *  @return Host properties.
     */
    public function getProperties()
    {
        // Begin with first tab.
        $tab = self::TAB_INFORMATIONS;
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
            case 'checkbox':
            case 'select2':
            case 'text':
                $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                break ;
            case 'custom':
                $methodName = 'get' . $propertyLocator;
                $properties[$property] = $this->$methodName();
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
     *  Set host properties.
     *
     *  @param $properties  New host properties.
     */
    public function setProperties($properties)
    {
        // Begin with first tab.
        $tab = self::TAB_INFORMATIONS;
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
}

?>
