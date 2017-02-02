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

class ACLGroupConfigurationListingPage implements ListingPage
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
            $this->context->visit('main.php?p=50203');
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
     *  Check that the current page matches this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'table.ListTable');
    }

    /**
     *  Get the list of ACL groups.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
            $entry['description'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['count_contacts'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['count_contactgroups'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['status'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }

    /**
     *  Get an ACL group.
     *
     *  @param $aclGroupName  ACL group name
     *  @throws \Exception
     *  @return An array of properties.
     */
    public function getEntry($aclGroupName)
    {
        $aclGroups = $this->getEntries();
        if (!array_key_exists($aclGroupName, $aclGroups)) {
            throw new \Exception('could not find acl group ' . $aclGroupName);
        }
        return $aclGroups[$aclGroupName];
    }

    /**
     *  Edit an acl group.
     *
     *  @param $aclGroupName  ACL group name
     *  @return ACLGroupConfigurationPage
     */
    public function inspect($aclGroupName)
    {
        $contacts = $this->context->assertFind('css', 'table.ListTable');
        $this->context->assertFindLinkIn($contacts, $aclGroupName)->click();
        return new ACLGroupConfigurationPage($this->context, false);
    }
}
