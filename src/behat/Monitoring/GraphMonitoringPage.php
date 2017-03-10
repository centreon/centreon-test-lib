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

namespace Centreon\Test\Behat\Monitoring;

class GraphMonitoringPage implements \Centreon\Test\Behat\Interfaces\Page
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
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
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

    /**
      * check if chart exists
      *
      * @param string hostname Hostame to select.
      * @param string servicename Servicename to select.
      */
    public function hasChart($hostname, $servicename)
    {
        $graphDivs = $this->context->getSession()->getPage()->findAll('css', 'div.graph');
        foreach ($graphDivs as $graphDiv) {
            $graphName = $this->context->assertFindIn($graphDiv, 'css', 'div.title > span')->getText();
            if ($graphName == $hostname . ' - ' . $servicename) {
                return true;
            }
        }

        return false;
    }

    /**
      * Get chart
      *
      * @param string hostname Hostame to select.
      * @param string servicename Servicename to select.
      */
    public function getChart($hostname, $servicename)
    {
        $graph = null;

        if ($this->hasChart($hostname, $servicename)) {
            $graphDivs = $this->context->getSession()->getPage()->findAll('css', 'div.graph');
            foreach ($graphDivs as $graphDiv) {
                $graphName = $this->context->assertFindIn($graphDiv, 'css', 'div.title > span')->getText();
                if ($graphName == $hostname . ' - ' . $servicename) {
                    $graph = $graphDiv;
                }
            }
        }

        return $graph;
    }
}
