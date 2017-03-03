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

class ServiceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const GENERAL_TAB = 1;
    const NOTIFICATIONS_TAB = 2;
    const RELATIONS_TAB = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    protected $validField = 'input[name="service_description"]';

    protected $properties = array(
        // General tab.
        'hosts' => array(
            'select2',
            'select#service_hPars',
            self::GENERAL_TAB
        ),
        'description' => array(
            'text',
            'input[name="service_description"]',
            self::GENERAL_TAB
        ),
        'templates' => array(
            'select2',
            'select#service_template_model_stm_id',
            self::GENERAL_TAB
        ),
        'check_command' => array(
            'select2',
            'select#command_command_id',
            self::GENERAL_TAB
        ),
        'check_period' => array(
            'select2',
            'select#timeperiod_tp_id',
            self::GENERAL_TAB
        ),
        'max_check_attempts' => array(
            'text',
            'input[name="service_max_check_attempts"]',
            self::GENERAL_TAB
        ),
        'normal_check_interval' => array(
            'text',
            'input[name="service_normal_check_interval"]',
            self::GENERAL_TAB
        ),
        'retry_check_interval' => array(
            'text',
            'input[name="service_retry_check_interval"]',
            self::GENERAL_TAB
        ),
        'active_checks_enabled' => array(
            'radio',
            'input[name="service_active_checks_enabled[service_active_checks_enabled]"]',
            self::GENERAL_TAB
        ),
        'passive_checks_enabled' => array(
            'radio',
            'input[name="service_passive_checks_enabled[service_passive_checks_enabled]"]',
            self::GENERAL_TAB
        ),
        // Notifications tab.
        'notifications_enabled' => array(
            'radio',
            'input[name="service_notifications_enabled[service_notifications_enabled]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_interval' => array(
            'text',
            'input[name="service_notification_interval"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_period' => array(
            'select2',
            'select#timeperiod_tp_id2',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_recovery' => array(
            'checkbox',
            'input[name="service_notifOpts[r]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_critical' => array(
            'checkbox',
            'input[name="service_notifOpts[c]"]',
            self::NOTIFICATIONS_TAB
        ),
        'first_notification_delay' => array(
            'text',
            'input[name="service_first_notification_delay"]',
            self::NOTIFICATIONS_TAB
        ),
        'recovery_notification_delay' => array(
            'text',
            'input[name="service_recovery_notification_delay"]',
            self::NOTIFICATIONS_TAB
        ),
        'cs' => array(
            'select2',
            'select#service_cs',
            self::NOTIFICATIONS_TAB
        ),
        // Data tab.
        'acknowledgement_timeout' => array(
            'text',
            'input[name="service_acknowledgement_timeout"]',
            self::DATA_TAB
        )
    );

    /**
     *  Navigate to and/or check that we are on a service configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank service
     *                   configuration page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60201&o=a');
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
