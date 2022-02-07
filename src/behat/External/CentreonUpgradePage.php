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

class CentreonUpgradePage implements \Centreon\Test\Behat\Interfaces\Page
{
    protected $context;

    /**
     *  Navigate to and/or check that we are on the Centreon upgrade
     *  page.
     *
     * @param $context  Centreon context.
     */
    public function __construct($context)
    {
        // Disconnect.
        $this->context = $context;
        $this->context->iAmLoggedOut();

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
            'th.step-wrapper'
        );
    }

    /**
     *  Run the web upgrade and browse all steps.
     */
    public function upgrade()
    {
        $mythis = $this;
        // Step 1
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->assertFind('css', 'th.step-wrapper span')->getText() == 1;
            },
            'Current page does not match step 1'
        );
        $this->context->assertFind('css', '#next')->click();

        // Step 2
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->assertFind('css', 'th.step-wrapper span')->getText() == 2;
            },
            'Current page does not match step 2'
        );
        $this->context->assertFind('css', '#next')->click();

        // Step 3
        $this->context->spin(
            function ($context) use ($mythis) {
                return ($this->context->assertFind('css', 'th.step-wrapper span')->getText() == 3)
                    && ($this->context->getSession()->getPage()->has('css', '#releasenotes'));
            },
            'Current page does not match step 3'
        );
        sleep(12);
        $this->context->assertFind('css', '#next')->click();

        // Step 4
        $this->context->spin(
            function ($context) use ($mythis) {
                return ($this->context->assertFind('css', 'th.step-wrapper span')->getText() == 4)
                    && ($this->context->assertFind('css', '#next')->isVisible());
            },
            'Current page does not match step 4',
            120
        );
        $this->context->assertFind('css', '#next')->click();

        // Step 5
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->assertFind('css', 'th.step-wrapper span')->getText() == 5;
            },
            'Current page does not match step 5'
        );
        $this->context->assertFind('css', '#finish')->click();
    }
}
