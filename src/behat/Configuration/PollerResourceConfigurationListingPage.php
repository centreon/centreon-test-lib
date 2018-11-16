<?php
/**
 * Copyright 2005-2018 Centreon
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

use Centreon\Test\Behat\CentreonContext;

class PollerResourceConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    /**
     * @var string Field to validate that page is correclty loaded
     */
    protected $validField = 'input[name="searchR"]';

    /**
     * @var array Properties used to locate data on the list page
     */
    protected $properties = array(
        'resource_name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'resource_line' => array(
            'text',
            'td:nth-child(3)'
        ),
        'associated_pollers' => array(
            'text',
            'td:nth-child(4)'
        ),
        'resource_comment' => array(
            'text',
            'td:nth-child(5)'
        ),
        'enabled' => array(
            'custom'
        )
    );

    /**
     *  Navigate to the poller resource configuration listing page.
     *
     * @param CentreonContext $context Centreon context.
     * @param bool $visit Set to true to visit the pollers resources configuration list page.
     * @throws \Exception
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60904');
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
     * Get status of the poller resource
     *
     * @param Behat\Mink\Element\NodeElement $element
     * @return bool Returns true if the poller resource is enabled
     * @throws \Exception
     */
    protected function getEnabled($element)
    {
        $pollerResourceStatus = $this->context
            ->assertFindIn($element, 'css', 'td:nth-child(6)')
            ->getText();

        return ($pollerResourceStatus === 'ENABLED')
            ? true
            : false;
    }
}
