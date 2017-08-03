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

class MetaServiceConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="meta_name"]';

    protected $properties = array(
        'name' => array(
            'input',
            'input[name="meta_name"]'
        ),
        'output_format' => array(
            'input',
            'input[name="meta_display"]'
        ),
        'warning_level' => array(
            'input',
            'input[name="warning"]'
        ),
        'critical_level' => array(
            'input',
            'input[name="critical"]'
        ),
        'calculation_type' => array(
            'select',
            'select[name="calcul_type"]'
        ),
        'data_source_type' => array(
            'select',
            'select[name="data_source_type"]'
        ),
        'selection_mode' => array(
            'radio',
            'input[name="meta_select_mode[meta_select_mode]"]'
        ),
        'sql_like_clause_expression' => array(
            'input',
            'input[name="regexp_str"]'
        ),
        'metric' => array(
            'select',
            'select[name="metric"]'
        ),
        'check_period' => array(
            'select2',
            'select#check_period'
        ),
        'max_check_attempts' => array(
            'input',
            'input[name="max_check_attempts"]'
        ),
        'normal_check_interval' => array(
            'input',
            'input[name="normal_check_interval"]'
        ),
        'retry_check_interval' => array(
            'input',
            'input[name="retry_check_interval"]'
        ),
        'notification_enabled' => array(
            'radio',
            'input[name="notifications_enabled[notifications_enabled]"]'
        ),
        'contacts' => array(
            'select2',
            'select#ms_cs'
        ),
        'contact_groups' => array(
            'select2',
            'select#ms_cgs'
        ),
        'notification_interval' => array(
            'input',
            'input[name="notification_interval"]'
        ),
        'notification_period' => array(
            'select2',
            'select#notification_period'
        ),
        'notification_on_warning' => array(
            'checkbox',
            'input[name="ms_notifOpts[w]"]'
        ),
        'notification_on_unknown' => array(
            'checkbox',
            'input[name="ms_notifOpts[u]"]'
        ),
        'notification_on_critical' => array(
            'checkbox',
            'input[name="ms_notifOpts[c]"]'
        ),
        'notification_on_recovery' => array(
            'checkbox',
            'input[name="ms_notifOpts[r]"]'
        ),
        'notification_on_flapping' => array(
            'checkbox',
            'input[name="ms_notifOpts[f]"]'
        ),
        'geo_coordinates' => array(
            'input',
            'input[name="geo_coords"]'
        ),
        'graph_template' => array(
            'select',
            'select[name="graph_id"]'
        ),
        'enabled' => array(
            'radio',
            'input[name="meta_activate[meta_activate]"]'
        ),
        'comments' => array(
            'input',
            'textarea[name="meta_comment"]'
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\MetaServiceConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a service configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank service
     *                   configuration page.
     */

    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60204&o=a');
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
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="meta_name"]');
    }
}
