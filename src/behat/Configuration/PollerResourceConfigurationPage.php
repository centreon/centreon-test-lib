<?php
/**
 * Copyright 2005-2018 Centreon
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

use Centreon\Test\Behat\CentreonContext;

class PollerResourceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="resource_name"]';

    /**
     * @var array Properties used to locate fields in the form
     */
    protected $properties = array(
        'resource_name' => array(
            'input',
            'input[name="resource_name"]'
        ),
        'resource_line' => array(
            'input',
            'input[name="resource_line"]'
        ),
        'instance_id' => array(
            'select2',
            'select#instance_id'
        ),
        'resource_activate' => array(
            'radio',
            'input[name="resource_activate[resource_activate]"]'
        ),
        'resource_comment' => array(
            'input',
            'textarea[name="resource_comment"]'
        )
    );

    /**
     * Navigate to and/or edit a poller configuration.
     *
     * @param CentreonContext $context
     * @param bool $visit Set to true to visit a new poller resource configuration page.
     * @throws \Exception
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60904&o=a');
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
}
