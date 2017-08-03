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

class CurrentUserConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_INFORMATION = 1;
    const TAB_NOTIFICATION = 2;

    protected $validField = 'input[name="contact_name"]';

    protected $properties = array(
        'alias' => array(
            'input',
            'input[name="contact_alias"]',
            self::TAB_INFORMATION
        ),
        'name' => array(
            'input',
            'input[name="contact_name"]',
            self::TAB_INFORMATION
        ),
        'email' => array(
            'input',
            'input[name="contact_email"]',
            self::TAB_INFORMATION
        ),
        'location' => array(
            'select2',
            'select#contact_location',
            self::TAB_INFORMATION
        ),
        'autologin_key' => array(
            'input',
            'input[name="contact_autologin_key"]',
            self::TAB_INFORMATION
        )
    );

    /**
     *  Navigate to and/or check that we are on a contact configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50104&o=c');
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
}
