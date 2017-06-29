<?php
/**
 * Copyright 2017 Centreon
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

class CommentConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'textarea[name="comment"]';

    protected $properties = array(
        'comment' => array(
            'input',
            'textarea[name="comment"]'
        )
    );

    /**
     *  Navigate to the comment creation page and/or check that it
     *  matches this class.
     *
     *  @param $context  Centreon context.
     *  @param $host     Host name. If empty, current page will not be
     *                   changed.
     *  @param $service  Service description. If empty, current page
     *                   will not be changed.
     */
    public function __construct($context, $host = '', $service = '')
    {
        // Visit page.
        $this->context = $context;
        if (!empty($host) && !empty($service)) {
            $this->context->visit('main.php?p=21002&o=as&host_name=' . $host . '&service_description=' . $service);
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
