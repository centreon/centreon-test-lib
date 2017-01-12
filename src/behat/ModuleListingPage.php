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

class ModuleListingPage implements ListingPage
{
    private $context;

    /**
     *  Module list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50701');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(function ($context) use ($mythis) {
            return $mythis->isPageValid();
        },
            5,
            'Current page does not match class ' . __CLASS__);
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
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(1) a')->getText();
            $entry['realname'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2) a')->getText();
            $entry['description'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['version'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['author'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
            $entry['expirationDate'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(6)')->getText();
            $entry['installed'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(7)')->getText();
            $entry['actions'] = array(
                'install' => false,
                'upgrade' => false,
                'remove' => false
            );
            $actionsComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(9)');
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
     *  Get a module.
     *
     * @param $module  Module name.
     *
     * @return An array of properties.
     */
    public function getEntry($module)
    {
        $modules = $this->getEntries();
        if (!array_key_exists($module, $modules)) {
            throw new \Exception('could not find module ' . $module);
        }
        return $modules[$module];
    }

    /**
     *  Edit a module.
     *
     * @param $name  Module name.
     */
    public function inspect($name)
    {
        throw new \Exception('Cannot inspect a module.');
    }


    /**
     *  Upgrade a module.
     *
     * @param $name  Module name.
     */
    public function upgrade($name)
    {
        $mythis = $this;
        $i = 0;
        $module = $this->getEntry($name);
        while ($module['actions']['upgrade']) {
            $i++;
            if ($module['actions']['upgrade']) {
                $moduleUpgradeImg = $this->context->assertFind('css', '#action' . $name . ' img[title="Upgrade"]');
                $moduleUpgradeImg->click();

                //update
                $this->context->spin(
                    function ($context) use ($mythis) {
                        return $this->context->getSession()->getPage()->has('css', 'input[name="upgrade"]');
                    },
                    5,
                    'Current page does not match'
                );

                $validUpgradeImg = $this->context->assertFind('css', 'input[name="upgrade"]');
                $validUpgradeImg->click();

                //back
                $this->context->spin(
                    function ($context) use ($mythis) {
                        return !$this->context->getSession()->getPage()->has('css', 'input[name="upgrade"]');
                    },
                    20,
                    'Current page does not match'
                );

                $this->context->spin(
                    function ($context) use ($mythis) {
                        return $this->context->getSession()->getPage()->has('css', 'input[name="list"]');
                    },
                    60,
                    'Current page does not match'
                );

                $validUpgradeImg = $this->context->assertFind('css', 'input[name="list"]');
                $validUpgradeImg->click();

            } else {
                throw new \Exception('Cannot upgrade the module : ' . $name);
            }
            $module = $this->getEntry($name);
        }
    }
}
