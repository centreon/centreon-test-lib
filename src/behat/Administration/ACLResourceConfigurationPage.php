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

class ACLResourceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const GENERAL_TAB = 1;
    const HOST_TAB = 2;
    const SERVICE_TAB = 3;
    const META_TAB = 4;
    const FILTER_TAB = 5;

    protected $validField = 'input[name="acl_res_name"]';

    protected $properties = array(
        'acl_name' => array(
            'input',
            'input[name="acl_res_name"]',
            self::GENERAL_TAB
        ),
        'acl_alias' => array(
            'input',
            'input[name="acl_res_alias"]',
            self::GENERAL_TAB
        ),
        'acl_groups' => array(
            'advmultiselect',
            'select[name="acl_groups',
            self::GENERAL_TAB
        ),
        'all_hosts' => array(
            'checkbox',
            'input[type="checkbox"][id="all_hosts"]',
            self::HOST_TAB
        ),
        'all_hostgroups' => array(
            'checkbox',
            'input[type="checkbox"][id="all_hostgroups"]',
            self::HOST_TAB
        ),
        'all_servicegroups' => array(
            'checkbox',
            'input[type="checkbox"][id="all_servicegroups"]',
            self::SERVICE_TAB
        )
    );

    /**
     *  Navigate to and/or check that we are on a command configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param bool $visit True to navigate to configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50202&o=a');
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
            if ($parameters[1] == 'checkbox') {
                $properties[$name] = true;
            }
        }
        $this->setProperties($properties);
    }
}
