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

class ServiceDowntimeConfigurationPage
{
    protected $context;

    private static $properties = array(
        'host' => array('select', 'select[name="host_id"]'),
        'service' => array('select', 'select[name="service_id"]'),
        'fixed' => array('checkbox', 'input[name="persistant"]'),
        'duration' => array('text', 'input[name="duration"]'),
        'duration_unit' => array('select', 'input[name="duration_scale"]'),
        'start_day' => array('text', 'input[name="start"]'),
        'start_time' => array('text', 'input[name="start_time"]'),
        'end_day' => array('text', 'input[name="end"]'),
        'end_time' => array('text', 'input[name="end_time"]'),
        'comment' => array('text', 'textarea[name="comment"]')
    );

    /**
     *  Navigate to and/or check that we are on a service downtime
     *  configuration page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank service downtime
     *                   configuration page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context= $context;
        if ($visit) {
            $this->context->visit('main.php?p=21001&o=as');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="start"]');
    }

    /**
     *  Set downtime properties.
     *
     *  @param $properties  Set downtime properties.
     */
    public function setProperties($properties)
    {
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown downtime property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
            case 'checkbox':
                $this->context->assertFind('css', $propertyLocator)->check();
                break ;
            case 'select':
                $this->context->selectInList($propertyLocator, $value);
                break ;
            case 'text':
                $this->context->assertFind('css', $propertyLocator)->setValue($value);
                break ;
            default:
                throw new \Exception(
                    'Unknown property type ' . $propertyType
                    . ' found while setting downtime property '
                    . $property . '.');
            }
        }
    }

    /**
     *  Save.
     */
    public function save()
    {
        $this->context->assertFindButton('submitA')->click();
    }
}
