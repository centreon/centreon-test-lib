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

class ImageListingPage implements ListingPage
{
    private $context;

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
     *  Check that the current page is matching this class.
     *
     *  @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', 'input[name="searchM"]');
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
     *  Get properties of an image.
     *
     * @param $image  Image name.
     * @return An array of properties.
     * @throws \Exception
     */
    public function getEntry($image)
    {
        $images = $this->getEntries();
        if (!array_key_exists($image, $images)) {
            throw new \Exception('could not find image ' . $image);
        }
        return $images[$image];
    }

    /**
     *  Edit a template.
     *
     *  @param $name  Image name.
     */
    public function inspect($name)
    {
        $this->context->assertFindLink($name)->click();
    }
}
