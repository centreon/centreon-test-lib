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

class CommandListPage
{
    protected $context;

    /**
     *  Command list page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to the default command list page.
     */
    public function __construct($context, $visit = TRUE)
    {
        $this->context = $context;

        if ($visit) {
            $this->context->visit('main.php?p=608');
        }
    }

    /**
     * Set the filter command
     *
     * @param string commandName Command name to select.
     */
    public function setFilterByCommand($commandName)
    {
        // Set filter
        $filterField = $this->context->assertFind('named', array('id_or_name', 'searchC'));
        $filterField->setValue(trim($commandName));

        // Apply filter
        $this->context->assertFindButton('Tmainpage', 'Button Search not found')->click();
    }

    /**
     * Put max command display in commands list to $limit
     *
     * @param string limit The value of limit in page limit dropdown
     */
    public function setPageLimitTo($limit)
    {
        $page = $this->context->getSession()->getPage();

        $toolbar_pagelimit = $page->find('css', '.Toolbar_pagelimit');
        $toolbar_pagelimit->selectFieldOption('l', $limit);
    }

    /**
     * Wait command(s) list page
     */
    public function waitForCommandListPage()
    {
        $this->context->spin(function ($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'searchC'));
        });
    }

    /**
     * Check if the command is in commands list or not
     *
     * @param string commandName Command name to check.
     * @return bool
     */
    public function isCommandExist($commandName)
    {

        $this->waitForCommandListPage();
        $this->setPageLimitTo(100);
        $this->setFilterByCommand($commandName);
        
        $XPath = "//*[@class='ListTable']/tr/td[2]/a[text()='".addslashes($commandName)."']/../..";

        $page = $this->context->getSession()->getPage();
        $linesWithCommandName = $page->findAll('xpath', $XPath);

        if (count($linesWithCommandName)) {
            return true;
        }

        return false;
    }

}
