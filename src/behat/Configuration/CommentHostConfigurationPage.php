<?php

namespace Centreon\Test\Behat\Configuration;

use Centreon\Test\Behat\CentreonContext;

class CommentHostConfigurationPage extends \Centreon\Test\Behat\ConfigurationPage
{
    protected $validField = 'textarea[name="comment"]';

    protected $properties = array(
        'comment' => array(
            'input',
            'textarea[name="comment"]'
        )
    );

    /**
     * Navigate to the comment creation page and/or check that it matches this class.
     *
     * @param CentreonContext $context Centreon context.
     * @param string $host Host name. If empty, current page will not be changed.
     * @throws \Exception
     */
    public function __construct($context, $host = '')
    {
        // Visit page.
        $this->context = $context;
        if (!empty($host)) {
            $this->context->visit('main.php?p=21002&o=ah&host_name=' . $host);
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
