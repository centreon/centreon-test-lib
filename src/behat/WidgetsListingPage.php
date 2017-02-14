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

namespace Centreon\Test\Behat;

class WidgetsListingPage implements ListingPage
{
    private $context;

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
            }
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page is matching this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'tr.ListHeader');
    }

    /**
     *  Get the list of templates.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            try {
                $this->context->assertFindIn($element, 'css', 'td:nth-child(1) a')->getText();
            } catch (\Exception $e) {
                continue;
            }
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(1)')->getText();
            $entry['description'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
            $entry['version'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['author'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['id'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getAttribute('id');
            $actionsComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)');
            if ($actionsComponent->has('css', 'img[src$="generate_conf.png"]')) {
                $entry['actions']['install'] = true;
            }
            if ($actionsComponent->has('css', 'img[src$="upgrade.png"]')) {
                $entry['actions']['upgrade'] = true;
            }
            if ($actionsComponent->has('css', 'img[src$="delete.png"]')) {
                $entry['actions']['remove'] = true;
            }
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }

    /**
     *  Get a widget.
     *
     * @param $widget  widget name.
     *
     * @return An array of properties.
     */
    public function getEntry($widget)
    {
        $widgets = $this->getEntries();
        if (!array_key_exists($widget, $widgets)) {
            throw new \Exception('could not find widget ' . $widget);
        }
        return $widgets[$widget];
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
     *  Upgrade a widget.
     *
     * @param $name  Widget name.
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
