<?php

namespace Centreon\Test\Behat\Configuration;

use Centreon\Test\Behat\CentreonContext;

class CommentListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="search_host"]';

    protected $properties = array(
        'hostname' => array(
            'text',
            'td:nth-child(2)'
        ),
        'service_description' => array(
            'text',
            'td:nth-child(3)'
        ),
        'entry_time' => array(
            'text',
            'td:nth-child(4)'
        ),
        'author' => array(
            'text',
            'td:nth-child(5)'
        ),
        'comment' => array(
            'text',
            'td:nth-child(6)'
        ),
        'persistent' => array(
            'text',
            'td:nth-child(7)'
        )
    );

    /**
     * Navigate to the comments listing page.
     *
     * @param CentreonContext $context  Centreon context.
     * @param boolean $visit Set true to visit the comments listing page.
     * @throws \Exception
     */
    public function __construct(CentreonContext $context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=21002');
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
