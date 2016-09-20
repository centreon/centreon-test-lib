<?php
/**
 * Copyright 2016 Centreon
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

class DowntimeConfigurationListingPage implements ListingPage
{
    protected $context;

    /**
     *  Navigate to the downtime configuration listing page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to visit the downtime configuration
     *                   listing page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=210');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            5,
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that current page matches this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->assertFind('css', 'input[name="search_author"]');
    }

    /**
     *  Get downtime listing.
     *
     *  @return An array of downtimes.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            // Fetch entry.
            $entry = array();
            $entry['host'] = $this->context->assertFindIn(
                $element,
                'css',
                'td:nth-child(2)'
            )->getText();
            $entry['service'] = $this->context->assertFindIn(
                $element,
                'css',
                'td:nth-child(3)'
            )->getText();
            $entry['start'] = \DateTime::createFromFormat(
                'Y/m/d H:i',
                $this->context->assertFindIn(
                    $element,
                    'css',
                    'td:nth-child(4)'
                )->getText()
            )->getTimestamp();
            $entry['end'] = \DateTime::createFromFormat(
                'Y/m/d H:i',
                $this->context->assertFindIn(
                    $element,
                    'css',
                    'td:nth-child(5)'
                )->getText()
            )->getTimestamp();
            $entry['author'] = $this->context->assertFindIn(
                $element,
                'css',
                'td:nth-child(7)'
            )->getText();
            $entry['comment'] = $this->context->assertFindIn(
                $element,
                'css',
                'td:nth-child(8)'
            )->getText();

            // Store entry in result set.
            $entries[] = $entry;
        }
        return $entries;
    }

    /**
     *  Get a specific downtime.
     *
     *  @param $entry  Downtime entry number in the listing.
     *
     *  @return An array with the downtime properties.
     */
    public function getEntry($entry)
    {
        $downtimes = $this->getEntries();
        if (!array_key_exists($entry, $downtimes)) {
            throw new \Exception('Downtime entry ' . $entry . ' does not exist');
        }
        return $downtimes[$entry];
    }

    /**
     *  Throw an exception. Downtimes are not inspectable.
     *
     *  @param $entry  Downtime entry number in the listing.
     */
    public function inspect($entry)
    {
        throw new \Exception('Downtimes are not inspectable');
    }

    /**
     *  Select an entry.
     *
     *  @param $entry   Downtime entry number in the listing.
     *  @param $select  True to select entry, false to unselect.
     */
    public function selectEntry($entry, $select = true)
    {
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        if (!array_key_exists($entry, $elements)) {
            throw new \Exception('Downtime entry ' . $entry . ' does not exist');
        }
        $checkbox = $this->context->assertFindIn($elements[$entry], 'css', 'input[type="checkbox"]');
        if ($select) {
            $checkbox->check();
        } else {
            $checkbox->uncheck();
        }
    }

    /**
     *  Cancel selected downtimes.
     */
    public function cancel()
    {
        $this->context->setConfirmBox(true);
        $this->context->assertFind('css', 'select[name="o1"]')->setValue('cs');
    }
}
