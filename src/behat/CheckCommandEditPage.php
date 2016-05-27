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

class CheckCommandEditPage
{
    protected $context;

    /**
     *  Check Command edit page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60801&o=a&type=2');
        }
    }

    /**
     *  Set template properties.
     *
     *  @param $properties  Host template properties.
     */
    public function setCommandProperties($properties)
    {
        // Warning : exist readonly field(s) (listOfArg)
        
        foreach ($properties as $key => $value) {
            switch ($key) {
                case 'name':
                    $this->context->assertFindField('command_name')->setValue($value);
                    break ;
                case 'line':
                    $this->context->assertFindField('command_line')->setValue($value);
                    break ;
                case 'comment':
                    $this->context->assertFindField('command_comment')->setValue($value);
                    break ;
                default:
                    throw new \Exception('Unsupported check command property: ' . $key);
            }
        }
    }

    public function getCommandProperties($properties)
    {
        $arr = array();
        
        foreach ($properties as $key) {
            switch ($key) {
                case 'name':
                    $arr['name'] = $this->context->assertFindField('command_name')->getValue();
                    break ;
                case 'line':
                    $arr['line'] = $this->context->assertFindField('command_line')->getValue();
                    break ;
                default:
                    throw new \Exception('Unsupported set command property: ' . $key);
            }
        }
        
        return $arr;
    }

    /**
     * Wait command(s) list page
     */
    public function waitForCommandEditPage()
    {
        $this->context->spin(function ($context) {
            return $context->getSession()->getPage()->has('named', array('id_or_name', 'command_name'));
        });
    }

    /**
     *  Save Check command.
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