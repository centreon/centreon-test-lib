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

class ServiceDowntimeConfigurationPage implements ConfigurationPage
{

    const CONFIGURATION_TAB = 1;
    const RELATIONS_TAB = 2;

    protected $context;

    private static $properties = array(
        // configuration tab.
        'name' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="downtime_name"]'
        ),
        'alias' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="downtime_description"]'
        ),
        'periods' => array(
            self::CONFIGURATION_TAB,
            'checkbox',
            'input[name="periods[1][days][]"]'
        ),
        'start' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="periods[1][start_period]"]'
        ),
        'end' => array(
            self::CONFIGURATION_TAB,
            'text',
            'input[name="periods[1][end_period]"]'
        ),
        'host_relation' => array(
            self::RELATIONS_TAB,
            'select2',
            'select#host_relation'
        ),
        'hostgroup_relation' => array(
            self::RELATIONS_TAB,
            'select2',
            'select#hostgroup_relation'
        ),
        'svc_relation' => array(
            self::RELATIONS_TAB,
            'select2',
            'select#svc_relation'
        ),
        'svcgroup_relation' => array(
            self::RELATIONS_TAB,
            'select2',
            'select#svcgroup_relation'
        )
    );

    /**
     *  Service template edit page.
     *
     * @param $context  Centreon context object.
     * @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=21003&o=a');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            5,
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page matches this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input#start_period_1');
    }

    /**
     *  Get template properties.
     *
     * @return Service template properties.
     */
    public function getProperties()
    {
        $properties = array();
        $properties['description'] = $this->context->assertFindField('service_description')->getValue();
        $properties['alias'] = $this->context->assertFindField('service_alias')->getValue();
        return ($properties);
    }

    /**
     *  Set template properties. (For 1 period)
     *
     * @param $properties  Service template properties.
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
                    foreach ($value as $key => $val) {


                        if ($val) {
                            $this->context->assertFind(
                                'css',
                                $propertyLocator. '[value="' . $val . '"]'
                            )->check();
                        } else {
                            $this->context->assertFind(
                                'css',
                                $propertyLocator . '[value="' . $val . '"]'
                            )->uncheck();
                        }
                    }
                    break;
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break;
                case 'select2':
                    if (is_array($value)) {
                        foreach ($value as $element) {
                            $this->context->selectToSelectTwo($propertyLocator, $element);
                        }
                    } else {
                        $this->context->selectToSelectTwo($propertyLocator, $value);
                    }
                    break;
                case 'text':
                    $this->context->assertFind(
                        'css',
                        $propertyLocator
                    )->setValue($value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting service property ' . $property . '.'
                    );
            }
        }
    }

    /**
     *  Save service template.
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
     *  Save service template.
     */
    public function addPeriode()
    {
        $this->context->assertFind(
            'css',
            'table tbody tr td.FormRowValue div a  '
        )->click();
        $this->nbPeriod++;
    }

    /**
     *  Switch between tabs.
     *
     * @param $tab  Tab periode ID.
     */
    public function switchPeriode($tab)
    {
        $this->context->assertFind('css', 'ul#ul_tabs li:nth-child(' . $tab . ') a:nth-child(1)')->click();
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
