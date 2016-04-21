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

class ConfigurationPollersPage
{
    private $ctx;

    public function __construct($context)
    {
        $this->ctx = $context;
    }

    public function addPoller($name)
    {
        $this->ctx->visit('/main.php?p=60901&o=a');
        $this->ctx->assertFindField('name')->setValue($name);
        $this->ctx->assertFindButton('Save')->click();
        $this->waitForPollerListPage();
    }

    public function duplicatePoller($name)
    {
        $this->listPollers();
        $this->ctx->setConfirmBox(true);
        $this->selectPoller($name);
        $this->ctx->getSession()->getPage()->selectFieldOption('o1', 'Duplicate');
        $this->waitForPollerListPage();
        $this->enablePoller($name . '_1');
    }

    public function editPoller($name)
    {
        $this->listPollers();
        $this->ctx->assertFindLink($name)->click();
    }

    public function enablePoller($name)
    {
        $this->listPollers();
        $this->ctx->assertFind('xpath', "//a[text()='" . $name . "']/../../td/a/img[@alt='Enabled']/..")->click();
        $this->waitForPollerListPage();
    }

    public function listPollers()
    {
        $this->ctx->visit('/main.php?p=609');
        $this->waitForPollerListPage();
    }

    public function removePoller($name)
    {
        $this->listPollers();
        $this->ctx->setConfirmBox(true);
        $this->selectPoller($name);
        $this->ctx->getSession()->getPage()->selectFieldOption('o1', 'Delete');
        $this->waitForPollerListPage();
    }

    public function selectPoller($name)
    {
        $this->ctx->assertFind('xpath', "//a[text()='" . $name . "']/../../td/input")->check();
    }

    public function waitForPollerListPage()
    {
        $this->ctx->spin(function($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'searchP'));
        });
    }
}