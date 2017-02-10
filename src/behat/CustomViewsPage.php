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
    public function showEditBar($show = true)
    {
        $hasBar = $this->context->assertFind('css', '#actionBar')->isVisible();
        $editButton = '.toggleEdit a';
        if (($show && !$hasBar) || (!$show && $hasBar)) {
            $this->context->assertFind('css', $editButton)->click();
        }
        $this->context->spin(
            function ($context) use ($show) {
                return $this->context->assertFind('css', '#actionBar')->isVisible() == $show;
            }
        );
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
        // Find number of existing tabs.
        $tabs = count($this->context->getSession()->getPage()->findAll('css', '#tabs .tabs_header li'));

        // Create new view (new tab).
        $this->showEditBar();
        $this->context->assertFind('css', 'button.addView')->click();
        $this->context->assertFind('css', '#formAddView input[name="name"]')->setValue($name);
        $this->context->assertFind('css', '#formAddView input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        if ($public) {
            $this->context->assertFind('css', '#formAddView input[name="public"]')->check();
        } else {
            $this->context->assertFind('css', '#formAddView input[name="public"]')->uncheck();
        }
        $this->context->assertFind('css', '#formAddView input[name="submit"]')->click();

        // Wait for new tab to appear.
        $tabs += 1;
        $this->context->spin(
            function ($context) use ($tabs) {
                return count($this->context->getSession()->getPage()->findAll(
                    'css',
                    '#tabs .tabs_header li'))
                    >= $tabs;
            }
        );
    }

    /**
     *  Load a view.
     *
     *  @param $publicView
     *  @param $shareView  Column number.
     */
    public function loadView($publicView = null, $sharedView = null)
    {
        // Open popin.
        $this->context->assertFind('css', 'button.addView')->click();
        $this->context->assertFind('css', '#formAddView input[name="create_load[create_load]"][value="load"]')->click();

        // Set requested view.
        if (!empty($publicView)) {
            $this->context->selectInList('#formAddView select[name="viewLoad"]', $publicView);
        }
        if (!empty($sharedView)) {
            $this->context->selectInList('#formAddView select[name="viewLoadShare"]', $sharedView);
        }

        // Submit form.
        $this->context->assertFind('css', '#formAddView input[name="submit"]')->click();
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

        $this->context->assertFind('css', '#formEditView input[name="name"]')->setValue($name);
        $this->context->assertFind('css', '#formEditView input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        if ($public) {
            $this->context->assertFind('css', '#formEditView input[name="public"]')->check();
        } else {
            $this->context->assertFind('css', '#formEditView input[name="public"]')->uncheck();
        }
        $this->context->assertFind('css', '#formEditView input[name="submit"]')->click();
    }

    /**
     *  Delete a view.
     *
     */
    public function deleteView()
    {
        $this->context->assertFind('css', 'button.deleteView')->click();
        $this->context->assertFind('css', '#deleteViewConfirm button.bt_danger')->click();
    }

    /**
     *  Add widget to view.
     *
     *  @param $title   Widget title.
     *  @param $widget  Widget type.
     */
    public function addWidget($title, $widget)
    {
        // Find number of existing widgets.
        $widgets = count($this->context->getSession()->getPage()->findAll('css', '.widgetTitle'));

        // Create new widget.
        $this->context->assertFind('css', 'button.addWidget')->click();
        $this->context->assertFind('css', '#formAddWidget input[name="widget_title"]')->setValue($title);
        $this->context->selectToSelectTwo('#formAddWidget select#widget_model_id', $widget);
        $this->context->assertFind('css', '#formAddWidget input[name="submit"]')->click();

        // Wait for new widget to appear.
        $widgets += 1;
        $this->context->spin(
            function ($context) use ($widgets) {
                return count($this->context->getSession()->getPage()->findAll(
                    'css',
                    '.widgetTitle'))
                    >= $widgets;
            }
        );
    }

    /**
     *  Share a custom view.
     *
     *  @param $user  user type.
     *  @param $userGroup  user group type.
     *  @param $lock  for a locked view
     */
    public function shareView($user = null, $userGroup = null, $lock = 1)
    {
        $this->context->assertFind('css', 'button.shareView')->click();

        $this->context->assertFind('css', '#formShareView input[name="locked[locked]"][value=' . $lock . ']')->click();

        if (!empty($user)) {
            $this->context->selectToSelectTwo('#formShareView select#user_id', $user);
        }

        if (!empty($userGroup)) {
            $this->context->selectToSelectTwo('#formShareView select#usergroup_id', $userGroup);
        }

        $this->context->assertFind('css', '#formShareView input[name="submit"]')->click();
    }
}
