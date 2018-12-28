<?php
/**
 * Copyright 2017 Centreon
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

class MetricsConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchVM"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(3)'
        ),
        'id' => array(
            'custom'
        ),
        'function' => array(
            'text',
            'td:nth-child(5)'
        ),
        'def_type' => array(
            'text',
            'td:nth-child(7)'
        ),
        'hidden' => array(
            'text',
            'td:nth-child(8)'
        ),
        'status' => array(
            'text',
            'td:nth-child(9)'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Monitoring\MetricsConfigurationPage';

    /**
     *  Contact group listing page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=20408');
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
    public function getId($element)
    {
        $idComponent =$this->context->assertFindIn($element,'css','input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }

    /**
     *  Set the virtual metrics filter.
     *
     * @param string $virtualMetricsName Virtual metrics filter.
     */
    public function setVirtualMetricsFilter($virtualMetricsName)
    {
        $filterField = $this->context->assertFind('named', array('id_or_name', 'searchVM'));
        $filterField->setValue($virtualMetricsName);
    }

    /**
     *  Search with the virtual metrics filter.
     */
    public function search()
    {
        $this->context->assertFindButton('Search', 'Button Search not found')->click();
    }
}
