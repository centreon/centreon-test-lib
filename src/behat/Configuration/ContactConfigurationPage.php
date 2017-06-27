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

class ContactConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    const TAB_CONFIGURATION = 1;
    const TAB_AUTHENTICATION = 2;
    const TAB_EXTENDED = 3;

    protected $validField = 'input[name="contact_name"]';

    protected $properties = array(
        // Configuration tab.
        'alias' => array(
            'text',
            'input[name="contact_alias"]',
            self::TAB_CONFIGURATION
        ),
        'name' => array(
            'text',
            'input[name="contact_name"]',
            self::TAB_CONFIGURATION
        ),
        'email' => array(
            'text',
            'input[name="contact_email"]',
            self::TAB_CONFIGURATION
        ),
        'notifications_enabled' => array(
            'radio',
            'input[name="contact_enable_notifications[contact_enable_notifications]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_recovery' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[r]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_down' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[d]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notification_period' => array(
            'select2',
            'select#timeperiod_tp_id',
            self::TAB_CONFIGURATION
        ),
        'host_notification_command' => array(
            'select2',
            'select#contact_hostNotifCmds',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_recovery' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[r]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_critical' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[c]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notification_period' => array(
            'select2',
            'select#timeperiod_tp_id2',
            self::TAB_CONFIGURATION
        ),
        'service_notification_command' => array(
            'select2',
            'select#contact_svNotifCmds',
            self::TAB_CONFIGURATION
        ),
        'password' => array(
            'text',
            'input#passwd1',
            self::TAB_AUTHENTICATION,
            false // Do not exist on ldap contact
        ),
        'password2' => array(
            'text',
            'input#passwd2',
            self::TAB_AUTHENTICATION,
            false // Do not exist on ldap contact
        ),
        'dn' => array(
            'text',
            'input[name="contact_ldap_dn"]',
            self::TAB_AUTHENTICATION,
            false // Do not exist when no ldap is configured
        ),
        'access' => array(
            'radio',
            'input[name="contact_oreon[contact_oreon]"]',
            self::TAB_AUTHENTICATION
        ),
        'admin' => array(
            'radio',
            'input[name="contact_admin[contact_admin]"]',
            self::TAB_AUTHENTICATION
        ),
        'location' => array(
            'select2',
            'select#contact_location',
            self::TAB_AUTHENTICATION
        ),
        'status' => array(
            'radio',
            'input[name="contact_activate[contact_activate]"]',
            self::TAB_EXTENDED
        )
    );

    /**
     * @var string
     */
    protected $listingClass = '\Centreon\Test\Behat\Configuration\ContactConfigurationListingPage';

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
            $this->context->visit('main.php?p=60301&o=a');
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
