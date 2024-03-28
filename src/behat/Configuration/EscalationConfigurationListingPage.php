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

class EscalationConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchE"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'escalation_line' => array(
            'text',
            'td:nth-child(3)'
        ),
        'id' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\EscalationConfigurationPage';

    /**
     *  Escalation list page.
     *
     * @param $context  Centreon context object.
     * @param $visit    True to navigate to the default escalation list page.
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
     *  Wait until page is valid or timeout occurs.
     */
    public function waitForValidPage(): void
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
     * Get id
     */
    protected function getId($element)
    {
        $idComponent = $this->context->assertFindIn($element, 'css', 'input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }

    /**
     *  Set the escalation filter.
     *
     * @param string $escalationName Escalation name to select.
     */
    public function setEscalationFilter($escalationName): void
    {
        $filterField = $this->context->assertFind('named', array('id_or_name', 'searchC'));
        $filterField->setValue($escalationName);
    }

    /**
     *  Search with the escalation filter.
     */
    public function search(): void
    {
        $this->context->assertFindButton('Search', 'Button Search not found')->click();
    }

    /**
     *  Set max escalation displayed in escalations list.
     *
     * @param string $limit The value of limit in page limit dropdown.
     */
    public function setListingLimit($limit): void
    {
        $page = $this->context->getSession()->getPage();
        $toolbar_pagelimit = $page->find('css', '.Toolbar_pagelimit');
        $toolbar_pagelimit->selectFieldOption('l', $limit);
    }
}
