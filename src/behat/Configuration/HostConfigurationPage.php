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

class HostConfigurationPage extends HostTemplateConfigurationPage
{
    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\HostConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a host configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = true)
    {
        unset($this->properties['service_templates']);
        $this->properties['monitored_from'] = array(
            'select',
            'select[name="nagios_server_id"]',
            self::CONFIGURATION_TAB
        );
        $this->properties['service_linked_to_template'] = array(
            'radio',
            'input[name="dupSvTplAssoc[dupSvTplAssoc]"]',
            self::CONFIGURATION_TAB
        );
        $this->properties['poller'] = array(
            'select',
            'select[name="nagios_server_id"]',
            self::CONFIGURATION_TAB
        );
        $this->properties['parent_host_groups'] = array(
            'select2',
            'select#host_hgs',
            self::RELATIONS_TAB
        );
        $this->properties['parent_hosts'] = array(
            'select2',
            'select#host_parents',
            self::RELATIONS_TAB
        );
        $this->properties['child_hosts'] = array(
            'select2',
            'select#host_childs',
            self::RELATIONS_TAB
        );
        $this->properties['geo_coordinates'] = array(
            'input',
            'input[name="geo_coords"]',
            self::EXTENDED_TAB
        );

        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60101&o=a');
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
