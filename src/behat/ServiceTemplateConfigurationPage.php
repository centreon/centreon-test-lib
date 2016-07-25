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

class ServiceTemplateConfigurationPage
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
     *  Go to service template creation page.
     */
    public function serviceTplPageCreation($tpl_name)
    {
      $this->context->visit('main.php?p=60206&o=a');
      $this->context->assertFind('named', array('id_or_name', 'service_alias'))->setValue($tpl_name);
      $this->context->assertFind('named', array('id_or_name', 'service_description'))->setValue($tpl_name);
    }

    /**
     *  Save a service template.
     */
    public function saveServiceTpl()
    {
      $this->context->assertFind('named', array('id_or_name', 'submitA'))->click();
    }
}
