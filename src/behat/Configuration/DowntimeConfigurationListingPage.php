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

class DowntimeConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="search_author"]';

    protected $properties = array(
        'service' => array(
            'text',
            'td:nth-child(3)'
        ),
        'host' => array(
            'text',
            'td:nth-child(2)'
        ),
        'start' => array(
            'text',
            'td:nth-child(4)'
        ),
        'end' => array(
            'text',
            'td:nth-child(5)'
        ),
        'author' => array(
            'text',
            'td:nth-child(7)'
        ),
        'comment' => array(
            'text',
            'td:nth-child(8)'
        ),
        'started' => array(
            'text',
            'td:nth-child(9)'
        )
    );

    /**
     *  Navigate to the downtime configuration listing page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to visit the downtime configuration
     *                   listing page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=21001');
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
     *  Get downtime listing.
     *
     * @return An array of downtimes.
     */
    public function getEntries()
    {
        $finalEntries = array();

        $entries = parent::getEntries();
        foreach ($entries as $entry) {
            $entry['started'] = ($entry['started'] == 'Yes') ? true : false;
            $finalEntries[] = $entry;
        }

        return $finalEntries;
    }

    /**
     *  display downtime cycle.
     *
     */
    public function displayDowntimeCycle(): void
    {
        $checkbox = $this->context->assertFind('css', 'input[name="view_downtime_cycle"]');
        $this->checkCheckbox($checkbox);
        $this->context->assertFindButton('SearchB')->click();
    }

    /**
     * Select a downtime
     *
     * @param $entry
     * @param bool $select
     * @throws \Exception
     */
    public function selectEntry($entry, $select = true): void
    {
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        if (!array_key_exists($entry, $elements)) {
            throw new \Exception('Downtime entry ' . $entry . ' does not exist');
        }
        $checkbox = $this->context->assertFindIn($elements[$entry], 'css', 'input[type="checkbox"]');
        if ($select) {
            $this->checkCheckbox($checkbox);
        } else {
            $this->uncheckCheckbox($checkbox);
        }
    }

    /**
     *  Cancel selected downtimes.
     */
    public function cancel(): void
    {
        $this->context->setConfirmBox(true);
        $this->context->assertFind('css', 'input[name="submit2"]')->click();
    }
}
