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

namespace Centreon\Test\Behat\Configuration;

class HostGroupServiceConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="hostgroups"]';

    protected $properties = array(
        'service' => array(
            'text',
            'td:nth-child(3)',
        ),
        'scheduling' => array(
            'text',
            'td:nth-child(4)'
        ),
        'id' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\HostGroupServiceConfigurationPage';

    /**
     * Host group service list page.
     *
     * @param $context  Centreon context class.
     * @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60202');
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

    protected function getId($element)
    {
        $idComponent =$this->context->assertFindIn($element,'css','input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }

    /**
     *  Get services.
     */
    public function getEntries()
    {
        // Browse all elements.
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two,.row_disabled');
        $currentHostGroup = '';
        foreach ($elements as $element) {
            $tempHostGroup = $this->context->assertFindIn($element, 'css', 'td:nth-child(2) a')->getText();
            if (!empty($tempHostGroup) && ($tempHostGroup != $currentHostGroup)) {
                $currentHostGroup = $tempHostGroup;
            }
            $currentService = $this->context->assertFindIn($element, 'css', 'td:nth-child(3) a')->getText();
            $entries[$currentHostGroup][$currentService] = array();
            foreach ($this->properties as $property => $metadata) {
                if (empty($propertyTitle)) {
                    $propertyTitle = $property;
                }

                // Set property meta-data in variables.
                $propertyType = $metadata[0];
                $propertyLocator = isset($metadata[1]) ? $metadata[1] : '';

                    switch ($propertyType) {
                    case 'text':
                        $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                        $entries[$currentHostGroup][$currentService][$property] = $component->getText();
                            break;
                    case 'attribute':
                            if (is_null($propertyLocator) || empty($propertyLocator)) {
                                $component = $element;
                        } else {
                            $component = $this->context->assertFindIn($element, 'css', $propertyLocator);
                        }
                        $entries[$currentHostGroup][$currentService][$property] = $component->getAttribute($metadata[2]);
                        break;
                    case 'custom':
                        $methodName = 'get' . ucfirst($property);
                        $entries[$currentHostGroup][$currentService][$property] = $this->$methodName($element);
                        break;
                }
            }
        }

        return $entries;
    }

    /**
     *  Get a service.
     *
     *  @param $hostgroupservice  Array with host_group and service.
     *
     *  @return Service properties.
     */
    public function getEntry($hostgroupservice)
    {
        $services = $this->getEntries();
        if (!array_key_exists($hostgroupservice['host_group'], $services) ||
            !array_key_exists($hostgroupservice['service'], $services[$hostgroupservice['host_group']])) {
            throw new \Exception(
                'could not find service ' . $hostgroupservice['service'] .
                ' of hostgroup ' . $hostgroupservice['host_group']
            );
        }
        return $services[$hostgroupservice['host_group']][$hostgroupservice['service']];
    }

    /**
     *  Check if a service exist.
     *
     *  @param $hostgroupservice  Array with host_group and service.
     *
     *  @return True if service exist.
     */
    public function hasEntry($hostgroupservice)
    {
        try {
            $this->getEntry($hostgroupservice);
            $thrown = false;
        } catch (\Exception $e) {
            $thrown = true;
        }
        return !$thrown;
    }

    /**
     *  Set hostgroup search filter.
     *
     *  @param $hostgroup  Hostgroup name.
     */
    public function setHostGroupFilter($host)
    {
        $this->context->assertFindField('hostgroups')->setValue($hostgroup);
    }

    /**
     *  Set service search filter.
     *
     *  @param $service  Service description.
     */
    public function setServiceFilter($service)
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
