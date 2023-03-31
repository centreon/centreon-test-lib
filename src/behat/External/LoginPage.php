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

namespace Centreon\Test\Behat\External;

class LoginPage extends \Centreon\Test\Behat\Page
{
    protected $context;

    private const LOGIN_FIELD_SELECTOR = 'input[aria-label="Alias"]';

    /**
     *  Navigate to and/or check that we are on the login page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('/', false);
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
     *  Check that the current page is matching this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has(
            'css',
            self::LOGIN_FIELD_SELECTOR
        );
    }

    /**
     *  Login.
     */
    public function login($user, $password)
    {
        // Send login form.
        try {
            $this->context->assertFind('css', self::LOGIN_FIELD_SELECTOR)->setValue($user);
            $this->context->assertFind('css', 'input[aria-label="Password"]')->setValue($password);
            $this->context->assertFind('css', 'button[aria-label="Connect"]')->click();
        } catch (\Exception $e) {
            throw new \Exception("Cannot login.\n" . $e->getMessage());
        }

        // Wait for connection.
        $this->context->spin(
            function ($context) {
                return $context->getSession()->getPage()->has(
                    'css',
                    'div[data-testid="sidebar"]'
                );
            },
            'Login failed.',
            15
        );
    }
}
