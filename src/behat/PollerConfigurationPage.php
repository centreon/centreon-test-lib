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

class PollerConfigurationPage implements ConfigurationPage
{
    static private $properties = array(
        'name' => array('text', 'input[name="name"]')
    );

    protected $context;

    /**
     *  Navigate to and/or edit a poller configuration.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to visit a new poller configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60901&o=a');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page matches this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="ns_ip_address"]');
    }

    /**
     *  Get poller properties.
     *
     *  @return An array of poller properties.
     */
    public function getProperties()
    {
        throw new \Exception(__METHOD__ . ' not yet implemented.');
    }

    /**
     *  Set poller properties.
     *
     *  @param $properties  Poller properties.
     */
    public function setProperties($properties)
    {
        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown poller property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break ;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType .
                        ' found while setting poller property ' . $property . '.'
                    );
            }
        }
    }

    /**
     *  Save configuration form.
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
}
