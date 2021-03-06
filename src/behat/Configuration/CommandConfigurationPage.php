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

class CommandConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TYPE_CHECK = 2;
    const TYPE_NOTIFICATION = 1;
    const TYPE_DISCOVERY = 4;
    const TYPE_MISC = 3;

    protected $validField = 'input[name="command_name"]';

    protected $properties = array(
        'command_name' => array(
            'input',
            'input[name="command_name"]'
        ),
        'command_type' => array(
            'radio',
            'input[name="command_type[command_type]"]'
        ),
        'command_line' => array(
            'input',
            'textarea[name="command_line"]'
        ),
        'enabled_shell' => array(
            'checkbox',
            'input[name="enable_shell"]'
        ),
        'argument_example' => array(
            'input',
            'input[name="command_example"]'
        ),
        'argument_description' => array(
            'input',
            'textarea[name="listOfArg"]'
        ),
        'macros_description' => array(
            'input',
            'textarea[name="listOfMacros"]'
        ),
        'connectors' => array(
            'select',
            'select[name="connectors"]'
        ),
        'graph_template' => array(
            'select',
            'select[name="graph_id"]'
        ),
        'enabled' => array(
            'radio',
            'input[name="command_activate[command_activate]"]'
        ),
        'comment' => array(
            'input',
            'textarea[name="command_comment"]'
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\CommandConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a command configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank check command
     *                   configuration page.
     * @param $type     Command type if visit is enabled. Default to
     *                   TYPE_CHECK.
     */
    public function __construct($context, $visit = true, $type = self::TYPE_CHECK)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60801&o=a&type=' . $type);
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
