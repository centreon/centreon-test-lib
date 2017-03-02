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

namespace Centreon\Test\Behat;

class SnmpTrapsConfigurationListingPage implements ListingPage
{
    private $context;

    /**
     *  SNMP traps list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=61701');
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
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="searchT"]');
    }

    /**
     *  Get the list of traps.
     */
    public function getEntries()
    {

    }

    /**
     *  Get a traps.
     *
     *  @param $tmpl traps name.
     *
     *  @return An array of properties.
     */
    public function getEntry($tmpl)
    {

    }

    /**
     *  Edit a traps.
     *
     *  @param $name  trap name.
     */
    public function inspect($name)
    {

    }

    /**
     *  Set a search.
     */
    public function setSearch($search)
    {
        $this->context->assertFind('css', 'input[name="searchT"]')->setValue($search);
        $this->context->assertFind('css', 'tbody tr td input.btc.bt_success')->click();
    }

    /**
     *  Get the search.
     */
    public function getSearch()
    {
        $search =  $this->context->assertFind('css', 'input[name="searchT"]')->getValue();
        if (!isset($search)) {
            throw new \Exception('could not find host template search');
        }
        return $search;
    }
}
