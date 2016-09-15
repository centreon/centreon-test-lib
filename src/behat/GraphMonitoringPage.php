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

class GraphMonitoringPage implements Page
{
    private $context;

    /**
     *  Navigate to and/or check that we are on a host configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=20401');
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
        return $this->context->getSession()->getPage()->has('css', 'select#select-chart');
    }

    /**
     * Set the filter chart
     *
     * @param string hostname Host to select.
     * @param string servicename Service to select.
     */
    public function setFilterbyChart($hostname, $servicename)
    {
        $this->setFilterbyHost($hostname);
sleep(10);
        $this->context->selectToSelectTwo('#select-chart', $hostname . ' - ' . $servicename);
    }

    /**
      * Set the filter hostname
      *
      * @param string hostname Hostame to select.
      */
    public function setFilterbyHost($hostname)
    {
        $this->context->selectToSelectTwo('#host_filter', $hostname);
    }

}
