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

namespace Centreon\Test\Behat\Configuration;

class KBServiceListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchHasNoProcedure"]';

    protected $properties = array();

    /**
     *  Navigate to and/or check that we are on the KB service listing
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to visit the page, false if already on it.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=61002');
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
     * Create wiki page
     *
     * @param $hostservice
     * @throws \Exception
     */
    public function createWikiPage($hostservice)
    {
        $entries = $this->context->getSession()->getPage()->findAll(
            'css',
            '.ListTable .list_one,.list_two'
        );
        foreach ($entries as $entry) {
            $host = trim($this->context->assertFindIn(
                $entry,
                'css',
                'td:nth-child(2)'
            )->getText());
            $service = trim($this->context->assertFindIn(
                $entry,
                'css',
                'td:nth-child(3)'
            )->getText());
            if (($hostservice['host'] == $host) && ($hostservice['service'] == $service)) {
                $this->context->assertFindIn(
                    $entry,
                    'css',
                    'td:nth-child(6) a'
                )->click();
                return;
            }
        }

        // No matching entry found, throw.
        throw new \Exception(
            'Service ' . $hostservice['service'] . ' of host ' .
            $hostservice['host'] . ' could not be found.'
        );
    }
}
