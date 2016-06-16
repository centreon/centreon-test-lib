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

class ServiceListPage
{
    private $context;

    /**
     *  Service list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60201');
        }
    }

    /**
     *  Check that a service exist in configuration.
     *
     *  @param $host     Host name.
     *  @param $service  Service description.
     */
    public function has($host, $service)
    {
        // Set search filters.
        $this->setSearchHost($host);
        $this->setSearchService($service);
        $this->search();

        // Browse all elements.
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two,.row_disabled');
        $currentHost = '';
        $retval = FALSE;
        foreach ($elements as $element) {
            $tempHost = $this->context->assertFindIn($element, 'css', 'td:nth-child(2) a')->getText();
            if (!empty($tempHost) && ($tempHost != $currentHost)) {
                $currentHost = $tempHost;
            }
            $currentService = $this->context->assertFindIn($element, 'css', 'td:nth-child(3) a')->getText();
            if (($currentHost == $host) && ($currentService == $service)) {
                $retval = TRUE;
                break ;
            }
        }

        // Unset search filters.
        $this->setSearchHost('');
        $this->setSearchService('');
        $this->search();

        return $retval;
    }

    /**
     *  Set host search filter.
     *
     *  @param $host  Host name.
     */
    public function setSearchHost($host)
    {
        $this->context->assertFindField('searchH')->setValue($host);
    }

    /**
     *  Set service search filter.
     *
     *  @param $service  Service description.
     */
    public function setSearchService($service)
    {
        $this->context->assertFindField('searchS')->setValue($service);
    }

    /**
     *  Launch search.
     */
    public function search()
    {
        $this->context->assertFindButton('Search')->click();
    }
}
