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

class CommandConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    const TYPE_CHECK = 2;
    const TYPE_NOTIFICATION = 1;
    const TYPE_DISCOVERY = 4;
    const TYPE_MISC = 3;

    protected $validField = 'input[name="searchC"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'command_line' => array(
            'text',
            'td:nth-child(3)'
        ),
        'type' => array(
            'text',
            'td:nth-child(4)'
        ),
        'enabled' => array(
            'custom'
        ),
        'id' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\CommandConfigurationPage';

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
            switch ($type) {
                case 1:
                    $this->context->visit('main.php?p=60802&type=1');
                    break;
                case 2:
                    $this->context->visit('main.php?p=60801&type=2');
                    break;
                case 3:
                    $this->context->visit('main.php?p=60803&type=3');
                    break;
                case 4:
                    $this->context->visit('main.php?p=60807&type=4');
                    break;
            }
        }

        // Check that page is valid for this class.
        $this->waitForValidPage();
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

    /**
     * Get id
     */
    protected function getId($element)
    {
        $idComponent =$this->context->assertFindIn($element,'css','input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }

    /**
     * Get enabled
     */
    protected function getEnabled($element)
    {
        return $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText() == 'ENABLED' ?
            true : false;
    }
}
