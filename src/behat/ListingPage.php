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
                $propertyLocator = $metadata[1];

                $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                switch ($propertyType) {
                    case 'text':
                        $entry[$property] = $component->getText();
                        break;
                    case 'attribute':
                        $entry[$property] = $component->getAttribute($metadata[2]);
                        break;
                    case 'custom':
                        $methodName = 'get' . $propertyLocator;
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
}

?>
