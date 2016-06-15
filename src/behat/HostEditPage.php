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

class HostEditPage
{
    const UNKNOWN_TAB = 0;
    const CONFIGURATION_TAB = 1;
    const NOTIFICATION_TAB = 2;
    const RELATIONS_TAB = 3;
    const DATA_TAB = 4;
    const EXTENDED_TAB = 5;

    private $context;
    private $tab;

    /**
     *  Host edit page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60101&o=a');
            $this->tab = self::CONFIGURATION_TAB;
        }
        else {
            $this->tab = self::UNKNOWN_TAB;
        }
    }

    /**
     *  Get properties.
     *
     *  @return Host properties.
     */
    public function getProperties()
    {
        $properties = array();
        $properties['name'] = $this->context->assertFindField('host_name')->getValue();
        $properties['alias'] = $this->context->assertFindField('host_alias')->getValue();
        $properties['address'] = $this->context->assertFindField('host_address')->getValue();
        return ($properties);
    }

    /**
     *  Set properties.
     */
    public function setProperties($properties)
    {
        $this->switchTab(self::CONFIGURATION_TAB);
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
            }
        }

        $this->switchTab(self::RELATIONS_TAB);
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
     *  Save host.
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
     *  Switch between tabs.
     *
     *  @param $tab  Target tab.
     */
    public function switchTab($tab)
    {
        $this->context->assertFind('css', 'li#c' . $tab . ' a')->click();
        $this->tab = $tab;
    }
}
