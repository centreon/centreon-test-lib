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

class BrokerConfigurationListingPage implements ListingPage
{
    private $context;

    /**
     * BrokerConfigurationListingPage constructor.
     * @param $context
     * @param bool $visit
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60909');
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
     * @return array
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2)')->getText();
            $entry['requester'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(3)')->getText();
            $entry['inputs'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entry['outputs'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
            $entry['loggers'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(6)')->getText();
            $entry['status'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(7)')->getText();
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }

    /**
     * @param string $brokerName
     * @return array
     * @throws \Exception
     */
    public function getEntry($brokerName)
    {
        $brokers = $this->getEntries();
        if (!array_key_exists($brokerName, $brokers)) {
            throw new \Exception('could not find broker configuration ' . $brokerName);
        }
        return $brokers[$brokerName];
    }

    /**
     * @param $name
     * @return BrokerConfigurationPage
     */
    public function inspect($name)
    {
        $brokers = $this->context->assertFind('css', 'table.ListTable');
        $this->context->assertFindLinkIn($brokers, $name)->click();
        return new BrokerConfigurationPage($this->context, false);
    }
}
