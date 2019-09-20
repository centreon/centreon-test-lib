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
            'input',
            'input[name="contact_alias"]',
            self::TAB_CONFIGURATION
        ),
        'name' => array(
            'input',
            'input[name="contact_name"]',
            self::TAB_CONFIGURATION
        ),
        'email' => array(
            'input',
            'input[name="contact_email"]',
            self::TAB_CONFIGURATION
        ),
        'pager' => array(
            'input',
            'input[name="contact_pager"]',
            self::TAB_CONFIGURATION
        ),
        'contact_template' => array(
            'select',
            'select[name="contact_template_id"]',
            self::TAB_CONFIGURATION
        ),
        'contact_groups' => array(
            'select2',
            'select[name="contact_cgNotif[]"]',
            self::TAB_CONFIGURATION
        ),
        'notifications_enabled' => array(
            'radio',
            'input[name="contact_enable_notifications[contact_enable_notifications]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_down' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[d]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_unreachable' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[u]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_recovery' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[r]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_flapping' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[f]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_downtime_scheduled' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[s]"]',
            self::TAB_CONFIGURATION
        ),
        'host_notify_on_none' => array(
            'checkbox',
            'input[name="contact_hostNotifOpts[n]"]',
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
        'service_notify_on_warning' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[w]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_unknown' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[u]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_critical' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[c]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_recovery' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[r]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_flapping' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[f]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_downtime_scheduled' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[s]"]',
            self::TAB_CONFIGURATION
        ),
        'service_notify_on_none' => array(
            'checkbox',
            'input[name="contact_svNotifOpts[n]"]',
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
        // Authentication tab
        'access' => array(
            'radio',
            'input[name="contact_oreon[contact_oreon]"]',
            self::TAB_AUTHENTICATION
        ),
        'password' => array(
            'input',
            'input#passwd1',
            self::TAB_AUTHENTICATION,
            false // Do not exist on ldap contact
        ),
        'password2' => array(
            'input',
            'input#passwd2',
            self::TAB_AUTHENTICATION,
            false // Do not exist on ldap contact
        ),
        'language' => array(
            'select',
            'select[name="contact_lang"]',
            self::TAB_AUTHENTICATION
        ),
        'location' => array(
            'select2',
            'select#contact_location',
            self::TAB_AUTHENTICATION
        ),
        'autologin_key' => array(
            'input',
            'input#aKey',
            self::TAB_AUTHENTICATION
        ),
        'authentication_source' => array(
            'select',
            'select[name="contact_auth_type"]',
            self::TAB_AUTHENTICATION
        ),
        'dn' => array(
            'input',
            'input[name="contact_ldap_dn"]',
            self::TAB_AUTHENTICATION,
            false // Do not exist when no ldap is configured
        ),
        'admin' => array(
            'radio',
            'input[name="contact_admin[contact_admin]"]',
            self::TAB_AUTHENTICATION
        ),
        'reach_API' => array(
            'radio',
            'input[name="reach_api[reach_api]"]',
            self::TAB_AUTHENTICATION
        ),
        'acl_groups' => array(
            'select2',
            'select#contact_acl_groups',
            self::TAB_AUTHENTICATION
        ),
        // Extended tab
        'address1' => array(
            'input',
            'input[name="contact_address1"]',
            self::TAB_EXTENDED
        ),
        'address2' => array(
            'input',
            'input[name="contact_address2"]',
            self::TAB_EXTENDED
        ),
        'address3' => array(
            'input',
            'input[name="contact_address3"]',
            self::TAB_EXTENDED
        ),
        'address4' => array(
            'input',
            'input[name="contact_address4"]',
            self::TAB_EXTENDED
        ),
        'address5' => array(
            'input',
            'input[name="contact_address5"]',
            self::TAB_EXTENDED
        ),
        'address6' => array(
            'input',
            'input[name="contact_address6"]',
            self::TAB_EXTENDED
        ),
        'enabled' => array(
            'radio',
            'input[name="contact_activate[contact_activate]"]',
            self::TAB_EXTENDED
        ),
        'comments' => array(
            'input',
            'textarea[name="contact_comment"]',
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
     * @param $context  Centreon context.
     * @param bool $visit True to navigate to a blank configuration page.
     */
    public function __construct($context, $visit = true)
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
