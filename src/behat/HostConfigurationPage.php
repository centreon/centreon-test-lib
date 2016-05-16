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

class HostConfigurationPage
{
    protected $context;
    /**
     * Constructor
     *
     * @param array $context A CentreonContext
     */
    public function __construct($context)
    {
        $this->context = $context;
    }

    /**
     *  Go to host creation page.
     *
     *  @param $host_name  The name of the host
     *  @param $ip         The ip of the host
     */
    public function toHostCreationPage($host_name, $ip = '127.0.0.1')
    {
      $this->context->visit('main.php?p=60101&o=a');
      $this->context->assertFind('named', array('name', 'host_name'))->setValue($host_name);
      $this->context->assertFind('named', array('name', 'host_alias'))->setValue($host_name);
      $this->context->assertFind('named', array('name', 'host_address'))->setValue($ip);
    }

    /**
     *  Save a host from the host creation page.
     */
    public function saveHost()
    {
      $this->context->assertFind('named', array('name', 'submitA'))->click();
    }
}
