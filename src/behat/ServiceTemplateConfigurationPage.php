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

namespace Centreon\Test\Behat;

class ServiceTemplateConfigurationPage implements ConfigurationPage
{
    private $context;

    /**
     *  Service template edit page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60206&o=a');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page matches this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="service_description"]');
    }

    /**
     *  Get template properties.
     *
     *  @return Service template properties.
     */
    public function getProperties()
    {
        $properties = array();
        $properties['description'] = $this->context->assertFindField('service_description')->getValue();
        $properties['alias'] = $this->context->assertFindField('service_alias')->getValue();
        return ($properties);
    }

    /**
     *  Set template properties.
     *
     *  @param $properties  Service template properties.
     */
    public function setProperties($properties)
    {
        foreach ($properties as $key => $value) {
            switch ($key) {
            case 'description':
                $this->context->assertFindField('service_description')->setValue($value);
                break ;
            case 'alias':
                $this->context->assertFindField('service_alias')->setValue($value);
                break ;
            default:
                throw new \Exception('Unsupported service template property: ' . $key);
            }
        }
    }

    /**
     *  Save service template.
     */
    public function save()
    {
        $button = $this->context->getSession()->getPage()->findButton('submitA');
        if (isset($button)) {
            $button->click();
        }
        else {
            $this->context->assertFindButton('submitC')->click();
        }
    }
}

?>
