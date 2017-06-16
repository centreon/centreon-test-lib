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

class ServiceTemplateConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_CONFIGURATION = 1;
    const TAB_AUTHENTICATION = 2;
    const TAB_EXTENDED = 3;

    protected $validField = 'input[name="service_description"]';

    protected $properties = array(
        // General tab.
        'description' => array(
            'text',
            'input[name="service_description"]',
            self::TAB_CONFIGURATION
        ),
        'alias' => array(
            'text',
            'input[name="service_alias"]',
            self::TAB_CONFIGURATION
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\ServiceTemplateConfigurationListingPage';

    /**
     *  Service template edit page.
     *
     *  @param $context  Centreon context object.
     *  @param $visit    True to navigate to a blank edit page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60206&o=a');
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
}

?>
