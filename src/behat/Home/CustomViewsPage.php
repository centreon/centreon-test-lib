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

namespace Centreon\Test\Behat\Home;

class CustomViewsPage extends \Centreon\Test\Behat\Page
{
    private $context;

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
            'Current page does not match class ' . __CLASS__
        );
    }

    /**
     *  Check that the current page is matching this class.
     *
     * @return True if the current page matches this class.
     */
    public function isPageValid()
    {
        return $this->context->getSession()->getPage()->has('css', '#globalView');
    }

    /**
     *  Show edit bar that is not currently visible or hide edit bar
     *  that is currently visible.
     */
    public function showEditBar($show = true)
    {
        $this->toggleEditBar($show);
        $this->context->spin(
            function ($context) use ($show) {
                $barVisible = $context->assertFind('css', 'button.addView')->isVisible();
                return $show == $barVisible;
            },
            'Toggle of edit bar did not ' . ($show ? 'show' : 'hide') . ' edit bar.'
        );
    }

    /**
     *  Show edit button status.
     */
    public function isCurrentViewEditable()
    {
        $ariadisabled = $this->context->assertFind(
            'css',
            'button.editView'
        )->getAttribute('aria-disabled');
        $buttonDisabled =  $this->context->assertFind(
            'css',
            'button.editView'
        )->hasClass('ui-state-disabled');
        return (is_null($ariadisabled) || ($ariadisabled == 'false')) && !$buttonDisabled;
    }

    /**
     *  Create a new view.
     *
     * @param $name
     * @param $columns  Column number.
     * @param $public   True for a public view, false otherwise.
     */
    public function createNewView($name, $columns = 1, $public = false)
    {
        // Find number of existing tabs.
        $tabs = count($this->context->getSession()->getPage()->findAll('css', '#tabs .tabs_header li'));

        $this->context->assertFind('css', 'button.addView')->click();
        $this->context->assertFind('css', '#formAddView input[name="name"]')->setValue($name);
        $this->context->assertFind('css',
            '#formAddView input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        $checkbox = $this->context->assertFind('css', '#formAddView input[name="public"]');
        if ($public) {
            $this->checkCheckbox($checkbox);
        } else {
            $this->uncheckCheckbox($checkbox);
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
     * @param $publicView
     * @param $shareView  Column number.
     */
    public function loadView($view)
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.addView')->isVisible();
            },
            'no button addView'
        );
        // Open popin.
        $this->context->assertFind('css', 'button.addView')->click();

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind(
                    'css',
                    '#formAddView input[name="create_load[create_load]"][value="load"]'
                );
            }
        );
        $this->context->assertFind('css', '#formAddView input[name="create_load[create_load]"][value="load"]')->click();

        // Set requested view.

        $this->context->selectToSelectTwo('#formAddView select#viewLoad', $view);

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formAddView input[name="submit"]')->isVisible();
            },
            'No submit button for load view'
        );
        $this->context->assertFind('css', '#formAddView input[name="submit"]')->click();

        $this->context->spin(
            function ($context) {
                return !$this->context->assertFind('css', '#formAddView input[name="submit"]')->isVisible();
            },
            'load view form is not submitted'
        );
    }

    /**
     *  Edit a view.
     *
     * @param $name
     * @param $columns  Column number.
     * @param $public   True for a public view, false otherwise.
     */
    public function editView($name, $columns = 1, $public = false)
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.editView')->isVisible();
            },
            'No button editView, or not visible.'
        );
        $this->context->assertFind('css', 'button.editView')->click();

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formEditView input[name="name"]');
            }
        );
        $this->context->assertFind('css', '#formEditView input[name="name"]')->setValue($name);

        $this->context->assertFind('css',
            '#formEditView input[name="layout[layout]"][value="column_' . $columns . '"]')->click();
        $checkbox = '#formEditView input[name="public"]';
        if ($public) {
            $this->checkCheckbox($checkbox);
        } else {
            $this->uncheckCheckbox($checkbox);
        }

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formEditView input[name="submit"]')->isVisible();
            },
            'No submit button for edit custom view, or not visible'
        );
        $this->context->assertFind('css', '#formEditView input[name="submit"]')->click();

        $this->context->spin(
            function ($context) {
                return !$this->context->assertFind('css', '#formEditView input[name="submit"]')->isVisible();
            },
            'The edit form is not submitted.'
        );
    }

    /**
     *  Delete a view.
     *
     */
    public function deleteView()
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.deleteView')->isVisible();
            },
            'No button deleteView, or not visible.'
        );
        $this->context->assertFind('css', 'button.deleteView')->click();
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#deleteViewConfirm button.bt_danger')->isVisible();
            },
            'No button delete in popin.'
        );
        $this->context->assertFind('css', '#deleteViewConfirm button.bt_danger')->click();
    }

    /**
     *  Add widget to view.
     *
     * @param $title   Widget title.
     * @param $widget  Widget type.
     */
    public function addWidget($title, $widget)
    {
        // Find number of existing widgets.
        $widgets = count($this->context->getSession()->getPage()->findAll('css', '.widgetTitle'));

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.addWidget');
            },
            'no button addWidget'
        );

        // Create new widget.
        $this->context->assertFind('css', 'button.addWidget')->click();
        $this->context->assertFind('css', '#formAddWidget input[name="widget_title"]')->setValue($title);
        $this->context->selectToSelectTwo('#formAddWidget select#widget_model_id', $widget);

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formAddWidget input[name="submit"]');
            },
            'No submit button for add widget'
        );
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
     * @param $user  user type.
     * @param $userGroup  user group type.
     * @param $lock  for a locked view
     */
    public function shareView($userLock = null, $userUnlock = null, $userGroupLock = null, $userGroupUnlock = null)
    {
        // Click on sharing button.
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.shareView');
            },
            'no button shareView'
        );
        $this->context->assertFind('css', 'button.shareView')->click();

        // Set user and/or user group lock/unlock.
        if (!empty($userLock)) {
            $this->context->selectToSelectTwo('#formShareView select#locked_user_id', $userLock);
        }
        if (!empty($userUnlock)) {
            $this->context->selectToSelectTwo('#formShareView select#unlocked_user_id', $userUnlock);
        }
        if (!empty($userGroupLock)) {
            $this->context->selectToSelectTwo('#formShareView select#locked_usergroup_id', $userGroupLock);
        }
        if (!empty($userGroupUnlock)) {
            $this->context->selectToSelectTwo('#formShareView select#unlocked_usergroup_id', $userGroupUnlock);
        }

        // Submit form.
        $this->context->assertFind('css', '#formShareView input[name="submit"]')->click();

        $this->context->spin(
            function ($context) {
                return !$this->context->assertFind('css', '#formShareView input[name="submit"]')->isVisible();
            },
            'share view form is not submitted'
        );

        // Wait a few seconds for asynchronous processing.
        sleep(10);
    }

    /**
     *  Toggle edit bar.
     */
    private function toggleEditBar($show)
    {
        $visible = $this->context->assertFind('css', 'button.addView')->isVisible();
        $toggleButton = $this->context->assertFind('css', '.toggleEdit a');
        if ($show != $visible) {
            $toggleButton->click();
        }
    }
}
