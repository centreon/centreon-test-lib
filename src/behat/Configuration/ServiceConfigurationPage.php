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
            'input',
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
        'macros' => array(
            'custom',
            'Macros',
            self::GENERAL_TAB
        ),
        'check_period' => array(
            'select2',
            'select#timeperiod_tp_id',
            self::GENERAL_TAB
        ),
        'max_check_attempts' => array(
            'input',
            'input[name="service_max_check_attempts"]',
            self::GENERAL_TAB
        ),
        'normal_check_interval' => array(
            'input',
            'input[name="service_normal_check_interval"]',
            self::GENERAL_TAB
        ),
        'retry_check_interval' => array(
            'input',
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
        'is_volatile' => array(
            'radio',
            'input[name="service_is_volatile[service_is_volatile]"]',
            self::GENERAL_TAB
        ),
        // Notifications tab.
        'notifications_enabled' => array(
            'radio',
            'input[name="service_notifications_enabled[service_notifications_enabled]"]',
            self::NOTIFICATIONS_TAB
        ),
        'inherits_contacts_groups' => array(
            'radio',
            'input[name="service_use_only_contacts_from_host[service_use_only_contacts_from_host]"]',
            self::NOTIFICATIONS_TAB
        ),
        'contacts' => array(
            'select2',
            'select#service_cs',
            self::NOTIFICATIONS_TAB
        ),
        'contact_additive_inheritance' => array(
            'checkbox',
            'input[name="contact_additive_inheritance"]',
            self::NOTIFICATIONS_TAB
        ),
        'contact_groups' => array(
            'select2',
            'select#service_cgs',
            self::NOTIFICATIONS_TAB
        ),
        'contact_group_additive_inheritance' => array(
            'checkbox',
            'input[name="cg_additive_inheritance"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_interval' => array(
            'input',
            'input[name="service_notification_interval"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_period' => array(
            'select2',
            'select#timeperiod_tp_id2',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_warning' => array(
            'checkbox',
            'input[name="service_notifOpts[w]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_unknown' => array(
            'checkbox',
            'input[name="service_notifOpts[u]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_critical' => array(
            'checkbox',
            'input[name="service_notifOpts[c]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_recovery' => array(
            'checkbox',
            'input[name="service_notifOpts[r]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_flapping' => array(
            'checkbox',
            'input[name="service_notifOpts[f]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_downtime_scheduled' => array(
            'checkbox',
            'input[name="service_notifOpts[s]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notify_on_none' => array(
            'checkbox',
            'input[name="service_notifOpts[n]"]',
            self::NOTIFICATIONS_TAB
        ),
        'first_notification_delay' => array(
            'input',
            'input[name="service_first_notification_delay"]',
            self::NOTIFICATIONS_TAB
        ),
        'recovery_notification_delay' => array(
            'input',
            'input[name="service_recovery_notification_delay"]',
            self::NOTIFICATIONS_TAB
        ),
        'cs' => array(
            'select2',
            'select#service_cs',
            self::NOTIFICATIONS_TAB
        ),
        // Relations tab
        'service_groups' => array(
            'select2',
            'select#service_sgs',
            self::RELATIONS_TAB
        ),
        'trap_relations' => array(
            'select2',
            'select#service_traps',
            self::RELATIONS_TAB
        ),
        // Data tab.
        'obsess_over_service' => array(
            'radio',
            'input[name="service_obsess_over_service[service_obsess_over_service]"]',
            self::DATA_TAB
        ),
        'acknowledgement_timeout' => array(
            'input',
            'input[name="service_acknowledgement_timeout"]',
            self::DATA_TAB
        ),
        'check_freshness' => array(
            'radio',
            'input[name="service_check_freshness[service_check_freshness]"]',
            self::DATA_TAB
        ),
        'freshness_threshold' => array(
            'input',
            'input[name="service_freshness_threshold"]',
            self::DATA_TAB
        ),
        'flap_detection_enabled' => array(
            'radio',
            'input[name="service_flap_detection_enabled[service_flap_detection_enabled]"]',
            self::DATA_TAB
        ),
        'low_flap_threshold' => array(
            'input',
            'input[name="service_low_flap_threshold"]',
            self::DATA_TAB
        ),
        'high_flap_threshold' => array(
            'input',
            'input[name="service_high_flap_threshold"]',
            self::DATA_TAB
        ),
        'retain_status_information' => array(
            'radio',
            'input[name="service_retain_status_information[service_retain_status_information]"]',
            self::DATA_TAB
        ),
        'retain_non_status_information' => array(
            'radio',
            'input[name="service_retain_nonstatus_information[service_retain_nonstatus_information]"]',
            self::DATA_TAB
        ),
        'stalking_on_ok' => array(
            'checkbox',
            'input[name="service_stalOpts[o]"]',
            self::DATA_TAB
        ),
        'stalking_on_warning' => array(
            'checkbox',
            'input[name="service_stalOpts[w]"]',
            self::DATA_TAB
        ),
        'stalking_on_unknown' => array(
            'checkbox',
            'input[name="service_stalOpts[u]"]',
            self::DATA_TAB
        ),
        'stalking_on_critical' => array(
            'checkbox',
            'input[name="service_stalOpts[c]"]',
            self::DATA_TAB
        ),
        'event_handler_enabled' => array(
            'radio',
            'input[name="service_event_handler_enabled[service_event_handler_enabled]"]',
            self::DATA_TAB
        ),
        'event_handler' => array(
            'select2',
            'select#command_command_id2',
            self::DATA_TAB
        ),
        'event_handler_arguments' => array(
            'input',
            'input[name="command_command_id_arg2"]',
            self::DATA_TAB
        ),
        // Extended tab.
        'graph_template' => array(
            'select2',
            'select#graph_id',
            self::EXTENDED_TAB
        ),
        'service_categories' => array(
            'select2',
            'select#service_categories',
            self::EXTENDED_TAB
        ),
        'url' => array(
            'input',
            'input[name="esi_notes_url"]',
            self::EXTENDED_TAB
        ),
        'notes' => array(
            'input',
            'input[name="esi_notes"]',
            self::EXTENDED_TAB
        ),
        'action_url' => array(
            'input',
            'input[name="esi_action_url"]',
            self::EXTENDED_TAB
        ),
        'icon' => array(
            'select',
            'select#esi_icon_image',
            self::EXTENDED_TAB
        ),
        'alt_icon' => array(
            'input',
            'input[name="esi_icon_image_alt"]',
            self::EXTENDED_TAB
        ),
        'severity' => array(
            'select',
            'select[name="criticality_id"]',
            self::EXTENDED_TAB
        ),
        'geo_coordinates' => array(
            'input',
            'input[name="geo_coords"]',
            self::EXTENDED_TAB
        ),
        'status' => array(
            'radio',
            'input[name="service_activate[service_activate]"]',
            self::EXTENDED_TAB
        ),
        'comments' => array(
            'input',
            'textarea',
            self::EXTENDED_TAB
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\ServiceConfigurationListingPage';

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

    /**
     * Set properties
     *
     * @param $properties
     * @throws \Exception
     */
    public function setProperties($properties)
    {
        if (isset($properties['macros'])) {
            $macros['macros'] = $properties['macros'];
            unset($properties['macros']);
            $properties = array_merge($macros, $properties);
        }
        parent::setProperties($properties);
    }

    /**
     *  Get macros
     *
     * @return macros
     */
    protected function getMacros()
    {
        $macros = array();

        $inputs = $this->context->getSession()->getPage()->findAll('css', '[id^=macroInput]');
        //[att*=str] :-  attribute value contains str – value can contain str anywhere either in middle or at end.

        foreach ($inputs as $input) {
            $elementId = $input->getAttribute('id');
            if (preg_match('/macroInput_(\d+)/', $elementId, $matches)) {
                $macroId = $matches[1];
                $macroName = $input->getValue();
                $macros[$macroName] =
                    $this->context->assertFind('css', '#macroValue_' . $macroId)->getValue();
            }
        }

        return $macros;
    }

    /**
     *  Set macros
     *
     * @param $macros Macros.
     */
    protected function setMacros($macros)
    {
        $currentMacros = $this->getMacros();
        $finalMacros = array_merge($macros, $currentMacros);
        $countFinalMacros = count($finalMacros) - count($currentMacros);
        $addButton = $this->context->assertFind('css', '#macro_add p');
        for ($i = 0; $i < $countFinalMacros; $i++) {
            $addButton->click();
        }

        $i = 0;

        $inputs = $this->context->getSession()->getPage()->findAll('css', '[id^=macroInput]');
        $macroNames = array_keys($finalMacros);
        foreach ($inputs as $input) {
            $elementId = $input->getAttribute('id');
            if (preg_match('/macroInput_(\d+)/', $elementId, $matches)) {
                $macroId = $matches[1];
                $input->setValue($macroNames[$i]);
                $macroName = $input->getValue();
                $macros[$macroName] = $this->context->assertFind(
                    'css',
                    '#macroValue_' . $macroId
                )->setValue($finalMacros[$macroNames[$i]]);
            }
            $i++;
        }
    }
}
