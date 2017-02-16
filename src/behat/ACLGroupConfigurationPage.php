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

class ACLGroupConfigurationPage implements ConfigurationPage
{
    const GROUP_TAB = 1;
    const AUTHORIZATION_TAB = 2;

    protected $context;

    private static $properties = array(
        'group_name' => array(
            self::GROUP_TAB,
            'text',
            'input[name="acl_group_name"]'
        ),
        'group_alias' => array(
            self::GROUP_TAB,
            'text',
            'input[name="acl_group_alias"]'
        ),
        'contacts' => array(
            self::GROUP_TAB,
            'advmultiselect',
            'select[name="cg_contacts-f[]"]'
        ),
        'contactgroups' => array(
            self::GROUP_TAB,
            'advmultiselect',
            'select[name="cg_contactGroups-f[]"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on a command configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param bool $visit True to navigate to configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50203&o=a');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="acl_group_name"]');
    }

    /**
     *  Get properties of the acl group.
     *
     *  @return Properties of the acl group.
     */
    public function getProperties()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException(__METHOD__);
    }

    /**
     *  Set properties of the acl group.
     *
     *  @param $properties  Properties of the acl group.
     */
    public function setProperties($properties)
    {
        // Begin with first tab.
        $tab = self::GROUP_TAB;
        $this->switchTab($tab);

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown acl group property ' . $property . '.');
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
