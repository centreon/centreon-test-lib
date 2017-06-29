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

namespace Centreon\Test\Behat\Administration;

class ParametersCentreonUiPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="proxy_url"]';

    protected $properties = array(
        // Configuration tab.
        'directory' => array(
            'input',
            'input[name="oreon_path"]'
        ),
        'web_directory' => array(
            'input',
            'input[name="oreon_web_path"]'
        ),
        'web_base_root' => array(
            'input',
            'input[name="centreon_web_base_root"]'
        ),
        'default_limit_page' => array(
            'input',
            'input[name="maxViewConfiguration"]'
        ),
        'monitoring_limit_page' => array(
            'select',
            'select[name="maxViewMonitoring"]'
        ),
        'graph_per_page' => array(
            'input',
            'input[name="maxGraphPerformances"]'
        ),
        'elements_loaded' => array(
            'input',
            'input[name="selectPaginationSize"]'
        ),
        'sessions_time' => array(
            'input',
            'input[name="session_expire"]'
        ),
        'refresh_statistics' => array(
            'input',
            'input[name="AjaxTimeReloadStatistic"]'
        ),
        'refresh_monitoring' => array(
            'input',
            'input[name="AjaxTimeReloadMonitoring"]'
        ),
        'display_sort_by' => array(
            'select',
            'select[name="global_sort_type"]'
        ),
        'display_order_by' => array(
            'select',
            'select[name="global_sort_order"]'
        ),
        'problem_display_sort_by' => array(
            'select',
            'select[name="problem_sort_type"]'
        ),
        'problem_display_order_by' => array(
            'select',
            'select[name="problem_sort_order"]'
        ),
        'proxy_url' => array(
            'input',
            'input[name="proxy_url"]'
        ),
        'proxy_port' => array(
            'input',
            'input[name="proxy_port"]'
        ),
        'proxy_user' => array(
            'input',
            'input[name="proxy_user"]'
        ),
        'proxy_password' => array(
            'input',
            'input[name="proxy_password"]'
        ),
        'enable autologin' => array(
            'checkbox',
            'input[name="enable_autologin[yes]"]'
        )
    );

    /**
     *  Navigate to and/or check that we are on a contact configuration
     *  page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank host configuration
     *                   page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50110&o=general');
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
