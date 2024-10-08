<?php
/*
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

namespace Centreon\Test\Behat\Administration;

/**
 * Class
 *
 * @class ExtensionsPage
 * @package Centreon\Test\Behat\Administration
 */
class ExtensionsPage
{
    const MODULE_TYPE = 'module';
    const WIDGET_TYPE = 'widget';

    private const INSTALL_CSS_SELECTOR = 'span[class*="MuiButton-startIcon"]';
    private const UPGRADE_CSS_SELECTOR = 'div[class*="MuiChip-deletable"][style="background-color:rgb(255,154,19)"]';
    private const REMOVE_CSS_SELECTOR = '[class*="MuiChip-deleteIcon"]';

    /** @var Centreon */
    protected $context;
    /** @var string */
    protected $validField = 'div[class*="content-wrapper"]';

    /**
     * ExtensionsPage constructor
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('administration/extensions/manager', false);
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
     *  Check that the current page is valid for this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', $this->validField);
    }

    /**
     * Get extension information
     *
     * @param string $type extension type (module/widget)
     * @param string $name extension name
     * @return array extension information
     */
    public function getEntry($type, $name): array
    {
        $entry = [
            'type' => $type,
            'name' => $name
        ];

        // check if extension exists
        $this->context->spin(
            function ($context) use ($type, $name) {
                return $context->getSession()->getPage()->has('css', '#' . $type . '-' . $name);
            },
            'Could not find ' . $type . ' ' . $name . '.'
        );
        $extension = $this->context->assertFind('css', '#' . $type . '-' . $name);

        // get available actions
        $entry['actions'] = [
            'install' => $extension->has('css', self::INSTALL_CSS_SELECTOR),
            'upgrade' => $extension->has('css', self::UPGRADE_CSS_SELECTOR),
            'remove' => $extension->has('css', self::REMOVE_CSS_SELECTOR),
        ];

        return $entry;
    }

    /**
     *  Install an extension.
     *
     * @param $type extension type
     * @param $name extension name
     * @throws \Exception
     * @return void
     */
    public function install($type, $name): void
    {
        $extension = $this->getEntry($type, $name);
        if ($extension['actions']['install']) {
            $extensionDOM = $this->context->assertFind('css', '#' . $type . '-' . $name);
            $this->context->assertFindIn($extensionDOM, 'css', self::INSTALL_CSS_SELECTOR)->click();

            // check if extension is properly installed
            $this->context->spin(
                function ($context) use ($type, $name) {
                    return $this->getEntry($type, $name)['actions']['remove'];
                },
                'Could not install ' . $type . ' ' . $name . '.',
                10
            );
        } else {
            throw new \Exception($type . ' ' . $name . ' is already installed.');
        }
    }

    /**
     *  Upgrade an extension.
     *
     * @param $type extension type
     * @param $name extension name
     * @throws \Exception
     * @return void
     */
    public function upgrade($type, $name): void
    {
        $extension = $this->getEntry($type, $name);
        if ($extension['actions']['upgrade']) {
            $extensionDOM = $this->context->assertFind('css', '#' . $type . '-' . $name);
            $this->context->assertFindIn($extensionDOM, 'css', self::UPGRADE_CSS_SELECTOR)->click();

            // check if extension is properly upgraded
            $this->context->spin(
                function ($context) use ($type, $name) {
                    return !$this->getEntry($type, $name)['actions']['upgrade'];
                },
                'Could not upgrade ' . $type . ' ' . $name . '.'
            );
        } else {
            throw new \Exception($type . ' ' . $name . ' cannot be upgraded.');
        }
    }

    /**
     *  Remove an extension.
     *
     * @param $type extension type
     * @param $name extension name
     * @throws \Exception
     * @return void
     */
    public function remove($type, $name): void
    {
        $extension = $this->getEntry($type, $name);
        if ($extension['actions']['remove']) {
            $extensionDOM = $this->context->assertFind('css', '#' . $type . '-' . $name);
            $this->context->assertFindIn($extensionDOM, 'css', self::REMOVE_CSS_SELECTOR)->click();

            // confirm popin
            $this->context->spin(
                function ($context) {
                    $this->context->assertFindButton('Delete')->click();
                    return true;
                },
                'Could not confirm remove of ' . $type . ' ' . $name . '.',
                3
            );

            // check if extension is properly removed
            $this->context->spin(
                function ($context) use ($type, $name) {
                    return $this->getEntry($type, $name)['actions']['install'];
                },
                'Could not remove ' . $type . ' ' . $name . '.',
                5
            );
        } else {
            throw new \Exception($type . ' ' . $name . ' cannot be removed.');
        }
    }
}
