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

class TimeperiodConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_CONFIGURATION = 1;
    const TAB_EXCEPTION = 2;

    protected $validField = 'input[name="tp_name"]';

    protected $properties = array(
        // Configuration tab.
        'name' => array(
            'input',
            'input[name="tp_name"]',
            self::TAB_CONFIGURATION
        ),
        'alias' => array(
            'input',
            'input[name="tp_alias"]',
            self::TAB_CONFIGURATION
        ),
        'sunday' => array(
            'input',
            'input[name="tp_sunday"]',
            self::TAB_CONFIGURATION
        ),
        'monday' => array(
            'input',
            'input[name="tp_monday"]',
            self::TAB_CONFIGURATION
        ),
        'tuesday' => array(
            'input',
            'input[name="tp_tuesday"]',
            self::TAB_CONFIGURATION
        ),
        'wednesday' => array(
            'input',
            'input[name="tp_wednesday"]',
            self::TAB_CONFIGURATION
        ),
        'thursday' => array(
            'input',
            'input[name="tp_thursday"]',
            self::TAB_CONFIGURATION
        ),
        'friday' => array(
            'input',
            'input[name="tp_friday"]',
            self::TAB_CONFIGURATION
        ),
        'saturday' => array(
            'input',
            'input[name="tp_saturday"]',
            self::TAB_CONFIGURATION
        ),
        'templates' => array(
            'select2',
            'select#tp_include',
            self::TAB_CONFIGURATION
        ),
        'exceptions' => array(
            'custom',
            'Exceptions',
            self::TAB_EXCEPTION
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\TimeperiodConfigurationListingPage';

    /**
     *  Navigate to and/or check that we are on a timeperiod configuration page.
     *
     * @param $context  Centreon context.
     * @param $visit    True to navigate to a blank timeperiods configuration page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60304&o=a');
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
     * get exceptions
     */
    protected function getExceptions()
    {
        $exceptions = array();
        $i = 0;
        while (true) {
            $exception = array();
            $day = $this->context->getSession()->getPage()->findField('exceptionInput_' . $i);
            if (is_null($day)) {
                break;
            }
            $exception['day'] = $day->getValue();
            $exception['timeRange'] =
                $this->context->getSession()->getPage()->findField('exceptionTimerange_' . $i)->getValue();
            $i++;
            $exceptions[] = $exception;
        }
        return $exceptions;
    }

    /**
     * set exceptions
     */
    protected function setExceptions($exceptionsArray)
    {
        $currentExceptions = $this->getExceptions();
        $i = count($currentExceptions);
        foreach ($exceptionsArray as $array) {
            $this->context->getSession()->evaluateScript('document.querySelector("#tab2 .list_two .FormRowValue span").click()');
            $this->context->assertFindField('exceptionInput_' . $i)->setValue($array['day']);
            $this->context->assertFindField('exceptionTimerange_' . $i)->setValue($array['timeRange']);
            $i++;
        }
    }
}
