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

class ServiceDependencyConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="dep_name"]';

    protected $properties = array(
        'name' => array(
            'input',
            'input[name="dep_name"]'
        ),
        'description' => array(
            'input',
            'input[name="dep_description"]'
        ),
        'parent_relationship' => array(
            'radio',
            'input[name="inherits_parent[inherits_parent]"]'
        ),
        'execution_fails_on_ok' => array(
            'checkbox',
            'input[name="execution_failure_criteria[o]"]'
        ),
        'execution_fails_on_warning' => array(
            'checkbox',
            'input[name="execution_failure_criteria[w]"]'
        ),
        'execution_fails_on_unknown' => array(
            'checkbox',
            'input[name="execution_failure_criteria[u]"]'
        ),
        'execution_fails_on_critical' => array(
            'checkbox',
            'input[name="execution_failure_criteria[c]"]'
        ),
        'execution_fails_on_pending' => array(
            'checkbox',
            'input[name="execution_failure_criteria[p]"]'
        ),
        'execution_fails_on_none' => array(
            'checkbox',
            'input[name="execution_failure_criteria[n]"]'
        ),
        'notification_fails_on_ok' => array(
            'checkbox',
            'input[name="notification_failure_criteria[o]"]'
        ),
        'notification_fails_on_warning' => array(
            'checkbox',
            'input[name="notification_failure_criteria[w]"]'
        ),
        'notification_fails_on_unknown' => array(
            'checkbox',
            'input[name="notification_failure_criteria[u]"]'
        ),
        'notification_fails_on_critical' => array(
            'checkbox',
            'input[name="notification_failure_criteria[c]"]'
        ),
        'notification_fails_on_pending' => array(
            'checkbox',
            'input[name="notification_failure_criteria[p]"]'
        ),
        'notification_fails_on_none' => array(
            'checkbox',
            'input[name="notification_failure_criteria[n]"]'
        ),
        'services' => array(
            'select2',
            'select#dep_hSvPar'
        ),
        'dependent_services' => array(
            'select2',
            'select#dep_hSvChi'
        ),
        'dependent_hosts' => array(
            'select2',
            'select#dep_hHostChi'
        ),
        'comment' => array(
            'input',
            'textarea[name="dep_comment"]'
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\ServiceDependencyConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a contact configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param bool $visit True to navigate to a blank configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60409&o=a');
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
