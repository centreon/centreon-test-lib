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

class MassiveChangeServiceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const GENERAL_TAB = 1;
    const NOTIFICATIONS_TAB = 2;
    const RELATIONS_TAB  = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    protected $validField = 'select#service_hPars';

    protected $properties = array(
        // General tab
        'update_mode_pars' => array(
            'radio',
            'input[name="mc_mod_Pars[mc_mod_Pars]"]',
            self::GENERAL_TAB
        ),
        'hosts' => array(
            'select2',
            'select#service_hPars',
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
        'arguments' => array(
            'input',
            'input[name="command_command_id_arg"]',
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
        // Notifications tab
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
        'update_mode_cgs' => array(
            'radio',
            'input[name="mc_mod_cgs[mc_mod_cgs]"]',
            self::NOTIFICATIONS_TAB
        ),
        'contact_additive_inheritance' => array(
            'radio',
            'input[name="mc_contact_additive_inheritance[mc_contact_additive_inheritance]"]',
            self::NOTIFICATIONS_TAB
        ),
        'contacts' => array(
            'select2',
            'select#service_cs',
            self::NOTIFICATIONS_TAB
        ),
        'contact_group_additive_inheritance' => array(
            'radio',
            'input[name="mc_cg_additive_inheritance[mc_cg_additive_inheritance]"]',
            self::NOTIFICATIONS_TAB
        ),
        'contact_groups' => array(
            'select2',
            'select#service_cgs',
            self::NOTIFICATIONS_TAB
        ),
        'update_mode_notif_interval' => array(
            'radio',
            'input[name="mc_mod_notifopt_notification_interval[mc_mod_notifopt_notification_interval]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_interval' => array(
            'input',
            'input[name="service_notification_interval"]',
            self::NOTIFICATIONS_TAB
        ),
        'update_mode_notif_timeperiod' => array(
            'radio',
            'input[name="mc_mod_notifopt_timeperiod[mc_mod_notifopt_timeperiod]"]',
            self::NOTIFICATIONS_TAB
        ),
        'notification_period' => array(
            'select2',
            'select#timeperiod_tp_id2',
            self::NOTIFICATIONS_TAB
        ),
        'update_mode_notif_options' => array(
            'radio',
            'input[name="mc_mod_notifopts[mc_mod_notifopts]"]',
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
        'update_mode_first_notif_delay' => array(
            'radio',
            'input[name="mc_mod_notifopt_first_notification_delay[mc_mod_notifopt_first_notification_delay]"]',
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
        // Relations tab
        'update_mode_sgs' => array(
            'radio',
            'input[name="mc_mod_sgs[mc_mod_sgs]"]',
            self::RELATIONS_TAB
        ),
        'service_groups' => array(
            'select2',
            'select#service_sgs',
            self::RELATIONS_TAB
        ),
        'update_mode_traps' => array(
            'radio',
            'input[name="mc_mod_traps[mc_mod_traps]"]',
            self::RELATIONS_TAB
        ),
        'trap_relations' => array(
            'custom',
            'TrapRelations',
            /*'select2',
            'select#service_traps',*/
            self::RELATIONS_TAB
        ),
        // Data tab
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
        // Extended tab
        'graph_template' => array(
            'select2',
            'select#graph_id',
            self::EXTENDED_TAB
        ),
        'update_mode_sc' => array(
            'radio',
            'input[name="mc_mod_sc[mc_mod_sc]"]',
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
            'textarea[name="service_comment"]',
            self::EXTENDED_TAB
        )
    );

    /**
     *  Service massive change page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page. 
        $this->context = $context;
        	if ($visit) {
            $this->context->visit('main.php?p=60201');
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
     *  Get macros.
     *
     *  @return macros
     */
    protected function getMacros()
    {
        $macros = array();

        $i = 0;
        while (true) {
            $name = $this->context->getSession()->getPage()->findField('macroInput_' . $i);
            if (is_null($name)) {
                break ;
            }
            $value = $this->context->assertFindField('macroValue_' . $i);
            $macros[$name->getValue()] = $value->getValue();
            ++$i;
        }

        return $macros;
    }

    /**
     *  Get trap_relations.
     *
     *  @return trap_relations
     */
    protected function getTrapRelations()
    {
        return $this->context->assertFind('css', $propertyLocator)->getText();
    }

    /**
     *  Set macros.
     *
     *  @param $macros Macros.
     */
    protected function setMacros($macros)
    {
        $currentMacros = $this->getMacros();
        $i = count($currentMacros);
        foreach ($macros as $name => $value) {
            $this->context->assertFind('css' , '#macro_add p')->click();
            $this->context->assertFindField('macroInput_' . $i)->setValue($name);
            $this->context->assertFindField('macroValue_' . $i)->setValue($value);
            $i++;
        }
    }

    /**
     *  Set trap_relations.
     *
     *  @param $trap_relations TrapRelations.
     */
    protected function setTrapRelations($trapRelations)
    {
        $css_id = 'select#service_traps';
        foreach ($trapRelations as $what1 => $what2) {
            $this->context->selectToSelectTwoWithSpace('select#service_traps', $what1, $what2);
        }
    }
}
