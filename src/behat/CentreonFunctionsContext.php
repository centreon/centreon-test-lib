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

use WebDriver\WebDriver;

class CentreonFunctionContext extends CentreonContext
{
    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     *  Restart engine on a given poller. 
     *
     *  @param $poller_id  The id of the poller to restart.
     */
    public function restartEngine($poller_id = 1)
    {
      $this->visit("/main.php?p=60902&poller=$poller_id");
      $this->assertFind('named', array('id', 'nrestart'))->check();
      $this->getSession()->getPage()->selectFieldOption('restart_mode', 'Restart');
      $this->assertFind('named', array('id', 'exportBtn'))->click();
      $this->spin(function($context) {
          return $context->getSession()->getPage()->has('named', array('id', 'progressPct'))
                 && $context->getSession()->getPage()->find('named', array('id', 'progressPct'))->getText() == '100%';
      });
    }
    
    /**
     *  Select an element in a select two.
     *
     *  @param $css_id  The id of the select two.
     *  @param $what    What to select.
     */
    public function selectToSelectTwo($css_id, $what)
    {
      $inputField = $this->assertFind('css', $css_id);
      $choice = $inputField->getParent()->find('css', '.select2-selection');
        if (!$choice) {
            throw new \Exception('No select2 choice found');
        }
      $choice->press();

      $this->spin(
          function ($context) {
              return count($context->getSession()->getPage()->findAll('css', '.select2-container--open li.select2-results__option')) != 0;
          },
          30
      );

      $chosenResults = $this->getSession()->getPage()->findAll('css', '.select2-results li:not(.select2-results__option--highlighted)');
      foreach ($chosenResults as $result) {
          if ($result->getText() == $what) {
              $result->click();
              break;
          }
      }
    }

    /**
     *  Go to host creation page.
     *
     *  @param $host_name  The name of the host
     *  @param $ip         The ip of the host
     */
    public function toHostCreationPage($host_name, $ip = '127.0.0.1')
    {
      $this->visit('main.php?p=60101&o=a');
      $this->assertFind('named', array('name', 'host_name'))->setValue($host_name);
      $this->assertFind('named', array('name', 'host_alias'))->setValue($host_name);
      $this->assertFind('named', array('name', 'host_address'))->setValue($ip);
    }

    /**
     *  Commit a host from the host creation page.
     */
    public function commitHost()
    {
      $this->assertFind('named', array('name', 'submitA'))->click();
    }

    /**
     *  Go to service creation page.
     *
     *  @param $host_name  The name of the host linked to this service
     *  @param $service_description  The description of this service.
     *  @param $template             The template to use for this service.
     */
    public function toServiceCreationPage($host_name, $service_description, $template = 'generic-service')
    {
      $this->visit('main.php?p=60201&o=a');
      $this->selectToSelectTwo('select#service_hPars', $host_name);
      $this->assertFind('named', array('name', 'service_description'))->setValue($service_description);
      $this->selectToSelectTwo('select#service_template_model_stm_id', $template);
    }

    /**
     *  Commit a service from the host creation page.
     */
    public function commitService()
    {
      $this->assertFind('named', array('name', 'submitA'))->click();
    }
}
