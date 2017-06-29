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

class SnmpTrapsConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_MAIN = 1;
    const TAB_RELATIONS = 2;
    const TAB_ADVANCED = 3;

    protected $validField = 'input[name="traps_name"]';

    protected $properties = array(
        //Main tab.
        'name' => array(
            'text',
            'input[name="traps_name"]',
            self::TAB_MAIN
        ),
        'oid' => array(
            'text',
            'input[name="traps_oid"]',
            self::TAB_MAIN
        ),
        'vendor' => array(
            'select2',
            'select#manufacturer_id',
            self::TAB_MAIN
        ),
        'output' => array(
            'text',
            'input[name="traps_args"]',
            self::TAB_MAIN
        ),
        'status' => array(
            'select',
            'select[name="traps_status"]',
            self::TAB_MAIN
        ),
        'severity' => array(
            'select',
            'select[name="severity"]',
            self::TAB_MAIN
        ),
        'mode' => array(
            'checkbox',
            'input[name="traps_advanced_treatment"]',
            self::TAB_MAIN
        ),
        'behavior' => array(
            'select',
            'select[name="traps_advanced_treatment_default"]',
            self::TAB_MAIN
        ),
        'rule_string' => array(
            'text',
            'input[name="rule[0]"]',
            self::TAB_MAIN
        ),
        'rule_regexp' => array(
            'text',
            'input#regexp_0',
            self::TAB_MAIN
        ),
        'rule_status' => array(
            'select',
            'select#rulestatus_0',
            self::TAB_MAIN
        ),
        'rule_severity' => array(
            'select',
            'select[name="ruleseverity[0]"]',
            self::TAB_MAIN
        ),
        'submit' => array(
            'checkbox',
            'input[name="traps_submit_result_enable"]',
            self::TAB_MAIN
        ),
        'reschedule' => array(
            'checkbox',
            'input[name="traps_reschedule_svc_enable"]',
            self::TAB_MAIN
        ),
        'execute_command' => array(
            'checkbox',
            'input[name="traps_execution_command_enable"]',
            self::TAB_MAIN
        ),
        'special_command' => array(
            'text',
            'input[name="traps_execution_command"]',
            self::TAB_MAIN
        ),
        'comments' => array(
            'text',
            'textarea[name="traps_comments"]',
            self::TAB_MAIN
        ),
        //Relation tab
        'services' => array(
            'select2',
            'select#services',
            self::TAB_RELATIONS
        ),
        'service_templates' => array(
            'select2',
            'select#service_templates',
            self::TAB_RELATIONS
        ),
        //Advanced tab
        'routing' => array(
            'checkbox',
            'input[name="traps_routing_mode"]',
            self::TAB_ADVANCED
        ),
        'routing_definition' => array(
            'text',
            'input[name="traps_routing_value"]',
            self::TAB_ADVANCED
        ),
        'filter_services' => array(
            'text',
            'input[name="traps_routing_filter_services"]',
            self::TAB_ADVANCED
        ),
        'preexec' => array(
            'custom',
            'Preexec',
            self::TAB_ADVANCED
        ),
        'insert_information' => array(
            'checkbox',
            'input[name="traps_log"]',
            self::TAB_ADVANCED
        ),
        'timeout' => array(
            'text',
            'input[name="traps_timeout"]',
            self::TAB_ADVANCED
        ),
        'execution_interval' => array(
            'text',
            'input[name="traps_exec_interval"]',
            self::TAB_ADVANCED
        ),
        'execution_type' => array(
            'radio',
            'input[name="traps_exec_interval_type[traps_exec_interval_type]"]',
             self::TAB_ADVANCED
        ),
        'execution_method' => array(
            'radio',
            'input[name="traps_exec_method[traps_exec_method]"]',
            self::TAB_ADVANCED
        ),
        'check_downtime' => array(
            'radio',
            'input[name="traps_downtime[traps_downtime]"]',
            self::TAB_ADVANCED
        ),
        'output_transform' => array(
            'text',
            'input[name="traps_output_transform"]',
            self::TAB_ADVANCED
        ),
        'custom_code' => array(
            'text',
            'textarea[name="traps_customcode"]',
            self::TAB_ADVANCED
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\SnmpTrapsConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a SNMP trap configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param bool $visit    True to navigate to a blank configuration page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=61701&o=a#');
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
     * get preexec
     *
     * @return preexec
     */
    protected function getPreexec()
    {
        $preexec = array();
        $i = 0;
        while (true) {
            $name = $this->context->getSession()->getPage()->findField('preexec_' . $i);
            if (is_null($name)) {
                break ;
            }
            $preexec[] = $name->getValue();
            ++$i;
        }
        return $preexec;
    }

    /**
     * set preexec
     */
    protected function setPreexec($preexec)
    {
        $currentPreexec = $this->getPreexec();
        $i = count($currentPreexec);
        foreach ($preexec as $name) {
            $this->context->assertFind('css' , '#preexec_add span')->click();
            $this->context->assertFindField('preexec_' . $i)->setValue($name);
            $i++;
        }
    }
}
