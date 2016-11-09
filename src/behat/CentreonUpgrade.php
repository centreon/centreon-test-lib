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

class CentreonUpgrade
{

    protected $context;
    protected $version;

    /**
     *  Navigate to and/or check that we are on the backup
     *  configuration page.
     *
     * @param $context  Centreon context.
     * @param $version  Centreon version.
     *
     */
    public function __construct($context, $version)
    {
        $this->context = $context;
        $this->version = $version;

        $this->installFiles();
        $this->logOut();
        $this->installWeb();
    }


    public function installFiles()
    {
        $this->context->container->execute(
            'yum update -y --nogpgcheck centreon-base-config-centreon-engine-' . $this->version .
            ' centreon-' . $this->version .
            ' centreon-plugins-' . $this->version .
            ' centreon-plugin-meta-' . $this->version .
            ' centreon-common-' . $this->version .
            ' centreon-web-' . $this->version .
            ' centreon-trap-' . $this->version .
            ' centreon-perl-libs-' . $this->version
            , 'web');
    }

    public function installWeb()
    {
        $mythis = $this;
        //step 1
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->getSession()->getPage()->has('css', '#next');
            },
            5,
            'Current page does not match step 1'
        );
        $this->context->assertFind('css', '#next')->click();

        //step 2
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->getSession()->getPage()->has('css', '#next');
            },
            5,
            'Current page does not match step 2'
        );
        $this->context->assertFind('css', '#next')->click();

        //step 3
        $this->context->spin(
            function ($context) use ($mythis) {
                return 'Next' == $this->context->assertFind('css', '#next')->getValue();
            },
            15,
            'Current page does not match step 3'
        );
        $this->context->assertFind('css', '#next')->click();

        //step 4
        $this->context->spin(
            function ($context) use ($mythis) {
                return !$this->context->getSession()->getPage()->has('css', 'tbody#step_contents td img');
            },
            60,
            'Current page does not match step 4'
        );
        $this->context->assertFind('css', '#next')->click();

        //step 5
        $this->context->spin(
            function ($context) use ($mythis) {
                return $this->context->getSession()->getPage()->has('css', '#finish');
            },
            5,
            'Current page does not match step 5'
        );
        $this->context->assertFind('css', '#finish')->click();
    }

    public function logOut()
    {
        $this->context->assertFind('css', 'div#logli a.red')->click();
    }
}
