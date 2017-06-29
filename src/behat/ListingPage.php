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

abstract class ListingPage implements \Centreon\Test\Behat\Interfaces\ListingPage
{
    protected $context;

    protected $validField;

    protected $lineSelector = '.list_one,.list_two';

    protected $properties = array();

    protected $objectClass;

    /**
     *  Check that the current page is valid for this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', $this->validField);
    }

    /**
     *  Get the list of objects.
     */
    public function getEntries()
    {
        $entries = array();

        $propertyTitle = '';

        $elements = $this->context->getSession()->getPage()->findAll('css', $this->lineSelector);
        foreach ($elements as $element) {
            if (!$this->validateEntry($element)) {
                continue;
            }

            $entry = array();
            foreach ($this->properties as $property => $metadata) {
                if (empty($propertyTitle)) {
                    $propertyTitle = $property;
                }

                // Set property meta-data in variables.
                $propertyType = $metadata[0];
                $propertyLocator = isset($metadata[1]) ? $metadata[1] : '';

                switch ($propertyType) {
                    case 'text':
                        $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                        $entry[$property] = $component->getText();
                        break;
                    case 'attribute':
                        if (is_null($propertyLocator) || empty($propertyLocator)) {
                            $component = $element;
                        } else {
                            $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                        }
                        $entry[$property] = $component->getAttribute($metadata[2]);
                        break;
                    case 'custom':
                        $methodName = 'get' . ucfirst($property);
                        $entry[$property] = $this->$methodName($element);
                        break;
                }
            }

            $entries[$entry[$propertyTitle]] = $entry;
        }

        return $entries;
    }

    /**
     * Validate entry integrity
     *
     * @param $element
     * @return bool
     */
    public function validateEntry($element)
    {
        return true;
    }

    /**
     * Get object info
     *
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function getEntry($name)
    {
        $objects = $this->getEntries();
        if (!array_key_exists($name, $objects)) {
            throw new \Exception('could not find object ' . $name);
        }
        return $objects[$name];
    }

    /**
     * Edit an object
     *
     * @param $name
     * @return mixed
     */
    public function inspect($name)
    {
        $this->context->assertFindLink($name)->click();

        if (isset($this->objectClass) && !is_null($this->objectClass)) {
            $objectClass = $this->objectClass;
            return new $objectClass($this->context, false);
        }

        return null;
    }
    
    /**
     * Click on the given object's checkbox and applies a choosen action
     * 
     * @param type $object The object to pass that will return the id
     * @param type $action The action to choose: Duplicate, Enable, Disable...
     * @throws \Exception
     */
    public function selectMoreAction($object, $action)
    {
        if (!empty($object) && !empty($action)) {
            $this->context->assertFind('css', 'input[type="checkbox"][name="select[' . $object['id'] . ']"]')->check();
            $this->context->setConfirmBox(true); 
            $this->context->selectInList('select[name="o1"]', $action);
        } else {
            throw new \Exception('some parameters are missing');
        }
    }
}
