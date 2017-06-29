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

class EscalationConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_UNKNOWN = 0;
    const TAB_INFORMATIONS = 1;
    const TAB_IMPACTED_RESOURCES = 2;

    protected $validField = 'input[name="esc_name"]';

    protected $properties = array(
        // Configuration tab.
        'name' => array(
            'input',
            'input[name="esc_name"]',
            self::TAB_INFORMATIONS
        ),
        'alias' => array(
            'input',
            'input[name="esc_alias"]',
            self::TAB_INFORMATIONS
        ),
        'first_notification' => array(
            'input',
            'input[name="first_notification"]',
            self::TAB_INFORMATIONS
        ),
        'last_notification' => array(
            'input',
            'input[name="last_notification"]',
            self::TAB_INFORMATIONS
        ),
        'notification_interval' => array(
            'input',
            'input[name="notification_interval"]',
            self::TAB_INFORMATIONS
        ),
        'escalation_period' => array(
            'select2',
            'select#escalation_period',
            self::TAB_INFORMATIONS
        ),
        'host_notify_on_down' => array(
            'checkbox',
            'input[name="escalation_options1[d]',
            self::TAB_INFORMATIONS
        ),
        'host_notify_on_unreachable' => array(
            'checkbox',
            'input[name="escalation_options1[u]',
            self::TAB_INFORMATIONS
        ),
        'host_notify_on_recovery' => array(
            'checkbox',
            'input[name="escalation_options1[r]',
            self::TAB_INFORMATIONS
        ),
        'service_notify_on_warning' => array(
            'checkbox',
            'input[name="escalation_options2[w]',
            self::TAB_INFORMATIONS
        ),
        'service_notify_on_unknown' => array(
            'checkbox',
            'input[name="escalation_options2[u]',
            self::TAB_INFORMATIONS
        ),
        'service_notify_on_critical' => array(
            'checkbox',
            'input[name="escalation_options2[c]',
            self::TAB_INFORMATIONS
        ),
        'service_notify_on_recovery' => array(
            'checkbox',
            'input[name="escalation_options2[r]',
            self::TAB_INFORMATIONS
        ),
        'contactgroups' => array(
            'select2',
            'select#esc_cgs',
            self::TAB_INFORMATIONS
        ),
        'comment' => array(
            'input',
            'input[name="esc_comment"]',
            self::TAB_INFORMATIONS
        ),
        // Resources tab.
        'host_inheritance_to_services' => array(
            'checkbox',
            'input[name="host_inheritance_to_services',
            self::TAB_IMPACTED_RESOURCES
        ),
        'hosts' => array(
            'select2',
            'select#esc_hosts',
            self::TAB_IMPACTED_RESOURCES
        ),
        'services' => array(
            'select2',
            'select#esc_hServices',
            self::TAB_IMPACTED_RESOURCES
        ),
        'hostgroup_inheritance_to_services' => array(
            'checkbox',
            'input[name="hostgroup_inheritance_to_services',
            self::TAB_IMPACTED_RESOURCES
        ),
        'hostgroups' => array(
            'select2',
            'select#esc_hgs',
            self::TAB_IMPACTED_RESOURCES
        ),
        'servicegroups' => array(
            'select2',
            'select#esc_sgs',
            self::TAB_IMPACTED_RESOURCES
        ),
        'metaservices' => array(
            'select2',
            'select#esc_metas',
            self::TAB_IMPACTED_RESOURCES
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\EscalationConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a escalation configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60401&o=a');
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

?>
