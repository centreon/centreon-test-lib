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

namespace Centreon\Test\Behat;

class ParametersCentreonUiPage implements ConfigurationPage
{
    protected $context;

    private static $properties = array(
        // Configuration tab.
        'directory' => array(
            'text',
            'input[name="oreon_path"]'
        ),
        'web_directory' => array(
            'text',
            'input[name="oreon_web_path"]'
        ),
        'web_base_root' => array(
            'text',
            'input[name="centreon_web_base_root"]'
        ),
        'default_limit_page' => array(
            'text',
            'input[name="maxViewConfiguration"]'
        ),
        'monitoring_limit_page' => array(
            'select',
            'select[name="maxViewMonitoring"]'
        ),
        'graph_per_page' => array(
            'text',
            'input[name="maxGraphPerformances"]'
        ),
        'elements_loaded' => array(
            'text',
            'input[name="selectPaginationSize"]'
        ),
        'sessions_time' => array(
            'text',
            'input[name="session_expire"]'
        ),
        'refresh_statistics' => array(
            'text',
            'input[name="AjaxTimeReloadStatistic"]'
        ),
        'refresh_monitoring' => array(
            'text',
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
            'text',
            'input[name="proxy_url"]'
        ),
        'proxy_port' => array(
            'text',
            'input[name="proxy_port"]'
        ),
        'proxy_user' => array(
            'text',
            'input[name="proxy_user"]'
        ),
        'proxy_password' => array(
            'text',
            'input[name="proxy_password"]'
        ),
        'enable autologin' => array(
            'checkbox',
            'input[name="enable_autologin[yes]"]')

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

    /**
     *  Check that the current page is matching this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="proxy_url"]');
    }

    /**
     *  Get host properties.
     *
     * @return Host properties.
     */
    public function getProperties()
    {
        // Begin with first tab.
        $properties = array();

        // Browse all properties.
        foreach (self::$properties as $property => $metadata) {
            // Set property meta-data in variables.
            $propertyType = $metadata[0];
            $propertyLocator = $metadata[1];

            // Get properties.
            switch ($propertyType) {
                case 'radio':
                    throw new \Behat\Behat\Tester\Exception\PendingException(__FUNCTION__);
                    break;
                case 'select2':
                    $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                    break;
                case 'checkbox':
                case 'text':
                    $properties[$property] = $this->assertFindField($propertyLocator)->getValue();
                    break;
                case 'select':
                    $properties[$property] = $this->context->assertFind('css', $propertyLocator)->getValue();
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while retrieving host properties.');
            }
        }
        return $properties;
    }

    /**
     *  Set host properties.
     *
     * @param $properties Centreon UI properties.
     */
    public function setProperties($properties)
    {

        // Browse all properties.
        foreach ($properties as $property => $value) {
            // Check that property exist.
            if (!array_key_exists($property, self::$properties)) {
                throw new \Exception('Unknown host property ' . $property . '.');
            }

            // Set property meta-data in variables.
            $propertyType = self::$properties[$property][0];
            $propertyLocator = self::$properties[$property][1];

            // Set property with its value.
            switch ($propertyType) {
                case 'custom':
                    $setter = 'set' . $propertyLocator;
                    $this->$setter($value);
                    break;
                case 'checkbox':
                case 'radio':
                    $this->context->assertFind('css', $propertyLocator . '[value="' . $value . '"]')->click();
                    break;
                case 'select2':
                    if (!is_array($value)) {
                        $value = array($value);
                    }
                    foreach ($value as $element) {
                        $this->context->selectToSelectTwo($propertyLocator, $element);
                    }
                    break;
                case 'text':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                case 'select':
                    $this->context->assertFind('css', $propertyLocator)->setValue($value);
                    break;
                default:
                    throw new \Exception(
                        'Unknown property type ' . $propertyType
                        . ' found while setting host property ' . $property . '.');
            }
        }
    }

    /**
     *  Save the current contact configuration page.
     */
    public function save()
    {
        $button = $this->context->getSession()->getPage()->findButton('submitA');
        if (isset($button)) {
            $button->click();
        } else {
            $this->context->assertFindButton('submitC')->click();
        }
    }

}
