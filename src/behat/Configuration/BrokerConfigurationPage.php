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

class BrokerConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_GENERAL = 1;
    const TAB_INPUT = 2;
    const TAB_LOGGER = 3;
    const TAB_OUTPUT = 4;

    protected $validField = 'select[name="ns_nagios_server"]';

    protected $properties = array(
        // Configuration tab.
        'requester' => array(
            'select',
            'select[name="ns_nagios_server"]',
            self::TAB_GENERAL,
        ),
        'name' => array(
            'input',
            'input[name="name"]',
            self::TAB_GENERAL
        ),
        'daemon' => array(
            'radio',
            'input[name="activate_watchdog[activate_watchdog]"]',
            self::TAB_GENERAL
        ),
        'filename' => array(
            'input',
            'input[name="filename"]',
            self::TAB_GENERAL
        ),
        'cache_directory' => array(
            'input',
            'input[name="cache_directory"]',
            self::TAB_GENERAL
        ),
        'status' => array(
            'radio',
            'input[name="activate[activate]"]',
            self::TAB_GENERAL
        ),
        'timestamp' => array(
            'radio',
            'input[name="write_timestamp[write_timestamp]"]',
            self::TAB_GENERAL
        ),
        'thread_id' => array(
            'radio',
            'input[name="write_thread_id[write_thread_id]"]',
            self::TAB_GENERAL
        ),
        'statistics' => array(
            'radio',
            'input[name="stats_activate[stats_activate]"]',
            self::TAB_GENERAL
        ),
        'event_queue_max_size' => array(
            'input',
            'input[name="event_queue_max_size"]',
            self::TAB_GENERAL
        ),
        'command_file' => array(
            'input',
            'input[name="command_file"]',
            self::TAB_GENERAL
        ),
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\BrokerConfigurationListingPage';

    /**
     * BrokerConfigurationPage constructor.
     * @param $context
     * @param bool $visit
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60909&o=a');
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
