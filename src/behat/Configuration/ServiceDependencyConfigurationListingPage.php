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

class ServiceDependencyConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchSD"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'description' => array(
            'text',
            'td:nth-child(3)'
        ),
        'id' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\ServiceDependencyConfigurationPage';

    /**
     *  Contact list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60409');
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
     * Get id
     */
    protected function getId($element)
    {
        $idComponent = $this->context->assertFindIn($element, 'css', 'input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }
}
