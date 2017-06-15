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

class ImageListingPage extends \Centreon\Test\Behat\ListingPage
{
    protected $validField = 'input[name="searchM"]';

    protected $properties = array(
        'name' => array(
            'text',
            'td:nth-child(2) a'
        ),
        'imageDirectory' => array(
            'custom',
            'imageDirectory'
        ),
        'imageName' => array(
            'custom',
            'imageName'
        ),
        'comment' => array(
            'text',
            'td:nth-child(4)'
        )
    );

    /**
     *  Images list page.
     *
     *  @param $context  Centreon context class.
     *  @param $visit    True to navigate to page.
     */
    public function __construct($context, $visit = true)
    {
        // Navigate.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=50102');
        }

        // Check that page is valid.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Get the list of images.
     */
    public function getEntries()
    {
        $entries = array();
        $elements = $this->context->getSession()->getPage()->findAll('css', '.list_one,.list_two');
        foreach ($elements as $element) {
            $entry = array();
            $entry['name'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(2) a')->getText();
            $imageFullPath = $this->context->assertFindIn($element, 'css', 'td:nth-child(3) a')->getText();
            list($entry['imageDirectory'], $entry['imageName']) = explode('/', $imageFullPath);
            $entry['comment'] = $this->context->assertFindIn($element, 'css', 'td:nth-child(4)')->getText();
            $entries[$entry['name']] = $entry;
        }
        return $entries;
    }

    /**
     *  Synchronize media directory.
     */
    public function synchronize()
    {
        $this->context->assertFindLink('Synchronize Media Directory')->click();
    }

    /**
     * Get image directory of an element
     *
     * @param $element
     * @return mixed
     */
    private function getImageDirectory($element)
    {
        $imageFullPath = $this->context->assertFindIn($element, 'css', 'td:nth-child(3) a')->getText();
        list($imageDirectory, $imageName) = explode('/', $imageFullPath);

        return $imageDirectory;
    }

    /**
     * Get image name of an element
     *
     * @param $element
     * @return mixed
     */
    private function getImageName($element)
    {
        $imageFullPath = $this->context->assertFindIn($element, 'css', 'td:nth-child(3) a')->getText();
        list($imageDirectory, $imageName) = explode('/', $imageFullPath);

        return $imageName;
    }
}
