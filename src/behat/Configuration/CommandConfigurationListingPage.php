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

class CommandConfigurationListingPage implements ListingPage
{
    const TYPE_CHECK = 2;
    const TYPE_NOTIFICATION = 1;
    const TYPE_DISCOVERY = 4;
    const TYPE_MISC = 3;

    protected $context;

    /**
     *  Command list page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to the default command list page.
     *  @param $type     Command type if visit is enabled. Default to
     *                   TYPE_CHECK.
     */
    public function __construct($context, $visit = true, $type = self::TYPE_CHECK)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=608');
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
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Get the list of commands.
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

        // Browse all pages to find all commands.
        $entries = array();
        while (true) {
            $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
            foreach ($elements as $element) {
                $entry = array();
                $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
                $entry['command_line'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
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
     *  Get a command.
     *
     *  @param $cmdname  Command name.
     *
     *  @return An array of properties.
     */
    public function getEntry($cmdname)
    {
        $commands = $this->getEntries();
        if (!array_key_exists($cmdname, $commands)) {
            throw new \Exception('could not find command ' . $cmdname);
        }
        return $commands[$cmdname];
    }

    /**
     *  Edit a command.
     *
     *  @param $command  Command name.
     */
    public function inspect($command)
    {
        $this->context->assertFindLink($command)->click();
        return new CommandConfigurationPage($this, false);
    }

    /**
     *  Set the command filter.
     *
     *  @param string $commandName Command name to select.
     */
    public function setCommandFilter($commandName)
    {
        $filterField = $this->context->assertFind('named', array('id_or_name', 'searchC'));
        $filterField->setValue($commandName);
    }

    /**
     *  Search with the command filter.
     */
    public function search()
    {
        $this->context->assertFindButton('Search', 'Button Search not found')->click();
    }

    /**
     *  Set max command displayed in commands list.
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
