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

namespace Centreon\Test\Behat\Configuration;

class MetaServiceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="meta_name"]';

    protected $properties = array(
        'name' => array(
            'text',
            'input[name="meta_name"]'
        ),
        'output_format' => array(
            'text',
            'input[name="meta_display"]'
        ),
        'warning_level' => array(
            'text',
            'input[name="warning"]'
        ),
        'critical_level' => array(
            'text',
            'input[name="critical"]'
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="meta_name"]');
    }
}
