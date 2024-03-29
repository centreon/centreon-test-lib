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

namespace Centreon\Test\Behat\Monitoring;

class HostMonitoringDetailsPage implements \Centreon\Test\Behat\Interfaces\Page
{
    const SERVICE_STATUS_TAB = 1;
    const PERFORMANCE_TAB = 2;
    const HOST_INFORMATIONS_TAB = 3;
    const COMMENTS_TAB = 4;

    const STATE_UP = 'UP';
    const STATE_DOWN = 'DOWN';
    const STATE_UNREACHABLE = 'UNREACHABLE';
    const STATE_PENDING = 'PENDING';

    protected $context;
    protected $hostname;

    /**
     *  Navigate to and/or check that we are on a host configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $hostname Host name.
     * @param $visit    True to navigate to a blank host configuration page.
     */
    public function __construct($context, $hostname, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=20202&o=hd&host_name=' . $hostname);
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
     *  Check that the current page is matching this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'li#c1.a');
    }

    /**
     *  Get properties printed on the host monitoring details page.
     *
     *  @return An array with detailed properties.
     */
    public function getProperties()
    {
        $result = array();

        //
        // Host information tab.
        //
        $tab = self::HOST_INFORMATIONS_TAB;
        $this->switchTab($tab);
        $table = $this->context->assertFind('css', '#tab' . $tab . ' table.ListTable');
        $context = $this->context;
        $getTableData = function ($child) use ($context, $table) {
            return $context->assertFindIn(
                $table,
                'css',
                'tr:nth-child(' . $child . ') td:nth-child(2)'
            )->getText();
        };
        $result['state'] = $getTableData(2);
        $result['output'] = $getTableData(3);
        $result['perfdata'] = $getTableData(4);
        $result['poller'] = $getTableData(5);
        $result['timezone'] = $getTableData(16);

        return $result;
    }

    /**
     *  Switch between tabs.
     *
     * @param $tab  Tab ID / Tab name.
     */
    public function switchTab($tab): void
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
    }
}
