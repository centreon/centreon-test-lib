<?php
/**
 * Copyright 2017 Centreon
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

class CommentConfigurationPage implements ConfigurationPage
{
    protected $context;

    /**
     *  Navigate to the comment creation page and/or check that it
     *  matches this class.
     *
     *  @param $context  Centreon context.
     *  @param $host     Host name. If empty, current page will not be
     *                   changed.
     *  @param $service  Service description. If empty, current page
     *                   will not be changed.
     */
    public function __construct($context, $host = '', $service = '')
    {
        // Visit page.
        $this->context = $context;
        if (!empty($host) && !empty($service)) {
            $this->context->visit('main.php?p=21002&o=as&host_name=' . $host . '&service_description=' . $service);
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
        return $this->context->getSession()->getPage()->has(
            'css',
            'textarea[name="comment"]'
        );
    }

    /**
     *  Get properties printed on the comment configuration page.
     *
     *  @return An array with detailed properties.
     */
    public function getProperties()
    {
        throw new \Exception('Not yet implemented.');
    }

    /**
     *  Set properties printed in the comment configuration page.
     *
     *  @param $properties  New comment properties.
     */
    public function setProperties($properties)
    {
        foreach ($properties as $property => $value) {
            switch ($property) {
                case 'comment':
                    $this->context->assertFind('css', 'textarea[name="comment"]')->setValue($value);
                    break ;
                default:
                    throw new \Exception(
                        'Unknown property ' . $property . '.'
                    );
            }
        }
    }

    /**
     *  Save form.
     */
    public function save()
    {
        $this->context->assertFindButton('submitA')->click();
    }
}
