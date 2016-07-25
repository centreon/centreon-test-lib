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

class ServiceTemplateListPage
{
    private $context;

    /**
     *  Service template list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60206');
        }
    }

    /**
     *  Get template properties.
     *
     *  @param $name  Host template name.
     */
    public function getTemplate($name)
    {
        $templates = $this->getTemplates();
        $tmpl = array_search($name, $templates);
        if ($tmpl == FALSE) {
            throw new \Exception('Cannot find host template "' . $name . '".');
        }
        return $tmpl;
    }

    /**
     *  Get the list of templates.
     */
    public function getTemplates()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
            $entry['alias'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['parents'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
            $entry['status'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(6)')->getText();
            $entry['locked'] = (null === $element->find('css', 'input:nth-child(2)'));
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }
}

?>
