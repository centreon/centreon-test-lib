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

class HostTemplateConfigurationListingPage implements ListingPage
{
    private $context;

    /**
     *  Host template list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60103');
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
     *  Check that the current page is matching this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="searchHT"]');
    }

    /**
     *  Edit a template.
     *
     *  @param $name  Host template name.
     */
    public function edit($name)
    {
        $this->context->assertFindLink($name)->click();
        return new HostTemplateEditPage($this->context, FALSE);
    }

    /**
     *  Get the list of templates.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $nameComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)');
            $imageComponent = $this->context->assertFindIn($nameComponent, 'css', 'img');
            $entry = array();
            $entry['name'] = $nameComponent->getText();
            $entry['icon'] = $imageComponent->getAttribute('src');
            $entry['description'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['linked_services'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['parents'] = explode(' ', str_replace('| ', '', $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText()));
            $entry['locked'] = (null === $element->find('css', 'input:nth-child(2)'));
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }
}