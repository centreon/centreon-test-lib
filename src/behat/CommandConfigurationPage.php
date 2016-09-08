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

class CommandConfigurationPage
{
    const TYPE_CHECK = 2;
    const TYPE_NOTIFICATION = 1;
    const TYPE_DISCOVERY = 4;
    const TYPE_MISC = 3;

    protected $context;

    private static $properties = array(
        'command_name' => array(
            'text',
            'input[name="command_name"]'),
        'command_line' => array(
            'text',
            'textarea[name="command_line"]')
    );

    /**
     *  Navigate to and/or check that we are on a command configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank check command
     *                   configuration page.
     */
    public function __construct($context, $visit = TRUE, $type = 2)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60801&o=a&type=' . $type);
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="command_name"]');
    }

    /**
     *  Get properties of the command.
     *
     *  @return Properties of the command.
     */
    public function getProperties()
    {
        throw new \Behat\Behat\Tester\Exception\PendingException(__METHOD__);
    }

    /**
     *  Set properties of the command.
     *
     *  @param $properties  Properties of the command.
     */
    public function setProperties($properties)
    {
        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown command property ' . $property . '.');
            }

            // Set property.
            $propertyType = self::$properties[$property][0];
            switch ($propertyType) {
            case 'text':
                $this->context->assertFind('css', self::$properties[$property][1])->setValue($value);
                break ;
            default:
                throw new \Exception(
                    'Unknown property type ' . $propertyType . '.');
            }
        }
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
