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

class ServiceTemplateConfigurationListingPage implements ListingPage
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
        // Navigate.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60206');
        }

        // Check that page is valid.
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
        return $this->context->getSession()->getPage()->has('css', 'input[name="searchST"]');
    }

    /**
     *  Get the list of templates.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $descriptionComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)');
            $imageComponent = $this->context->assertFindIn($descriptionComponent, 'css', 'img');
            $entry = array();
            $entry['description'] = $descriptionComponent->getText();
            $entry['icon'] = $imageComponent->getAttribute('src');
            $entry['alias'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['parents'] = explode(' ', $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText());
            $entry['status'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(6)')->getText();
            $entry['locked'] = (null === $element->find('css', 'input:nth-child(2)'));
            $entries[$entry['description']] = $entry;
        }
        return $entries;
    }

    /**
     *  Get properties of a service template.
     *
     *  @param $svctmpl  Service template name.
     *
     *  @return An array of properties.
     */
    public function getEntry($svctmpl)
    {
        $templates = $this->getEntries();
        if (!array_key_exists($svctmpl, $templates)) {
            throw new \Exception('could not find service template ' . $svctmpl);
        }
        return $templates[$svctmpl];
    }

    /**
     *  Edit a template.
     *
     *  @param $name  Service template name.
     */
    public function inspect($name)
    {
        $this->context->assertFindLink($name)->click();
        return new ServiceTemplateConfigurationPage($this->context, false);
    }
}
