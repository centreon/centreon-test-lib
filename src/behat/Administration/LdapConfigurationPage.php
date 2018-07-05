<?php


namespace Centreon\Test\Behat\Administration;

class LdapConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
   protected $validField = 'input[name="ar_name"]';

   protected $listingClass =
        '\Centreon\Test\Behat\Administration\LdapConfigurationListingPage';

   protected $properties = array(
        // Configuration fields. general information
        'configuration_name' => array(
            'input',
            'input[name="ar_name"]'
        ),
        'description' => array(
            'input',
            'textarea[name="ar_description"]'
        ),
        'enable_authentication' => array(
            'radio',
            'input[name="ldap_auth_enable[ldap_auth_enable]"]'
        ),
        'store_password' => array(
            'radio',
            'input[name="ldap_store_password[ldap_store_password]"]'
        ),
        'auto_import' => array(
            'radio',
            'input[name="ldap_auto_import[ldap_auto_import]"]'
        ),
        'search_size_limit' => array(
            'input',
            'input[name="ldap_search_limit"]'
        ),
        'search_timeout' => array(
            'input',
            'input[name="ldap_search_timeout"]'
        ),
        'use_service_dns' => array(
            'radio',
            'input[name="ldap_srv_dns[ldap_srv_dns]"]'
        ),
         // LDAP servers
        'servers_host_address' => array(
            'input',
            'input[name="address[0]"]'
        ),
        'servers_host_port' => array(
            'input',
            'input[name="port[0]"]'
        ),
        'server_ssl' => array(
            'checkbox',
            'input[name="ssl[0]"]'
        ),
       //LDAP Information
        'bind_user' => array(
            'input',
            'input[name="bind_dn"]'
        ),
        'bind_password' => array(
            'input',
            'input[name="bind_pass"]'
        ),
        'protocol_version' => array(
            'select',
            'select[name="protocol_version"]'
        ),
        'template' => array(
            'select',
            'select#ldap_template'
        ),
        'search_user_base_dn' => array(
            'input',
            'input[name="user_base_search"]'
        ),
        'search_group_base_dn' => array(
            'input',
            'input[name="group_base_search"]'
        ),
        'user_filter' => array(
            'input',
            'input[name="user_filter"]'
        ),
        'Login attribute' => array(
            'input',
            'input[name="alias"]'
        ),
        'user_group_attribute' => array(
            'input',
            'input[name="user_group"]'
        ),
        'user_displayname_attribute' => array(
            'input',
            'input[name="user_name"]'
        ),
        'user_firstname_attribute' => array(
            'input',
            'input[name="user_firstname"]'
        ),
        'user_lastname_attribute' => array(
            'input',
            'input[name="user_lastname"]'
        ),
        'user_email_attribute' => array(
            'input',
            'input[name="user_email"]'
        ),
        'user_pager_attribute' => array(
            'input',
            'input[name="user_pager"]'
        ),
       'group_filter' => array(
            'input',
            'input[name="group_filter"]'
        ),
       'group_attribute' => array(
            'input',
            'input[name="group_name"]'
        ),
       'group_member_attribute' => array(
            'input',
            'input[name="group_member"]'
        )
    );

   /**
    *
    * @param type $context
    * @param type $visit
    */
   public function __construct($context, $visit = TRUE)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50113&o=ldap&new=1');
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