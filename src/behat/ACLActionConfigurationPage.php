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

class ACLActionConfigurationPage implements ConfigurationPage
{
    protected $context;

    private static $properties = array(
        'acl_name' => array(
            'text',
            'input[name="acl_action_name"]'
        ),
        'acl_alias' => array(
            'text',
            'input[name="acl_action_description"]'
        ),
        'acl_groups' => array(
            'advmultiselect',
            'select[name="acl_groups-f[]"]'
        ),
        'action_top_counter_overview' => array(
            'checkbox',
            'input[type="checkbox"][name="top_counter"]'
        ),
        'action_top_counter_poller' => array(
            'checkbox',
            'input[type="checkbox"][name="poller_stats"]'
        ),
        'action_poller_listing' => array(
            'checkbox',
            'input[type="checkbox"][name="poller_listing"]'
        ),
        'action_generate_configuration' => array(
            'checkbox',
            'input[type="checkbox"][name="generate_cfg"]'
        ),
        'action_generate_trap' => array(
            'checkbox',
            'input[type="checkbox"][name="generate_trap"]'
        ),
        'action_engine' => array(
            'checkbox',
            'input[type="checkbox"][name="all_engine"]'
        ),
        'action_service' => array(
            'checkbox',
            'input[type="checkbox"][name="all_service"]'
        ),
        'action_host' => array(
            'checkbox',
            'input[type="checkbox"][name="all_host"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on an acl page
     *
     *  @param $context  Centreon context.
     *  @param bool $visit True to navigate to configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50204&o=a');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="acl_action_name"]');
    }

    /**
     *  Get properties of the acl.
     *
     *  @return Properties of the acl.
     */
    public function getProperties()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException(__METHOD__);
    }

    /**
     *  Set properties of the acl.
     *
     *  @param $properties  Properties of the acl.
     */
    public function setProperties($properties)
    {
        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown action acl property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'advmultiselect':
                    $this->context->selectInAdvMultiSelect($propertyLocator, $value);
                    break ;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break ;
                case 'checkbox':
                    if ($value) {
                        $this->context->assertFind('css', $propertyLocator)->check();
                    } else {
                        $this->context->assertFind('css', $propertyLocator)->uncheck();
                    }
                    break ;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting acl property ' . $property . '.');
            }
        }
    }

    /*
     * Select all actions access
     */
    public function selectAll()
    {
        $properties = array();
        foreach (self::$properties as $name => $parameters) {
            if ($parameters[0] == 'checkbox') {
                $properties[$name] = true;
            }
        }
        $this->setProperties($properties);
    }

    /**
     *  Save form.
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
}
