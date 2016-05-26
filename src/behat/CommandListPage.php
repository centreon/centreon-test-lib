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
    protected $ctx;

    /**
     * Constructor
     *
     * @param array $context A CentreonContext
     */
    public function __construct($context)
    {
        $this->ctx = $context;
    }

    /**
     * Set the filter command
     *
     * @param string commandName Command name to select.
     */
    public function setFilterByCommand($commandName)
    {
        $this->ctx->assertFind('named', array('id_or_name', 'searchC'))->setValue(trim($commandName));

        $mainpage = $this->ctx->assertFind('named', array('id', 'Tmainpage'));
        $this->ctx->assertFindButton($mainpage, 'Search')->click();
    }

    /**
     * Put max command display in commands list to $limit
     *
     * @param string limit The value of limit in page limit dropdown
     */
    public function setPageLimitTo($limit)
    {
        $page = $this->ctx->getSession()->getPage();

        $toolbar_pagelimit = $page->find('css', '.Toolbar_pagelimit');
        $toolbar_pagelimit->selectFieldOption('l', $limit);
    }

    /**
     * Wait command(s) list page
     */
    public function waitForCommandListPage()
    {
        $this->ctx->spin(function ($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'searchC'));
        });
    }

    public function listCommands()
    {
        // Go to : Configuration > Commands > Checks
        $this->ctx->visit('main.php?p=608');
        $this->waitForCommandListPage();
    }

    /**
     * Check if the command is in commands list or not
     *
     * @param string commandName Command name to check.
     * @return bool
     */
    public function isCommandExist($commandName)
    {

        $this->listCommands();
        //$this->setPageLimitTo(100);
        $this->setFilterByCommand($commandName);

        $page = $this->ctx->getSession()->getPage();

        $linesWithCommandName = $page->findAll('xpath', "//*[@class='ListTable']/tr/td[2]/a[text()='".$commandName."']/../..");

        if (count($linesWithCommandName)) {
            return true;
        }
        return false;
    }

}
