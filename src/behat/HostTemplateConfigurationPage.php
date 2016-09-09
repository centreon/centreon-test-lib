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

class HostTemplateConfigurationPage implements ConfigurationPage
{
    const TAB_UNKNOWN = 0;
    const TAB_CONFIGURATION = 1;
    const TAB_NOTIFICATION = 2;
    const TAB_RELATIONS = 3;
    const TAB_DATA = 4;
    const TAB_EXTENDED = 5;

    private $context;
    private $tab;

    /**
     *  Host template edit page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60103&o=a');
            $this->tab = self::TAB_CONFIGURATION;
        }
        else {
            $this->tab = self::TAB_UNKNOWN;
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(function ($context) use ($mythis) {
            return $mythis->isPageValid();
        },
        5,
        'Current page does not match class ' . __CLASS__);
    }

    /**
     *  Check that the current page is valid for this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="host_name"]');
    }

    /**
     *  Get template properties.
     *
     *  @return Host template properties.
     */
    public function getProperties()
    {
        $properties = array();
        $properties['name'] = $this->context->assertFindField('host_name')->getValue();
        $properties['alias'] = $this->context->assertFindField('host_alias')->getValue();
        $properties['address'] = $this->context->assertFindField('host_address')->getValue();
        $properties['macros'] = array();
        $i = 0;
        while (true) {
            $name = $this->context->getSession()->getPage()->findField('macroInput_' . $i);
            if (is_null($name)) {
                break ;
            }
            $value = $this->context->assertFindField('macroValue_' . $i);
            $properties['macros'][$name->getValue()] = $value->getValue();
            ++$i;
        }

        return ($properties);
    }

    /**
     *  Set template properties.
     *
     *  @param $properties  Host template properties.
     */
    public function setProperties($properties)
    {
        $this->switchTab(self::TAB_CONFIGURATION);
        foreach ($properties as $key => $value) {
            switch ($key) {
            case 'name':
                $this->context->assertFindField('host_name')->setValue($value);
                break ;
            case 'alias':
                $this->context->assertFindField('host_alias')->setValue($value);
                break ;
            case 'address':
                $this->context->assertFindField('host_address')->setValue($value);
                break ;
            case 'macros':
                $i = 0;
                foreach ($value as $varname => $varvalue) {
                    $this->context->assertFind('css' , '#macro_add p')->click();
                    $this->context->assertFindField('macroInput_' . $i)->setValue($varname);
                    $this->context->assertFindField('macroValue_' . $i)->setValue($varvalue);
                    ++$i;
                }
                break ;
            }
        }

        $this->switchTab(self::TAB_RELATIONS);
        foreach ($properties as $key => $value) {
            switch ($key) {
            case 'service_templates':
                foreach ($value as $tpl) {
                    $this->context->selectToSelectTwo('select#host_svTpls', $tpl);
                }
                break ;
            }
        }
    }

    /**
     *  Save host template.
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

    /**
     *  Switch tab.
     *
     *  @param $tab  Tab.
     */
    public function switchTab($tab)
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
        $this->tab = $tab;
    }
}

?>
