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

class HostCategoryConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="hc_name"]';

    protected $properties = array(
        'name' => array(
            'input',
            'input[name="hc_name"]'
        ),
        'alias' => array(
            'input',
            'input[name="hc_alias"]'
        ),
        'hosts' => array(
            'select2',
            'select[name="hc_hosts[]"]'
        ),
        'host_templates' => array(
            'select2',
            'select[name="hc_hostsTemplate[]"]'
        ),
        'severity' => array(
            'checkbox',
            'input[name="hc_type"]'
        ),
        'severity_level' => array(
            'input[]',
            'input[name="hc_severity_level"]'
        ),
        'severity_icon' => array(
            'select',
            'select[name="hc_severity_icon"]'
        ),
        'enabled' => array(
            'radio',
            'input[name="hc_activate[hc_activate]"]'
        ),
        'comments' => array(
            'input',
            'textarea[name="hc_comment"]'
        )
    );

    /**
     * @var string
     */
    //protected $listingClass = '\Centreon\Test\Behat\Configuration\HostCategoryConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a contact configuration
     *  page.
     *
     *  @param $context  Centreon context.
     *  @param bool $visit    True to navigate to a blank configuration page.
     */
    public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60104&o=a');
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
}
