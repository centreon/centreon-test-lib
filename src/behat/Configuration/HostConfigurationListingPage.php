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

class HostConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchH"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'id' => array(
            'custom'
        ),
        'alias' => array(
            'text',
            'td:nth-child(4)'
        ),
        'ip_address' => array(
            'text',
            'td:nth-child(5)'
        ),
        'poller' => array(
            'text',
            'td:nth-child(6)'
        ),
        'parents' => array(
            'custom',
            'parents'
        ),
        'enabled' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\HostConfigurationPage';

    /**
     *  Host template list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60101');
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

    /**
     * Get enabled
     */
    protected function getEnabled($element)
    {
        return $this->context->assertFindIn($element, 'css', 'td:nth-child(8)')->getText() == 'ENABLED' ?
            true : false;
    }

    /**
     * Get parent templates
     *
     * @param $element
     * @return array
     */
    protected function getParents($element)
    {
        $parents = $this->context->assertFindIn($element, 'css', 'td:nth-child(7)')->getText();
        $parents = explode(' ', str_replace('| ', '', $parents));

        return $parents;
    }

    /**
     *  Get a search.
     */
    public function setSearch($search)
    {
        $this->context->assertFind('css', 'input[name="searchH"]')->setValue($search);
        $this->context->assertFind('css', 'tbody tr td input.btc.bt_success')->click();
    }

    /**
     *  Get the search.
     */
    public function getSearch()
    {
        $search = $this->context->assertFind('css', 'input[name="searchH"]')->getValue();
        if (!isset($search)) {
            throw new \Exception('could not find host search');
        }
        return $search;
    }


    /**
     *  Del an host.
     */
    public function delHost($hostId)
    {
        $this->context->setConfirmBox(true);
        $this->context->assertFind('css', 'input[name="select[' . $hostId . ']"]')->click();
        $this->context->assertFind('css', 'select[name="o1"]')->selectOption('Delete');
    }
}
