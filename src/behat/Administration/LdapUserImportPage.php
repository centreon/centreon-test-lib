<?php


namespace Centreon\Test\Behat\Administration;

class LdapUserImportPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'input[name="ldap_search_button"]';
    
    protected $properties = array(
        'servers' => array(
            'custom',
            'Servers'
        )
    );

    /**
     *
     * @param type $context
     * @param bool|type $visit
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=60301&o=li');
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
     *  Get ldap servers.
     *
     *  @return servers
     */
    protected function getServers()
    {
        $servers = array();

        $elements = $this->context->getSession()->getPage()->findAll('css', 'input[type="checkbox"][name^="ldapConf"]');
        foreach ($elements as $checkbox) {
            $checkboxName = $checkbox->getAttribute('name');
            if (preg_match('/ldapConf\[(\d+)\]/', $checkboxName, $matches)) {
                $isChecked = $checkbox->isChecked();
                $element = $checkbox->getParent();
                $id = $matches[1];
                $name = $element->getText();
                if (preg_match('/(.+)Filter/', $name, $matches2)) {
                    $name = trim($matches2[1]);
                }
                $filter = $this->context->assertFindIn($element, 'css', 'input[type="text"]')->getValue();
                $servers[$name] = array(
                    'id' => $id,
                    'checked' => $isChecked,
                    'filter' => $filter
                );
            }
        }

        return $servers;
    }

    /**
     *  Set ldap servers.
     *
     *  @param $servers Servers.
     */
    protected function setServers($servers)
    {
        $currentServers = $this->getServers();
        foreach ($servers as $name => $properties) {
            if (isset($properties['filter'])) {
                $this->context->assertFind(
                    'css',
                    'input[name="ldap_search_filter[' . $currentServers[$name]['id'] . ']"]'
                )->setValue($properties['filter']);
            }
            if (isset($properties['checked'])) {
                $checkbox = $this->context->assertFind(
                    'css',
                    'input[name="ldapConf[' . $currentServers[$name]['id'] . ']"]'
                );
                if ($properties['checked']) {
                    $checkbox->check();
                } else {
                    $checkbox->uncheck();
                }
            }
        }
    }
}

