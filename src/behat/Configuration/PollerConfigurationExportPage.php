<?php
/**
 * Copyright 2016-2019 Centreon
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

use Centreon\Test\Behat\Exception\SpinStopException;
use Centreon\Test\Behat\ConfigurationPage;

class PollerConfigurationExportPage extends ConfigurationPage
{

    const METHOD_RELOAD = 'Reload';
    const METHOD_RESTART = 'Restart';

    protected $validField = '#nrestart_mode';
    protected $properties = [
        'pollers' => [
            'custom',
            'Pollers',
        ],
        'generate_files' => [
            'checkbox',
            'input[name="gen"]',
        ],
        'run_debug' => [
            'checkbox',
            'input[name="debug"]',
        ],
        'move_files' => [
            'checkbox',
            'input[name="move"]',
        ],
        'restart_engine' => [
            'checkbox',
            'input[name="restart"]',
        ],
        'restart_method' => [
            'select',
            'select[name="restart_mode"]'
        ],
    ];

    /**
     *  Constructor.
     *
     * @param $context  Centreon context.
     * @param $visit    True to visit the poller configuration export
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
        $this->context->spin(
            function () {
                return $this->isPageValid();
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

        // wait for result
        $this->context->spin(function ($context) {
            $page = $context->getSession()->getPage();

            if ($page->find('xpath', '//*[@id="consoleContent"]//font[contains(@color, "red") and .="NOK"]')) {
                throw new SpinStopException(
                    "The export of pollers was unsuccessful:\n\n"
                    . strip_tags(str_replace('<br>', "\n", $page->find('xpath', '//*[@id="debug_1"]')->getHtml()))
                );
            }

            $elementProgressBar = $page->find('named', ['id', 'progressPct']);

            return ($elementProgressBar && $elementProgressBar->getText() == '100%');
        });
    }

    /**
     *  Set pollers.
     *
     * @param $pollers  Array of pollers.
     */
    public function setPollers($pollers)
    {
        $pollers = !is_array($pollers) ? [$pollers] : $pollers;

        foreach ($pollers as $poller) {
            if ('all' == $poller) {
                $this->context->assertFind('css', '.select2-results-header__select-all > button')->press();
                $this->context->spin(
                    function () {
                        return $this->context->getSession()->getPage()->has('css', '.centreon-popin .popin-wrapper');
                    }
                );

                $this->context->assertFind('css', '.popin-wrapper .button_group_center .btc.bt_success')->click();
                $this->context->spin(
                    function () {
                        return count(
                            $this->context->getSession()->getPage()->findAll(
                                'css',
                                '.select2-container--open li.select2-results__option'
                            )
                        ) == 0;
                    }
                );
            } else {
                $this->context->selectToSelectTwo('select#nhost', $poller);
            }
        }
    }
}
