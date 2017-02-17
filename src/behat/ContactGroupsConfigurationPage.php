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

class ContactGroupsConfigurationPage implements ConfigurationPage
{
    protected $context;

    private static $properties = array(
        // Configuration tab.
        'name' => array(
            'text',
            'input[name="cg_name"]'
        ),
        'alias' => array(
            'text',
            'input[name="cg_alias"]'
        ),
        'contacts' => array(
            'select2',
            'select#cg_contacts'
        ),
        'acl' => array(
            'select2',
            'select#cg_acl_groups'
        ),
        'status' => array(
            'radio',
            'input[name="cg_activate[cg_activate]"]'
        ),
        'comments' => array(
            'text',
            'textarea[name="cg_comment"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on a contact configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param bool $visit True to navigate to a blank configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60302&o=a');
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
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="cg_name"]');
    }


    /**
     *  Get CG properties.
     *
     * @return CG properties.
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
                case 'checkbox':
                    break;
                case 'select2':
                    $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break;
                case 'text':
                    $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
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
     *  Set CG properties.
     *
     * @param $properties  New CG properties.
     */
    public function setProperties($properties)
    {

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown host property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'custom':
                    $setter = 'set' . $propertyLocator;
                    $this->$setter($value);
                    break;
                case 'checkbox':
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break;
                case 'select2':
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                    break;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting host property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the current contact group configuration page.
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
