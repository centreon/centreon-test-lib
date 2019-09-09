<?php
/**
 * Copyright 2005-2019 Centreon
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

use Behat\Mink\Element\TraversableElement;

abstract class Page
{
    /**
     * Current context
     *
     * @var object
     */
    protected $context;

    /**
     * css selector to validate current page
     *
     * @var string
     */
    protected $validField;

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
     * Check checkbox
     *
     * @param TraversableElement $checkbox checkbox to check
     * @return void
     */
    public function checkCheckbox(TraversableElement $checkbox)
    {
        if ($checkbox->getParent()->hasClass('md-checkbox') && !$checkbox->getValue()) {
            $checkbox->getParent()->click();
        } else {
            $checkbox->check();
        }
    }

    /**
     * Uncheck checkbox
     *
     * @param TraversableElement $checkbox checkbox to uncheck
     * @return void
     */
    public function uncheckCheckbox(TraversableElement $checkbox)
    {
        if ($checkbox->getParent()->hasClass('md-checkbox') && $checkbox->getValue()) {
            $checkbox->getParent()->click();
        } else {
            $checkbox->uncheck();
        }
    }
}