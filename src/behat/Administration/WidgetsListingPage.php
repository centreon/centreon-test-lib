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

namespace Centreon\Test\Behat\Administration;

class WidgetsListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'tr.ListHeader';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(1)'
        ),
        'description' => array(
            'text',
            'td:nth-child(2)'
        ),
        'version' => array(
            'text',
            'td:nth-child(3)'
        ),
        'author' => array(
            'text',
            'td:nth-child(4)'
        ),
        'id' => array(
            'attribute',
            'td:nth-child(5)',
            'id'
        ),
        'actions' => array(
            'custom',
            'actions'
        )
    );

    /**
     *  Widget list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50703');
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
     * Validate entry integrity
     *
     * @param $element
     * @return bool
     */
    public function validateEntry($element)
    {
        $valid = true;
        try {
            $this->context->assertFindIn($element, 'css', 'td:nth-child(1) a')->getText();
        } catch (\Exception $e) {
            $valid = false;
        }

        return $valid;
    }

    /**
     * Get list of actions
     *
     * @param $element
     * @return array
     */
    public function getActions($element)
    {
        $actions = array(
            'install' => false,
            'upgrade' => false,
            'remove' => false
        );

        $actionsComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(9)');
        if ($actionsComponent->has('css', 'img[src$="generate_conf.png"]')) {
            $actions['install'] = true;
        }
        if ($actionsComponent->has('css', 'img[src$="upgrade.png"]')) {
            $actions['upgrade'] = true;
        }
        if ($actionsComponent->has('css', 'img[src$="delete.png"]')) {
            $actions['remove'] = true;
        }

        return $actions;
    }

    /**
     *  Install a widget.
     *
     * @param $name  Widget name.
     * @throws \Exception
     */
    public function install($name)
    {

        $mythis = $this;
        $i = 0;
        $widget = $this->getEntry($name);

        $widget['id'];

        if ($widget['actions']['install']) {

            $widgetInstallImg = $this->context->assertFind('css', '#'. $widget['id'] . ' .installBtn ');
            $widgetInstallImg->click();

            // Install widget.
            $this->context->spin(
                function ($context) use ($mythis) {
                    return $mythis->context->getSession()->getPage()->has('css', 'input[name="install"]');
                },
                'Could not install widget ' . $name . '.'
            );

            $validInstallImg = $this->context->assertFind('css', 'input[name="install"]');
            $validInstallImg->click();

            // Back.
            $this->context->spin(
                function ($context) use ($mythis) {
                    return $mythis->context->getSession()->getPage()->has('css', 'input[name="list"]');
                },
                'Could not go back after installation of widget ' . $name . '.'
            );

            $validInstallImg = $this->context->assertFind('css', 'input[name="list"]');
            $validInstallImg->click();
        } else {
            throw new \Exception('Widget ' . $name . ' is already installed.');
        }
    }

    /**
     * Upgrade a widget
     *
     * @param $name
     * @throws \Exception
     */
    public function upgrade($name)
    {
        $mythis = $this;
        $i = 0;
        $widget = $this->getEntry($name);
        while ($widget['actions']['upgrade']) {
            $i++;
            if ($widget['actions']['upgrade']) {
                $widgetUpgradeImg = $this->context->assertFind('css', '#'. $widget['id'] . ' .installBtn ');;
                $widgetUpgradeImg->click();

                // Update.
                $this->context->spin(
                    function ($context) use ($mythis) {
                        return $this->context->getSession()->getPage()->has('css', 'input[name="upgrade"]');
                    },
                    'Could not upgrade widget ' . $name . '.'
                );

                $validUpgradeImg = $this->context->assertFind('css', 'input[name="upgrade"]');
                $validUpgradeImg->click();

                // Back.
                $this->context->spin(
                    function ($context) use ($mythis) {
                        return $this->context->getSession()->getPage()->has('css', 'input[name="list"]');
                    },
                    'Could not go back after upgrade of widget ' . $name . '.'
                );

                $validUpgradeImg = $this->context->assertFind('css', 'input[name="list"]');
                $validUpgradeImg->click();

            } else {
                throw new \Exception('Cannot upgrade the module : ' . $name);
            }
            $widget = $this->getEntry($name);
        }
    }
}
