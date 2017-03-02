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

class ContactConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'table.ListTable';

    protected $properties = array(
        'alias' => array(
            'text',
            'td:nth-child(2)'
        ),
        'icon' => array(
            'attribute',
            'td:nth-child(2) img',
            'src'
        ),
        'name' => array(
            'text',
            'td:nth-child(3)'
        ),
        'email' => array(
            'text',
            'td:nth-child(4)'
        ),
        'host_notification_period' => array(
            'text',
            'td:nth-child(5)'
        ),
        'service_notification_period' => array(
            'text',
            'td:nth-child(6)'
        ),
        'language' => array(
            'text',
            'td:nth-child(7)'
        ),
        'access' => array(
            'text',
            'td:nth-child(8)'
        ),
        'admin' => array(
            'text',
            'td:nth-child(8)'
        ),
        'status' => array(
            'text',
            'td:nth-child(8)'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\ContactConfigurationPage';

    /**
     *  Contact list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60301');
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
