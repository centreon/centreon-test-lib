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

class MetricsConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="vmetric_name"]';

    protected $properties = array(
        'name' => array(
            'input',
            'input[name="vmetric_name"]'
        ),
        'linked-host_services' => array(
            'select2',
            'select#host_id'
        ),
        'def_type' => array(
            'select',
            'select[name="def_type"]'
        ),
        'known_metrics' => array(
            'select2',
            'select#sl_list_metrics'
        ),
        'function' => array(
            'custom',
            'Function'
        ),
        'hidden_graph' => array(
            'checkbox',
            'input[name="vhidden"]'
        )
    );

    /**
     *
     * @var type 
     */
    protected $listingClass = '\Centreon\Test\Behat\Monitoring\MetricsConfigurationListingPage';

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
            $this->context->visit('main.php?p=20408&o=a');
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
     * 
     * @param type $value
     */
    public function setFunction($value) 
    {
        if (!empty($value)) {
            $this->context->spin(
                function ($context) {
                    $metricCount = count($context->getSession()->getPage()->findAll(
                        'css',
                        'select#sl_list_metrics option'
                    ));
                    return ($metricCount >= 2); 
                },
                'Can not load metrics before setting function'
            );
        }  
        $this->context->assertFind('css', 'textarea[name="rpn_function"]')->setValue($value);
    }
}
