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

class MassiveChangeHostConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const CONFIGURATION_TAB = 1;
    const NOTIFICATION_TAB = 2;
    const RELATIONS_TAB = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    protected $validField = 'input[name="host_snmp_community"]';

    protected $properties = array(
        // Configuration tab
        'snmp_community' => array(
            'input',
            'input[name="host_snmp_community"]',
            self::CONFIGURATION_TAB
        ),
        'snmp_version' => array(
            'select',
            'select[name="host_snmp_version"]',
            self::CONFIGURATION_TAB
        ),
        'monitored_from' => array(
            'select',
            'select[name="nagios_server_id"]',
            self::CONFIGURATION_TAB
        ),
        'monitored_option' => array(
            'radio',
            'input[name="mc_mod_nsid[mc_mod_nsid]"]',
            self::CONFIGURATION_TAB
        ),
        'location' => array(
            'select2',
            'select#host_location',
            self::CONFIGURATION_TAB
        ),
        'update_mode_tplp' => array(
            'radio',
            'input[name="mc_mod_tplp[mc_mod_tplp]"]',
            self::CONFIGURATION_TAB
        ),
        'templates' => array(
            'custom',
            'Templates',
            self::CONFIGURATION_TAB
        ),
        'service_linked_to_template' => array(
            'radio',
            'input[name="dupSvTplAssoc[dupSvTplAssoc]"]',
            self::CONFIGURATION_TAB
        ),
        'check_command' => array(
            'select2',
            'select#command_command_id',
            self::CONFIGURATION_TAB
        ),
        'command_arguments' => array(
            'input',
            'input[name="command_command_id_arg1"]',
            self::CONFIGURATION_TAB
        ),
        'macros' => array(
            'custom',
            'Macros',
            self::CONFIGURATION_TAB
        ),
        'check_period' => array(
            'select2',
            'select#timeperiod_tp_id',
            self::CONFIGURATION_TAB
        ),
        'max_check_attempts' => array(
            'input',
            'input[name="host_max_check_attempts"]',
            self::CONFIGURATION_TAB
        ),
        'normal_check_interval' => array(
            'input',
            'input[name="host_check_interval"]',
            self::CONFIGURATION_TAB
        ),
        'retry_check_interval' => array(
            'input',
            'input[name="host_retry_check_interval"]',
            self::CONFIGURATION_TAB
        ),
        'active_checks_enabled' => array(
            'radio',
            'input[name="host_active_checks_enabled[host_active_checks_enabled]"]',
            self::CONFIGURATION_TAB
        ),
        'passive_checks_enabled' => array(
            'radio',
            'input[name="host_passive_checks_enabled[host_passive_checks_enabled]"]',
            self::CONFIGURATION_TAB
        ),
        // Notification tab
        'notifications_enabled' => array(
            'radio',
            'input[name="host_notifications_enabled[host_notifications_enabled]"]',
            self::NOTIFICATION_TAB
        ),
        'contact_additive_inheritance' => array(
            'radio',
            'input[name="mc_contact_additive_inheritance[mc_contact_additive_inheritance]"]',
            self::NOTIFICATION_TAB
        ),
        'contacts' => array(
            'select2',
            'select[name="host_cs[]"]',
            self::NOTIFICATION_TAB
        ),
        'contact_group_additive_inheritance' => array(
            'radio',
            'input[name="mc_cg_additive_inheritance[mc_cg_additive_inheritance]"]',
            self::NOTIFICATION_TAB
        ),
        'contact_groups' => array(
            'select2',
            'select[name="host_cgs[]"]',
            self::NOTIFICATION_TAB
        ),
        'update_mode_notifopts' => array(
            'radio',
            'input[name="mc_mod_notifopts[mc_mod_notifopts]"]',
            self::NOTIFICATION_TAB
        ),
        'notify_on_down' => array(
            'checkbox',
            'input[name="host_notifOpts[d]"]',
            self::NOTIFICATION_TAB
        ),
        'notify_on_unreachable' => array(
            'checkbox',
            'input[name="host_notifOpts[u]"]',
        ),
        'notify_on_recovery' => array(
            'checkbox',
            'input[name="host_notifOpts[r]"]',
            self::NOTIFICATION_TAB
        ),
        'notify_on_flapping' => array(
            'checkbox',
            'input[name="host_notifOpts[f]"]',
            self::NOTIFICATION_TAB
        ),
        'notify_on_downtime_scheduled' => array(
            'checkbox',
            'input[name="host_notifOpts[s]"]',
            self::NOTIFICATION_TAB
        ),
        'notify_on_none' => array(
            'checkbox',
            'input[name="host_notifOpts[n]"]',
            self::NOTIFICATION_TAB
        ),
        'update_mode_notif_interval' => array(
            'radio',
            'input[name="mc_mod_notifopt_notification_interval[mc_mod_notifopt_notification_interval]"]',
            self::NOTIFICATION_TAB
        ),
        'notification_interval' => array(
            'input',
            'input[name="host_notification_interval"]',
            self::NOTIFICATION_TAB
        ),
        'update_mode_timeperiod' => array(
            'radio',
            'input[name="mc_mod_notifopt_timeperiod[mc_mod_notifopt_timeperiod]"]',
            self::NOTIFICATION_TAB
        ),
        'notification_period' => array(
            'select2',
            'select#timeperiod_tp_id2',
            self::NOTIFICATION_TAB
        ),
        'update_mode_first_notif_delay' => array(
            'radio',
            'input[name="mc_mod_notifopt_first_notification_delay[mc_mod_notifopt_first_notification_delay]"]',
            self::NOTIFICATION_TAB
        ),
        'first_notification_delay' => array(
            'input',
            'input[name="host_first_notification_delay"]',
            self::NOTIFICATION_TAB
        ),
        'recovery_notification_delay' => array(
            'input',
            'input[name="host_recovery_notification_delay"]',
            self::NOTIFICATION_TAB
        ),
        // Relations tab
        'update_mode_hhg' => array(
            'radio',
            'input[name="mc_mod_hhg[mc_mod_hhg]"]',
            self::RELATIONS_TAB
        ),
        'parent_host_groups' => array(
            'select2',
            'select#host_hgs',
            self::RELATIONS_TAB
        ),
        'update_mode_hhc' => array(
            'radio',
            'input[name="mc_mod_hhc[mc_mod_hhc]"]',
            self::RELATIONS_TAB
        ),
        'parent_host_categories' => array(
            'select2',
            'select#host_hcs',
            self::RELATIONS_TAB
        ),
        'update_mode_hpar' => array(
            'radio',
            'input[name="mc_mod_hpar[mc_mod_hpar]"]',
            self::RELATIONS_TAB
        ),
        'parent_hosts' => array(
            'select2',
            'select#host_parents',
            self::RELATIONS_TAB
        ),
        'update_mode_hch' => array(
            'radio',
            'input[name="mc_mod_hch[mc_mod_hch]"]',
            self::RELATIONS_TAB
        ),
        'child_hosts' => array(
            'select2',
            'select#host_childs',
            self::RELATIONS_TAB
        ),
        // Data tab
        'acknowledgement_timeout' => array(
            'input',
            'input[name="host_acknowledgement_timeout"]',
            self::DATA_TAB
        ),
        'check_freshness' => array(
            'radio',
            'input[name="host_check_freshness[host_check_freshness]"]',
            self::DATA_TAB
        ),
        'freshness_threshold' => array(
            'input',
            'input[name="host_freshness_threshold"]',
            self::DATA_TAB
        ),
        'flap_detection_enabled' => array(
            'radio',
            'input[name="host_flap_detection_enabled[host_flap_detection_enabled]"]',
            self::DATA_TAB
        ),
        'low_flap_threshold' => array(
            'input',
            'input[name="host_low_flap_threshold"]',
            self::DATA_TAB
        ),
        'high_flap_threshold' => array(
            'input',
            'input[name="host_high_flap_threshold"]',
            self::DATA_TAB
        ),
        'event_handler_enabled' => array(
            'radio',
            'input[name="host_event_handler_enabled[host_event_handler_enabled]"]',
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
        'url' => array(
            'input',
            'input[name="ehi_notes_url"]',
            self::EXTENDED_TAB
        ),
        'notes' => array(
            'input',
            'input[name="ehi_notes"]',
            self::EXTENDED_TAB
        ),
        'action_url' => array(
            'input',
            'input[name="ehi_action_url"]',
            self::EXTENDED_TAB
        ),
        'icon' => array(
            'select',
            'select#ehi_icon_image',
            self::EXTENDED_TAB
        ),
        'alt_icon' => array(
            'input',
            'input[name="ehi_icon_image_alt"]',
            self::EXTENDED_TAB
        ),
        'vrml_image' => array(
            'select',
            'select#ehi_vrml_image',
            self::EXTENDED_TAB
        ),
        'geo_coordinates' => array(
            'input',
            'input[name="geo_coords"]',
            self::EXTENDED_TAB
        ),
        'severity_level' => array(
            'select',
            'select[name="criticality_id"]',
            self::EXTENDED_TAB
        ),
        'enabled' => array(
            'radio',
            'input[name="host_activate[host_activate]"]',
            self::EXTENDED_TAB
        ),
        'comments' => array(
            'input',
            'textarea[name="host_comment"]',
            self::EXTENDED_TAB
        )
    );

    /**
     *  Host template edit page.
     *
     * @param $context  Centreon context object.
     * @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=6');
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
    public function setProperties($properties): void
    {
        if (isset($properties['macros'])) {
            $macros['macros'] = $properties['macros'];
            unset($properties['macros']);
            $properties = array_merge($macros, $properties);
        }
        parent::setProperties($properties);
    }

    /**
     *  Get macros.
     *
     * @return array<string,string>
     */
    protected function getMacros()
    {
        $macros = array();

        $i = 0;
        while (true) {
            $name = $this->context->getSession()->getPage()->findField('macroInput_' . $i);
            if (is_null($name)) {
                break;
            }
            $value = $this->context->assertFindField('macroValue_' . $i);
            $macros[$name->getValue()] = $value->getValue();
            $i++;
        }

        return $macros;
    }

    /**
     *  Get host templates.
     *
     * @return host templates
     */
    protected function getTemplates()
    {
        $templates = array();

        $elements = $this->context->getSession()->getPage()->findAll(
            'css',
            '[id^="tpSelect_"] option[selected="selected"]'
        );
        foreach ($elements as $element) {
            $templateName = $element->getText();
            if ($templateName != '') {
                $templates[] = $templateName;
            }
        }

        return $templates;
    }

    /**
     *  Set macros.
     *
     * @param $macros Macros.
     */
    protected function setMacros($macros)
    {
        $currentMacros = $this->getMacros();
        $i = count($currentMacros);
        foreach ($macros as $name => $value) {
            $this->context->assertFind('css', '#macro_add p')->click();
            $this->context->assertFindField('macroInput_' . $i)->setValue($name);
            $this->context->assertFindField('macroValue_' . $i)->setValue((string) $value);
            $i++;
        }
    }

    /**
     *  Set host templates.
     *
     * @param $templates  Parent templates.
     */
    protected function setTemplates($templates)
    {
        if (!is_array($templates)) {
            $templates = array($templates);
        }
        $i = 0;
        foreach ($templates as $tpl) {
            $this->context->assertFind('css', '#template_add span')->click();
            $this->context->selectInList('#tpSelect_' . $i, $tpl);
            $i++;
        }
    }
}
