<?php
/*
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

/**
 * Class
 *
 * @class RecurrentDowntimeConfigurationPage
 * @package Centreon\Test\Behat\Configuration
 */
class RecurrentDowntimeConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const CONFIGURATION_TAB = 1;
    const RELATIONS_TAB = 2;

    /** @var */
    public $nbPeriod;
    /** @var string */
    protected $validField = 'input#start_period_1';
    /** @var array[] */
    protected $properties = array(
        // configuration tab.
        'name' => array(
            'input',
            'input[name="downtime_name"]',
            self::CONFIGURATION_TAB
        ),
        'alias' => array(
            'input',
            'input[name="downtime_description"]',
            self::CONFIGURATION_TAB
        ),
        'days' => array(
            'custom',
            'days',
            self::CONFIGURATION_TAB
        ),
        'start' => array(
            'input',
            'input[name="periods[1][start_period]"]',
            self::CONFIGURATION_TAB
        ),
        'end' => array(
            'input',
            'input[name="periods[1][end_period]"]',
            self::CONFIGURATION_TAB
        ),
        'host_relation' => array(
            'select2',
            'select#host_relation',
            self::RELATIONS_TAB
        ),
        'hostgroup_relation' => array(
            'select2',
            'select#hostgroup_relation',
            self::RELATIONS_TAB
        ),
        'svc_relation' => array(
            'select2',
            'select#svc_relation',
            self::RELATIONS_TAB
        ),
        'svcgroup_relation' => array(
            'select2',
            'select#svcgroup_relation',
            self::RELATIONS_TAB
        )
    );

    /**
     * RecurrentDowntimeConfigurationPage constructor
     *  Service template edit page.
     *
     * @param $context  Centreon context object.
     * @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=21003&o=a');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     * Get downtime days
     */
    protected function getDays()
    {
        // @todo
    }

    /**
     * Set downtime days
     *
     * @param $days
     *
     * @return void
     */
    protected function setDays($days)
    {
        if (!is_array($days)) {
            $days = array($days);
        }
        foreach ($days as $day) {
            $this->context->assertFind('css', 'input[name="periods[1][days][]"][value="' . $day . '"]')->click();
        }
    }

    /**
     *  Save service template.
     *
     * @return void
     */
    public function addPeriode(): void
    {
        $this->context->assertFind(
            'css',
            'table tbody tr td.FormRowValue div a  '
        )->click();
        $this->nbPeriod++;
    }

    /**
     *  Switch between tabs.
     *
     * @param $tab  Tab periode ID.
     *
     * @return void
     */
    public function switchPeriode($tab): void
    {
        $this->context->assertFind('css', 'ul#ul_tabs li:nth-child(' . $tab . ') a:nth-child(1)')->click();
    }
}
