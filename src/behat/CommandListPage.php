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
        $this->context->assertFindButton('Search', 'Button Search not found')->click();
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
     *  Get the list of command.
     */
    public function getCommands()
    {
        $entries = array();

        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');

        foreach ($elements as $element) {
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
            /*
            $entry['command_line'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['type'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['parents'] = explode(' ', $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText());
            $entry['host_uses'] = (null === $element->find('css', 'input:nth-child(2)'));
            $entry['options'] = (null === $element->find('css', 'input:nth-child(2)'));
            */

            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }

    /**
     *  Get command properties.
     *
     *  @param $name  Command name.
     */
    public function getCommand($name)
    {
        $templates = $this->getCommands();
        if (!array_key_exists($name, $templates)) {
            throw new \Exception('Cannot find command "' . $name . '".');
        }
        return $templates[$name];
    }

}
