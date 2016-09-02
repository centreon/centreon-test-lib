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

class MetaServiceConfigurationPage
{

    protected $context;

    private static $properties = array(

        'name' => array(
            'text',
            'input[name="meta_name"]'
        ),
        'check_period' => array(
            'select2',
            'select#check_period'
        ),
        'max_check_attempts' => array(
            'text',
            'input[name="max_check_attempts"]'
        ),
        'normal_check_interval' => array(
            'text',
            'input[name="normal_check_interval"]'
        ),
        'retry_check_interval' => array(
            'text',
            'input[name="retry_check_interval"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on a service configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank service
     *                   configuration page.
     */

    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60204&o=a');
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="meta_name"]');
    }

    /**
     *  Get properties of the service.
     *
     * @return Properties of the service.
     */
    public function getProperties()
    {
        $properties = array();

        // Browse all properties.
        foreach (self::$properties as $property => $metadata) {

            // Set property meta-data in variables.
            $propertyType = $metadata[0];
            $propertyLocator = $metadata[1];

            // Get properties.
            switch ($propertyType) {
                case 'radio':
                    throw new \Behat\Behat\Tester\Exception\PendingException(__METHOD__);
                case 'select2':
                    $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                    break;
                case 'text':
                    $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                    break;
            }
        }
        return $properties;
    }

    /**
     *  Set properties of the service.
     *
     * @param $properties  Properties to set.
     */
    public function setProperties($properties)
    {
        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown service property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'checkbox':
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
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
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
        } else {
            $this->context->assertFindButton('submitC')->click();
        }
    }

}
