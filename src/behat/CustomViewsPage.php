<?php
/**
 * Copyright 2016 Centreon
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

namespace Centreon\Test\Behat;

class CustomViewsPage implements Page
{
    protected $context;

    /**
     *  Navigate to and/or check that we are on the custom views page.
     */
    public function __construct($context, $visit = true)
    {
        // Visit page.
        $this->context = $context;
        if ($visit) {
            $this->context->visit('main.php?p=103');
        }

        // Check that page is valid for this class.
        $mythis = $this;
        $this->context->spin(
            function ($context) use ($mythis) {
                return $mythis->isPageValid();
            },
            30,
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
        return $this->context->getSession()->getPage()->has('css', '#globalView');
    }

    /**
     *  Show/hide edit bar.
     */
    public function showEditBar($show)
    {
        $hasBar = $this->context->getSession()->getPage()->has('css', '#actionBar');
        $editButton = '.toggleEdit a';
        if (($show && !$hasBar) || (!$show && $hasBar)) {
            $this->context->assertFind('css', $editButton)->click();
        }
    }

    /**
     *  Create a new view.
     *
     *  @param $name
     *  @param $columns  Column number.
     *  @param $public   True for a public view, false otherwise.
     */
    public function createNewView($name, $columns = 1, $public = false)
    {
        $this->context->assertFind('css', 'button.addView')->click();
        $this->context->assertFind('css', 'input[name="name"]')->setValue($name);
        $this->context->assertFind('css', 'input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        if ($public) {
            $this->context->assertFind('css', 'input[name="public"]')->check();
        } else {
            $this->context->assertFind('css', 'input[name="public"]')->uncheck();
        }
        $this->context->assertFind('css', 'input[name="submit"]')->click();
    }

    /**
     *  Load a view.
     *
     *  @param $publicView
     *  @param $shareView  Column number.
     */
    public function loadView($publicView, $shareView)
    {
        $this->context->assertFind('css', 'button.addView')->click();
        $this->context->assertFind('css', 'input[name="create_load[create_load][value="load"]"')->click();

        $this->context->selectInList('input[name="viewLoad"]', $publicView);
        $this->context->selectInList('input[name="viewLoadShare"]', $shareView);

        $this->context->assertFind('css', 'input[name="submit"]')->click();
    }

    /**
     *  Edit a view.
     *
     *  @param $name
     *  @param $columns  Column number.
     *  @param $public   True for a public view, false otherwise.
     */
    public function editView($name, $columns = 1, $public = false)
    {
        $this->context->assertFind('css', 'button.editView')->click();

        $this->context->assertFind('css', 'input[name="name"]')->setValue($name);
        $this->context->assertFind('css', 'input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        if ($public) {
            $this->context->assertFind('css', 'input[name="public"]')->check();
        } else {
            $this->context->assertFind('css', 'input[name="public"]')->uncheck();
        }
        $this->context->assertFind('css', 'input[name="submit"]')->click();
    }

    /**
     *  Delete a view.
     *
     */
    public function deleteView()
    {
        $this->context->assertFind('css', 'button.editView')->click();
        $this->context->assertFind('css', '.button_group_center button.bt_danger')->click();
    }

    /**
     *  Add widget to view.
     *
     *  @param $title   Widget title.
     *  @param $widget  Widget type.
     */
    public function addWidget($title, $widget)
    {
        $this->context->assertFind('css', 'button.addWidget')->click();
        $this->context->assertFind('css', 'input[name="widget_title"]')->setValue($title);
        $this->context->selectToSelectTwo('select#widget_model_id', $widget);
        $this->context->assertFind('css', 'input[name="submit"]')->click();
    }

    /**
     *  Share a custom view.
     *
     *  @param $lock  for a locked view
     *  @param $user  user type.
     *  @param $userGroup  user group type.
     */
    public function shareView($lock = 1, $user, $userGroup)
    {
        $this->context->assertFind('css', 'button.shareView')->click();
        $this->context->assertFind('css', 'input[name="locked"]')->setValue($lock);
        $this->context->selectToSelectTwo('select#user_id', $user);
        $this->context->selectToSelectTwo('select#usergroup_id', $userGroup);
        $this->context->assertFind('css', 'input[name="submit"]')->click();
    }
}
