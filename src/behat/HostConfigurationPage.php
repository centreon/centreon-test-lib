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
      $this->context->assertFind('named', array('id_or_name', 'host_name'))->setValue($host_name);
      $this->context->assertFind('named', array('id_or_name', 'host_alias'))->setValue($host_name);
      $this->context->assertFind('named', array('id_or_name', 'host_address'))->setValue($ip);
    }

    /**
     *  Save a host from the host creation page.
     */
    public function saveHost()
    {
      $this->context->assertFind('named', array('id_or_name', 'submitA'))->click();
    }

    /**
     *  Switch to a (other) tab
     */
    public function switchToTab($tabName, $idOrNameInTab = '')
    {
      // Search the tab from the name of the tab
      $tabLink = $this->context->assertFind('named', array('link', $tabName));

      // Click on the tab for switch to the tab
      $tabLink->click();

      // Wait tab load
      if ($idOrNameInTab == '') {
        $context->getSession()->wait(5000, '');
        return true;
      }

      // Check when the tab is loaded (by a id or name search)
      $this->context->getSession()->spin(function($context, $idOrNameInTab) {
        $container = $context->assertFind('named', array('id_or_name', $idOrNameInTab));
        return $container->isVisible();
      });
      
    }
    
}
