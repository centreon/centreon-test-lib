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

class PollerConfigurationPage
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
     *  Restart engine on a given poller. 
     *
     *  @param $poller_id  The id of the poller to restart.
     */
    public function restartEngine($poller_id = 1)
    {
      $this->context->visit("/main.php?p=60902&poller=$poller_id");
      $this->context->assertFind('named', array('id', 'nrestart'))->check();
      $this->context->getSession()->getPage()->selectFieldOption('restart_mode', 'Restart');
      $this->context->assertFind('named', array('id', 'exportBtn'))->click();
      $this->context->spin(function($context) {
          return $context->getSession()->getPage()->has('named', array('id', 'progressPct'))
                 && $context->getSession()->getPage()->find('named', array('id', 'progressPct'))->getText() == '100%';
      });
    }
    
}
