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

use Centreon\Test\Behat\CentreonContext;

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

    /**
     * @var CentreonContext Centreon context
     */
    protected $context;

    /**
     * @var array Array representing the table structure
     */
    protected $commentsProperties = array(
        'hostname' => array(
            'text',
            'td:nth-child(1)'
        ),
        'entry_time' => array(
            'text',
            'td:nth-child(2)'
        ),
        'author' => array(
            'text',
            'td:nth-child(3)'
        ),
        'comment' => array(
            'text',
            'td:nth-child(4)'
        ),
        'persistent' => array(
            'text',
            'td:nth-child(5)'
        )
    );

    /**
     * Navigate to and/or check that we are on a host configuration page.
     *
     * @param CentreonContext $context Centreon context.
     * @param string $hostname Host name.
     * @param boolean $visit Set true to navigate to a blank host configuration page.
     * @throws \Exception
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
     * Check that the current page is matching this class.
     *
     * @return boolean Return true if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'li#c1.a');
    }

    /**
     * Get properties printed on the host monitoring details page.
     *
     * @return array An array with detailed properties.
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
     * Switch between tabs.
     *
     * @param int $tab Tab ID / Tab name.
     */
    public function switchTab($tab)
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
    }

    /**
     * Retrieve all comments.
     *
     * @return array Array containing all comments
     * @throws \Exception
     */
    public function getComments()
    {
        $comments = array();
        $table = $this->context->assertFind('css', 'div#tab4 > table.ListTable');

        $elements = $table->findAll(
            'css',
            '.list_one, .list_two'
        );
        foreach ($elements as $element) {
            $newComment = array();
            foreach ($this->commentsProperties as $property => $metadata) {
                $propertyLocator = isset($metadata[1]) ? $metadata[1] : '';
                $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                $newComment[$property] = $component->getText();
            }
            $comments[] = $newComment;
        }
        return $comments;
    }
}
