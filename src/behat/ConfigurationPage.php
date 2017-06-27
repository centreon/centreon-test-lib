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

abstract class ConfigurationPage implements \Centreon\Test\Behat\Interfaces\ConfigurationPage
{
    protected $context;

    protected $validField;

    protected $properties = array();

    /**
     *  Check that the current page is valid for this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', $this->validField);
    }

    public function getProperties($propertiesList = array())
    {
        $properties = array();
        
        if (isset($propertiesList) && !empty($propertiesList)) {           
            foreach ($propertiesList as $property ) {  
            $properties[$property] = $this->getProperty($property);
         }
        
        return $properties;
        } else {
              
        $tab = '';
        // Browse all properties.
        foreach ($this->properties as $property => $metadata) {
            // Set property meta-data in variables.
            $propertyType = $metadata[0];
            $propertyLocator = $metadata[1];
            $mandatory = isset($metadata[3]) ? $metadata[3] : true;
            // Switch between tabs if required.
            if (isset($metadata[2]) && !empty($metadata[2]) && $tab != $metadata[2]) {
                $this->switchTab($metadata[2]);
                $tab = $metadata[2];
            }
            try {
                // Get properties.
                switch ($propertyType) {
                    case 'radio':
                    case 'checkbox':
                    case 'select':
                    case 'text':
                        $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                        break;
                    case 'select2':
                        $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getText();
                        break;
                    case 'custom':
                        $methodName = 'get' . $propertyLocator;
                        $properties[$property] = $this->$methodName();
                        break;
                    default:
                        throw new \Exception(
                            'Unknown property type ' . $propertyType
                            . ' found while retrieving host properties.'
                        );
                }
            } catch (\Exception $e) {
                if ($mandatory) {
                    throw new \Exception($e);
                }
            }
        }
        return $properties;
        }
    }

    /**
     * Set properties
     *
     * @param $properties
     * @throws \Exception
     */
    public function setProperties($properties)
    {
        $tab = '';

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, $this->properties)) {
                throw new \Exception('Unknown host property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = $this->properties[$property][0];
            $propertyLocator = $this->properties[$property][1];

            // Switch between tabs if required.
            if (isset($this->properties[$property][2]) && !empty($this->properties[$property][2]) &&
                $tab != $this->properties[$property][2]) {
                $this->switchTab($this->properties[$property][2]);
                $tab = $this->properties[$property][2];
            }

            // Set property with its value.
            switch ($propertyType) {
                case 'custom':
                    $setter = 'set' . $propertyLocator;
                    $this->$setter($value);
                    break;
                case 'checkbox':
                    if ($value) {
                        $this->context->assertFind('css', $propertyLocator)->check();
                    } else {
                        $this->context->assertFind('css', $propertyLocator)->uncheck();
                    }
                    break;
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break;
                case 'select':
                    $this->context->selectInList($propertyLocator, $value);
                    break;
                case 'select2':
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                    break;
                case 'advmultiselect':
                    $this->context->selectInAdvMultiSelect($propertyLocator, $value);
                    break;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting host property ' . $property . '.'
                    );
            }
        }
    }
    
    public function getProperty($propertyName) 
    {
        $metadata = $this->properties[$propertyName];
        $tab = '';
         
        // Set property meta-data in variables.
        $propertyType = $metadata[0];
        $propertyLocator = $metadata[1];
        $mandatory = isset($metadata[3]) ? $metadata[3] : true;
        // Switch between tabs if required.
        if (isset($metadata[2]) && !empty($metadata[2]) && $tab != $metadata[2]) {
            $this->switchTab($metadata[2]);
            $tab = $metadata[2];
        }
            try {
                
                switch ($propertyType) {
                    case 'radio':
                    case 'checkbox':
                    case 'select':
                    case 'select2':
                    case 'text':
                       $property = $this->context->assertFind('css', $propertyLocator)->getValue();
                        break;
                    case 'custom':
                        $methodName = 'get' . $propertyLocator;
                        $property = $this->$methodName();
                        break;
                    default:
                        throw new \Exception(
                            'Unknown property type ' . $propertyType
                            . ' found while retrieving host properties.'
                        );
                    }
                } catch (\Exception $e) {
                    if ($mandatory) {
                        throw new \Exception($e);
                    }
                }
   
        return $property;
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
        
        if (isset($this->listingClass)) {
            $listingClass = $this->listingClass;
            return new $listingClass($this->context, false);
        }
    }
}
