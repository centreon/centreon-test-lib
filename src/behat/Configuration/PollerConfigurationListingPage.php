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

class PollerConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    const ACTION_DUPLICATE = 'm';
    const ACTION_DELETE = 'd';
    const ACTION_UPDATE = 'i';

    protected $validField = 'input[name="searchP"]';

    protected $properties = [
        'name' => [
            'text',
            'td:nth-child(2)'
        ],
        'id' => [
            'custom'
        ],
    ];

    protected $objectClass = '\Centreon\Test\Behat\Configuration\PollerConfigurationPage';

    /**
     *  Navigate to the poller configuration listing page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to the page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=609');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Enable or disable a poller.
     *
     * @param $poller  Poller name.
     * @param $enable  True to enable, false to disable.
     */
    public function enableEntry($poller, $enable = true)
    {
        $label = $enable ? 'Enabled' : 'Disabled';
        $this->context->assertFind(
            'xpath',
            "//a[text()='" . $poller .
            "']/../../td/a/img[@alt='" . $label . "']/.."
        )->click();
    }

    /**
     *  Select or unselect a poller.
     *
     * @param $pollerName  Poller name.
     * @param $select  True to check, false to uncheck.
     */
    public function selectEntry($pollerName, $select = true)
    {
        $poller = $this->getEntry($pollerName);
        $checkbox = $this->context->assertFind('css', 'input[type="checkbox"][name="select[' . $poller['id'] . ']"]');

        if ($select) {
            $this->checkCheckbox($checkbox);
        } else {
            $this->uncheckCheckbox($checkbox);
        }
    }

    /**
     *  Go to the configuration export page.
     *
     * @return New PollerConfigurationExportPage.
     */
    public function exportConfiguration()
    {
        $this->context->assertFind('css', 'input[name="apply_configuration"]')->click();
        return new PollerConfigurationExportPage($this->context, false);
    }

    /**
     *  Do some action.
     *
     *  Beware, you might need to call setConfirmBox(true) to use this
     *  method effectively.
     *
     * @param $action  The action to perform on selected elements.
     */
    public function moreActions($action)
    {
        $this->context->assertFind('css', 'select[name="o1"]')->setValue($action);
    }

    /**
     * Get poller id
     *
     * @param \Behat\Mink\Element\NodeElement $element
     */
    protected function getId($element)
    {
        $idComponent = $this->context->assertFindIn($element, 'css', 'input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }
}
