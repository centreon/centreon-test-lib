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

namespace Centreon\Test\Behat\Administration;

class ACLMenuConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="acl_topo_name"]';

    protected $properties = array(
        'acl_name' => array(
            'input',
            'input[name="acl_topo_name"]'
        ),
        'acl_alias' => array(
            'input',
            'input[name="acl_topo_alias"]'
        ),
        'acl_groups' => array(
            'advmultiselect',
            'acl_groups'
        ),
        'menu_home' => array(
            'checkbox',
            'input[type="checkbox"][id="i0"]'
        ),
        'menu_monitoring' => array(
            'checkbox',
            'input[type="checkbox"][id="i1"]'
        ),
        'menu_reporting' => array(
            'checkbox',
            'input[type="checkbox"][id="i2"]'
        ),
        'menu_configuration' => array(
            'checkbox',
            'input[type="checkbox"][id="i3"]'
        ),
        'menu_administration' => array(
            'checkbox',
            'input[type="checkbox"][id="i4"]'
        ),
    );

    /**
     *  Navigate to and/or check that we are on an acl page
     *
     *  @param $context  Centreon context.
     *  @param bool $visit True to navigate to configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50201&o=a');
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

    /*
     * Select all menu access
     */
    public function selectAll()
    {
        $properties = array();
        foreach ($this->properties as $name => $parameters) {
            if ($parameters[0] == 'checkbox') {
                $properties[$name] = true;
            }
        }
        $this->setProperties($properties);
    }
}
