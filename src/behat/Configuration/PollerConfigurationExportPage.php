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

class PollerConfigurationExportPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const METHOD_RELOAD = 1;
    const METHOD_RESTART = 2;

    protected $validField = '#nrestart_mode';

    protected $properties = array(
        'pollers' => array('custom', 'Pollers'),
        'generate_files' => array('checkbox', 'input[name="gen"]'),
        'run_debug' => array('checkbox', 'input[name="debug"]'),
        'move_files' => array('checkbox', 'input[name="move"]'),
        'restart_engine' => array('checkbox', 'input[name="restart"]'),
        'restart_method' => array('select', 'select[name="restart_mode"]')
    );

    /**
     *  Constructor.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to visit the poller configuration export
     *                   page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60902&poller=1');
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
     *  Export configuration.
     */
    public function export()
    {
        $this->context->assertFind('css', '#exportBtn')->click();
        $this->context->spin(
            function($context) {
                return (
                    $context->getSession()->getPage()->has(
                        'named',
                        array('id', 'progressPct')) &&
                    $context->getSession()->getPage()->find(
                        'named',
                        array('id', 'progressPct')
                    )->getText() == '100%'
                );
            }
        );
    }

    /**
     *  Set pollers.
     *
     *  @param $pollers  Array of pollers.
     */
    public function setPollers($pollers)
    {
        if (!is_array($pollers)) {
            $pollers = array($pollers);
        }
        foreach ($pollers as $poller) {
            if ('all' == $poller) {
                $this->context->assertFind('css', '.select2-search__field')->click();
                $this->context->assertFindButton('Select all')->click();
                $this->context->assertFind('css', '.popin-wrapper .button_group_center .btc.bt_success')->click();
            } else {
                $this->context->selectToSelectTwo('select#nhost', $poller);
            }
        }
    }
}
