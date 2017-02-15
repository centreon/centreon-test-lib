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
        $this->toggleEditBar();
        $this->spin(
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
        return is_null($ariadisabled) || ($ariadisabled == 'false');
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
     * @param $publicView
     * @param $shareView  Column number.
     */
    public function loadView($publicView = null, $sharedView = null)
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.addView');
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
        if (!empty($publicView)) {
            $this->context->spin(
                function ($context) use ($publicView) {
                    if (count($this->context->getSession()->getPage()->findAll(
                            'css',
                            '#formAddView select[name="viewLoad"] option'
                        )) == 2
                    ) {
                        $this->context->selectInList('#formAddView select[name="viewLoad"]', $publicView);
                        return true;
                    }
                },
                'No public view in select list'
            );
        }

        if (!empty($sharedView)) {
            $this->context->spin(
                function ($context) use ($sharedView) {
                    if (count($this->context->getSession()->getPage()->findAll(
                            'css',
                            '#formAddView select[name="viewLoadShare"] option'
                        )) == 2
                    ) {
                        $this->context->selectInList('#formAddView select[name="viewLoadShare"]', $sharedView);
                        return true;
                    }
                },
                'No shared view in select list'
            );
        }

        // Submit form.
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind(
                    'css',
                    '#formAddView input[name="submit"]'
                );
            },
            'No submit button for add/load custom view'
        );
        $this->context->assertFind('css', '#formAddView input[name="submit"]')->click();
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
                return $this->context->assertFind('css', 'button.editView');
            },
            'no button editView'
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
        if ($public) {
            $this->context->assertFind('css', '#formEditView input[name="public"]')->check();
        } else {
            $this->context->assertFind('css', '#formEditView input[name="public"]')->uncheck();
        }

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formEditView input[name="submit"]');
            },
            'No submit button for edit custom view'
        );

        $this->context->assertFind('css', '#formEditView input[name="submit"]')->click();
    }

    /**
     *  Delete a view.
     *
     */
    public function deleteView()
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.deleteView');
            },
            'no button deleteView'
        );
        $this->context->assertFind('css', 'button.deleteView')->click();

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#deleteViewConfirm button.bt_danger');
            },
            'no button delete in popin'
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
    public function shareView($user = null, $userGroup = null, $lock = 1)
    {
        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', 'button.shareView');
            },
            'no button shareView'
        );

        $this->context->assertFind('css', 'button.shareView')->click();

        $this->context->assertFind('css', '#formShareView input[name="locked[locked]"][value=' . $lock . ']')->click();

        if (!empty($user)) {
            $this->context->selectToSelectTwo('#formShareView select#user_id', $user);
        }

        if (!empty($userGroup)) {
            $this->context->selectToSelectTwo('#formShareView select#usergroup_id', $userGroup);
        }

        $this->context->spin(
            function ($context) {
                return $this->context->assertFind('css', '#formShareView input[name="submit"]');
            },
            'No submit button for share view'
        );
        $this->context->assertFind('css', '#formShareView input[name="submit"]')->click();
    }

    /**
     *  Toggle edit bar.
     */
    private function toggleEditBar()
    {
        $editButton = '.toggleEdit a';
        $this->context->assertFind('css', $editButton)->click();
    }
}
