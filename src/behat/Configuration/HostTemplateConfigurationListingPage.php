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

class HostTemplateConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchHT"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'id' => array(
            'custom'
        ),
        'description' => array(
            'text',
            'td:nth-child(3)'
        ),
        'linked_services' => array(
            'text',
            'td:nth-child(4)'
        ),
        'parents' => array(
            'custom'
        ),
        'locked' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\HostTemplateConfigurationPage';

    /**
     *  Host template list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60103');
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
     * @param $element
     * @return null
     */
    protected function getId($element)
    {
        $idComponent = $this->context->assertFindIn($element, 'css', 'input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;

        return $id;
    }

    /**
     * Get parent templates
     *
     * @param $element
     * @return array
     */
    protected function getParents($element)
    {
        $parents = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
        $parents = explode(' ', str_replace('| ', '', $parents));

        return $parents;
    }

    /**
     * @param $element
     * @return bool
     */
    protected function getLocked($element)
    {
        return (null === $element->find('css', 'input:nth-child(2)'));
    }

    /**
     *  Get a search.
     */
    public function setSearch($search)
    {
        $this->context->assertFind('css', 'input[name="searchHT"]')->setValue($search);
        $this->context->assertFind('css', 'tbody tr td input.btc.bt_success')->click();
    }

    /**
     * Check/uncheck "locked elements" filter and run search
     *
     * @param boolean $displayLocked state to apply on the checkbox
     * @return void
     */
    public function setLockedElementsFilter(bool $displayLocked): void
    {
        $checkbox = $this->context->assertFind('css', 'input[name="displayLocked"]');
        $displayLocked ? $this->checkCheckbox($checkbox) : $this->uncheckCheckbox($checkbox);
        $this->context->assertFind('css', 'tbody tr td input.btc.bt_success')->click();
    }

    /**
     *  Get the search.
     */
    public function getSearch()
    {
        $search = $this->context->assertFind('css', 'input[name="searchHT"]')->getValue();
        if (!isset($search)) {
            throw new \Exception('could not find host template search');
        }
        return $search;
    }

    /**
     *  Del an host.
     */
    public function delHostTemplate($hostTemplateName)
    {
        $this->context->setConfirmBox(true);

        $hostTemplates = $this->getEntries();
        $this->context->assertFind(
            'css',
            'input[name="select[' . $hostTemplates[$hostTemplateName]['id'] . ']"]'
        )->click();
        $this->context->assertFind('css', 'select[name="o1"]')->selectOption('Delete');
    }
}
