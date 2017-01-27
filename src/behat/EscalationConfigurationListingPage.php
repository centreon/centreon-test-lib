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

class EscalationConfigurationListingPage implements ListingPage
{
    protected $context;

    /**
     *  Escalation list page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to the default escalation list page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60401');
        }

        // Check that page is valid for this class.
        $this->waitForValidPage();
    }

    /**
     *  Check that the current page matches this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="searchC"]');
    }

    /**
     *  Wait until page is valid or timeout occurs.
     */
    public function waitForValidPage()
    {
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
     *  Get the list of escalations.
     */
    public function getEntries()
    {
        // Go to first page.
        $paginationLinks = $this->context->getSession()->getPage()->findAll('css', '.ToolbarPagination a');
        foreach ($paginationLinks as $pageLink) {
            if ($pageLink->getText() == '1') {
                $pageLink->click();
                $this->waitForValidPage();
            }
        }

        // Browse all pages to find all escalations.
        $entries = array();
        while (true) {
            $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
            foreach ($elements as $element) {
                $entry = array();
                $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
                $entry['escalation_line'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
                $entries[$entry['name']] = $entry;
            }

            // Go to next page or break.
            $nextLink = $this->context->getSession()->getPage()->find(
                'css',
                '.ToolbarPagination a img[title="Next page"]'
            );
            if (is_null($nextLink)) {
                break ;
            } else {
                $nextLink->click();
                $this->waitForValidPage();
            }
        }
        return $entries;
    }

    /**
     *  Get a escalation.
     *
     *  @param $escname  Escalation name.
     *
     *  @return An array of properties.
     */
    public function getEntry($escname)
    {
        $escalations = $this->getEntries();
        if (!array_key_exists($escname, $escalations)) {
            throw new \Exception('could not find escalation ' . $escname);
        }
        return $escalations[$escname];
    }

    /**
     *  Edit a escalation.
     *
     *  @param $escalation  Escalation name.
     */
    public function inspect($escalation)
    {
        $this->context->assertFindLink($escalation)->click();
        return new EscalationConfigurationPage($this, false);
    }

    /**
     *  Set the escalation filter.
     *
     *  @param string $escalationName Escalation name to select.
     */
    public function setEscalationFilter($escalationName)
    {
        $filterField = $this->context->assertFind('named', array('id_or_name', 'searchC'));
        $filterField->setValue($escalationName);
    }

    /**
     *  Search with the escalation filter.
     */
    public function search()
    {
        $this->context->assertFindButton('Search', 'Button Search not found')->click();
    }

    /**
     *  Set max escalation displayed in escalations list.
     *
     *  @param string $limit  The value of limit in page limit dropdown.
     */
    public function setListingLimit($limit)
    {
        $page = $this->context->getSession()->getPage();
        $toolbar_pagelimit = $page->find('css', '.Toolbar_pagelimit');
        $toolbar_pagelimit->selectFieldOption('l', $limit);
    }
}
