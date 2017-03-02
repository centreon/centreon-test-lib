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

class ContactConfigurationListingPage implements ListingPage
{
    private $context;

    /**
     *  Contact list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60301');
        }

        // Check that page is valid for this class.
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
        return $this->context->getSession()->getPage()->has('css', 'table.ListTable');
    }

    /**
     *  Get the list of contacts.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $nameComponent = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)');
            $imageComponent = $this->context->assertFindIn($nameComponent, 'css', 'img');
            $entry = array();
            $entry['alias'] = $nameComponent->getText();
            $entry['icon'] = $imageComponent->getAttribute('src');
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['email'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['host_notification_period'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
            $entry['service_notification_period'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(6)')->getText();
            $entry['language'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(7)')->getText();
            $entry['access'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(8)')->getText();
            $entry['admin'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(8)')->getText();
            $entry['status'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(8)')->getText();
            $entries[$entry['alias']] = $entry;
        }
        return $entries;
    }

    /**
     *  Get a contact.
     *
     *  @param $contactname  Contact name.
     *
     *  @return An array of properties.
     */
    public function getEntry($contactname)
    {
        $contacts = $this->getEntries($contactname);
        if (!array_key_exists($contactname, $contacts)) {
            throw new \Exception('could not find contact ' . $contactname);
        }
        return $contacts[$contactname];
    }

    /**
     *  Edit a contact.
     *
     *  @param $name  Contact name.
     */
    public function inspect($name)
    {
        $contacts = $this->context->assertFind('css', 'table.ListTable');
        $this->context->assertFindLinkIn($contacts, $name)->click();
        return new ContactConfigurationPage($this->context, false);
    }
}
