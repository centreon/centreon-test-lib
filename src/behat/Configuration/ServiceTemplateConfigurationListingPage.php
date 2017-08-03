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

class ServiceTemplateConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchST"]';

    protected $properties = array(
        'description' => array(
            'text',
            'td:nth-child(2)'
        ),
        'icon' => array(
            'attribute',
            'td:nth-child(2) img',
            'src'
        ),
        'alias' => array(
            'text',
            'td:nth-child(3)'
        ),
        'parents' => array(
            'custom'
        ),
        'status' => array(
            'text',
            'td:nth-child(6)'
        ),
        'locked' => array(
            'custom'
        ),
        'id' => array(
            'custom'
        )
    );

    protected $objectClass = '\Centreon\Test\Behat\Configuration\ServiceTemplateConfigurationPage';

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
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     * Get parent templates
     *
     * @param $element
     * @return array
     */
    protected function getParents($element)
    {
        $parents = $this->context->assertFindIn($element, 'css', 'td:nth-child(5)')->getText();
        $parents = explode(' ', $parents);

        return $parents;
    }

    /**
     * @param $element
     * @return bool
     */
    protected function getLocked($element)
    {
        return (null === $element->find('css', 'input:nth-child(2)'));
    }

    /**
     * @param $element
     * @return id
     */
    protected function getId($element)
    {
        $idComponent =$this->context->assertFindIn($element,'css','input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;
        return $id;
    }
}
