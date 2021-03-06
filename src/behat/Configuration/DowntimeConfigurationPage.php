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

class DowntimeConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TYPE_HOST = 1;
    const TYPE_SERVICE = 2;

    protected $validField = 'input[name="start"]';

    protected $properties = array(
        'type' => array('radio', 'input[name="downtimeType[downtimeType]"]'),
        'host' => array('select2', 'select#host_id'),
        'service' => array('select2', 'select#service_id'),
        'fixed' => array('checkbox', 'input[name="persistant"]'),
        'duration' => array('input', 'input[name="duration"]'),
        'duration_unit' => array('select', 'select[name="duration_scale"]'),
        'start_day' => array('input', 'input[name="start"]'),
        'start_time' => array('input', 'input[name="start_time"]'),
        'end_day' => array('input', 'input[name="end"]'),
        'end_time' => array('input', 'input[name="end_time"]'),
        'comment' => array('input', 'textarea[name="comment"]')
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\DowntimeConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a service downtime
     *  configuration page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank service downtime
     *                   configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=21001&o=a');
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
