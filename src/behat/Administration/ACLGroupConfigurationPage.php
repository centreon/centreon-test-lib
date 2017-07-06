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

class ACLGroupConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const GROUP_TAB = 1;
    const AUTHORIZATION_TAB = 2;

    protected $validField = 'input[name="acl_group_name"]';

    protected $properties = array(
        'group_name' => array(
            'input',
            'input[name="acl_group_name"]',
            self::GROUP_TAB
        ),
        'group_alias' => array(
            'input',
            'input[name="acl_group_alias"]',
            self::GROUP_TAB
        ),
        'contacts' => array(
            'advmultiselect',
            'cg_contacts',
            self::GROUP_TAB
        ),
        'contactgroups' => array(
            'advmultiselect',
            'cg_contactGroups',
            self::GROUP_TAB
        ),
        'status' => array(
            'input',
            'input[name="acl_group_activate[acl_group_activate]"]',
            self::GROUP_TAB
        ),
        'resources' => array(
            'advmultiselect',
            'resourceAccess',
            self::AUTHORIZATION_TAB
        ),
        'menu' => array(
            'advmultiselect',
            'menuAccess',
            self::AUTHORIZATION_TAB
        ),
        'actions' => array(
            'advmultiselect',
            'actionAccess',
            self::AUTHORIZATION_TAB
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
            $this->context->visit('main.php?p=50203&o=a');
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
