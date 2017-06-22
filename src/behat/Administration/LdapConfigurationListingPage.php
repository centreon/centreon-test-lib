<?php


namespace Centreon\Test\Behat\Administration;

class LdapConfigurationListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchLdap"]';
    
    protected $properties = array(
         'configuration_name' => array(
            'text',
            'td:nth-child(2)'
        ),
        'id' => array(
            'custom'
        )
       
    );
    
    protected $objectClass = '\Centreon\Test\Behat\Administration\LdapConfigurationPage';
    

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
            $this->context->visit('main.php?p=50113&o=ldap');
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
    
    protected function getId($element)
    {
        $idComponent = $this->context->assertFindIn($element, 'css', 'input[type="checkbox"]')->getAttribute('name');
        $id = preg_match('/select\[(\d+)\]/', $idComponent, $matches) ? $matches[1] : null;

        return $id;
    }
}

