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

class ACLActionConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="acl_action_name"]';

    protected $properties = array(
        'acl_name' => array(
            'input',
            'input[name="acl_action_name"]'
        ),
        'acl_alias' => array(
            'input',
            'input[name="acl_action_description"]'
        ),
        'acl_groups' => array(
            'advmultiselect',
            'acl_groups'
        ),
        //Global Functionalities Access
        'action_top_counter_overview' => array(
            'checkbox',
            'input[type="checkbox"][name="top_counter"]'
        ),
        'action_top_counter_poller' => array(
            'checkbox',
            'input[type="checkbox"][name="poller_stats"]'
        ),
        'action_poller_listing' => array(
            'checkbox',
            'input[type="checkbox"][name="poller_listing"]'
        ),
        //Configuration Actions
        'action_generate_configuration' => array(
            'checkbox',
            'input[type="checkbox"][name="generate_cfg"]'
        ),
        'action_generate_trap' => array(
            'checkbox',
            'input[type="checkbox"][name="generate_trap"]'
        ),
        //Global Monitoring Engine Actions
        'action_engine' => array(
            'checkbox',
            'input[type="checkbox"][name="all_engine"]'
        ),
        'action_shutdown' => array(
            'checkbox',
            'input[name="global_shutdown"]'
        ),
        'action_restart' => array(
            'checkbox',
            'input[name="global_restart"]'
        ),
        'action_notifications' => array(
            'checkbox',
            'input[name="global_notifications"]'
        ),
        'action_service_checks' => array(
            'checkbox',
            'input[name="global_service_checks"]'
        ),
        'action_service_passive_checks' => array(
            'checkbox',
            'input[name="global_service_passive_checks"]'
        ),
        'action_host_checks' => array(
            'checkbox',
            'input[name="global_host_checks"]'
        ),
        'action_host_passive_checks' => array(
            'checkbox',
            'input[name="global_host_passive_checks"]'
        ),
        'action_event_handler' => array(
            'checkbox',
            'input[name="global_event_handler"]'
        ),
        'action_flap_detection' => array(
            'checkbox',
            'input[name="global_flap_detection"]'
        ),
        'action_service_obsess' => array(
            'checkbox',
            'input[name="global_service_obsess"]'
        ),
        'action_host_obsess' => array(
            'checkbox',
            'input[name="global_host_obsess"]'
        ),
        'action_perf_data' => array(
            'checkbox',
            'input[name="global_perf_data"]'
        ),
        //Services Actions Access
        'action_service' => array(
            'checkbox',
            'input[type="checkbox"][name="all_service"]'
        ),
        'action_service_checks' => array(
            'checkbox',
            'input[name="service_checks"]'
        ),
        'action_service_notifications' => array(
            'checkbox',
            'input[name="service_notifications"]'
        ),
        'action_service_acknowledgement' => array(
            'checkbox',
            'input[name="service_acknowledgement"]'
        ),
        'action_service_disacknowledgement' => array(
            'checkbox',
            'input[name="service_disacknowledgement"]'
        ),
        'action_service_schedule_check' => array(
            'checkbox',
            'input[name="service_schedule_check"]'
        ),
        'action_service_schedule_forced_check' => array(
            'checkbox',
            'input[name="service_schedule_forced_check"]'
        ),
        'action_service_schedule_downtime' => array(
            'checkbox',
            'input[name="service_schedule_downtime"]'
        ),
        'action_service_comment' => array(
            'checkbox',
            'input[name="service_comment"]'
        ),
        'action_service_event_handler' => array(
            'checkbox',
            'input[name="service_event_handler"]'
        ),
        'action_service_flap_detection' => array(
            'checkbox',
            'input[name="service_flap_detection"]'
        ),
        'action_service_passive_checks' => array(
            'checkbox',
            'input[name="service_passive_checks"]'
        ),
        'action_service_submit_result' => array(
            'checkbox',
            'input[name="service_submit_result"]'
        ),
        'action_service_display_command' => array(
            'checkbox',
            'input[name="service_display_command"]'
        ),
        //Hosts Actions Access
        'action_host' => array(
            'checkbox',
            'input[type="checkbox"][name="all_host"]'
        ),
        'action_host_checks' => array(
            'checkbox',
            'input[name="host_checks"]'
        ),
        'action_host_notifications' => array(
            'checkbox',
            'input[name="host_notifications"]'
        ),
        'action_host_acknowledgement' => array(
            'checkbox',
            'input[name="host_acknowledgement"]'
        ),
        'action_host_disacknowledgement' => array(
            'checkbox',
            'input[name="host_disacknowledgement"]'
        ),
        'action_host_schedule_check' => array(
            'checkbox',
            'input[name="host_schedule_check"]'
        ),
        'action_host_schedule_forced_check' => array(
            'checkbox',
            'input[name="host_schedule_forced_check"]'
        ),
        'action_host_schedule_downtime' => array(
            'checkbox',
            'input[name="host_schedule_downtime"]'
        ),
        'action_host_comment' => array(
            'checkbox',
            'input[name="host_comment"]'
        ),
        'action_host_event_handler' => array(
            'checkbox',
            'input[name="host_event_handler"]'
        ),
        'action_host_flap_detection' => array(
            'checkbox',
            'input[name="host_flap_detection"]'
        ),
        'action_host_checks_for_services' => array(
            'checkbox',
            'input[name="host_checks_for_services"]'
        ),
        'action_host_notifications_for_services' => array(
            'checkbox',
            'input[name="host_notifications_for_services"]'
        ),
        'action_name_submit_result' => array(
            'checkbox',
            'input[name="host_submit_result"]'
        ),
        'enabled' => array(
            'radio',
            'input[name="acl_action_activate[acl_action_activate]"]'
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Administration\ACLActionConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on an acl page
     *
     * @param $context  Centreon context.
     * @param bool $visit True to navigate to configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50204&o=a');
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
     * Select all actions access
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
