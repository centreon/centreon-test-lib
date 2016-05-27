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

class ServiceConfigurationPage
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
     *  Go to service creation page.
     *
     *  @param $host_name  The name of the host linked to this service
     *  @param $service_description  The description of this service.
     *  @param $template             The template to use for this service.
     */
    public function toServiceCreationPage($host_name, $service_description, $template = 'generic-service')
    {
      $this->context->visit('main.php?p=60201&o=a');
      $this->context->selectToSelectTwo('select#service_hPars', $host_name);
      $this->context->assertFind('css', 'input[name=service_description]')->setValue($service_description);
      $this->context->selectToSelectTwo('select#service_template_model_stm_id', $template);
      $this->context->selectToSelectTwo('select#command_command_id', 'check_centreon_dummy');
      $this->context->assertFind('named', array('id_or_name', 'service_max_check_attempts'))->setValue('1');
      $this->context->assertFind('named', array('id_or_name', 'service_normal_check_interval'))->setValue('1');
      $this->context->assertFind('named', array('id_or_name', 'service_retry_check_interval'))->setValue('1');
    }

    /**
     *  Save a service from the service creation page.
     */
    public function saveService()
    {
      $this->context->assertFind('named', array('id_or_name', 'submitA'))->click();
    }
}
