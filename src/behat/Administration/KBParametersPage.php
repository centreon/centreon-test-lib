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

class KBParametersPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="kb_wiki_url"]';

    protected $properties = array(
        // Configuration tab.
        'kb_wiki_url' => array(
            'input',
            'input[name="kb_wiki_url"]'
        ),
        'kb_wiki_account' => array(
            'input',
            'input[name="kb_wiki_account"]'
        ),
        'kb_wiki_password' => array(
            'input',
            'input[name="kb_wiki_password"]'
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
            $this->context->visit('main.php?p=50133&o=knowledgeBase');
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
