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

class ServiceMonitoringDetailsPage implements \Centreon\Test\Behat\Interfaces\Page
{
    const STATE_OK = 'OK';
    const STATE_WARNING = 'WARNING';
    const STATE_CRITICAL = 'CRITICAL';
    const STATE_UNKNOWN = 'UNKNOWN';
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
        'service_description' => array(
            'text',
            'td:nth-child(2)'
        ),
        'entry_time' => array(
            'text',
            'td:nth-child(3)'
        ),
        'author' => array(
            'text',
            'td:nth-child(4)'
        ),
        'comment' => array(
            'text',
            'td:nth-child(5)'
        ),
        'persistent' => array(
            'text',
            'td:nth-child(6)'
        )
    );

    /**
     * Navigate to a specific service monitoring details page and/or
     * check that it matches this class.
     *
     * @param CentreonContext $context  Centreon context.
     * @param string $host Host name. If empty, current page will not be changed.
     * @param string $service Service description. If empty, current page
     * will not be changed.
     * @throws \Exception
     */
    public function __construct($context, $host = '', $service = '')
    {
        // Visit page.
        $this->context = $context;
        if (!empty($host) && !empty($service)) {
            $this->context->visit('main.php?p=20201&o=svcd&host_name=' . $host . '&service_description=' . $service);
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
        // This check is very limited, multiple pages can match.
        return $this->context->getSession()->getPage()->has('css', '.ListTable');
    }

    /**
     * Get properties printed on the service details page.
     *
     * @return array An array with detailed properties.
     * @throws \Exception
     */
    public function getProperties()
    {
        $table = $this->context->assertFind('css', 'table.ListTable');
        $result = array();

        // State
        $result['state'] = $this->context->assertFindIn(
            $table,
            'css',
            'tbody tr:nth-child(2) td:nth-child(2)'
        )->getText();

        // Performance data
        $perfdata = $this->context->assertFindIn($table, 'css', 'tbody tr:nth-child(5) td:nth-child(2) pre')->getText();
        $result['perfdata'] = array();
        $perfdata = trim($perfdata);
        if (!empty($perfdata)) {
            $result['perfdata'] = explode(' ', $perfdata);
        }

        // last_check
        $lastCheck = $this->context->assertFindIn($table, 'css', 'tbody tr:nth-child(9) td:nth-child(2)')->getText();
        $lastCheck = \DateTime::createFromFormat('Y/m/d - H:i:s', $lastCheck);
        $result['last_check'] = ($lastCheck === false) ? false : $lastCheck->getTimestamp();

        // in_downtime
        $inDowntime = $this->context->assertFindIn($table, 'css', 'tbody tr:nth-child(19) td:nth-child(2)')->getText();
        $result['in_downtime'] = ($inDowntime == 'Yes') ? true : false;

        return $result;
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
        $table = $this->context->assertFind('css', '#Tmainpage > table.ListTable:nth-of-type(3)');

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
