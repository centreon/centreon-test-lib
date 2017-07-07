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

class HostGroupConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="hg_name"]';

    protected $properties = array(
        'name' => array(
            'input',
            'input[name="hg_name"]'
        ),
        'alias' => array(
            'input',
            'input[name="hg_alias"]'
        ),
        'hosts' => array(
            'select2',
            'select[name="hg_hosts[]"]'
        ),
        'notes' => array(
            'input',
            'input[name="hg_notes"]'
        ),
        'notes_url' => array(
            'input',
            'input[name="hg_notes_url"]'
        ),
        'action_url' => array(
            'input',
            'input[name="hg_action_url"]'
        ),
        'icon' => array(
            'select',
            'select[name="hg_icon_image"]'
        ),
        'map_icon' => array(
            'select',
            'select[name="hg_map_icon_image"]'
        ),
        'geo_coordinates' => array(
            'input',
            'input[name="geo_coords"]'
        ),
        'rrd_retention' => array(
            'input',
            'input[name="hg_rrd_retention"]'
        ),
        'comments' => array(
            'input',
            'textarea[name="hg_comment"]'
        ),
        'enabled' => array(
            'radio',
            'input[name="hg_activate[hg_activate]"]'
        )
    );

    /**
     * @var string
     */
    //protected $listingClass = '\Centreon\Test\Behat\Configuration\HostGroupConfigurationListingPage';

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
            $this->context->visit('main.php?p=60102&o=a');
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
