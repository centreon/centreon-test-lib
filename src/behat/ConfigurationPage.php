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

/**
 *  Represents a configuration page.
 *
 *  Most monitoring objects have their configuration page in Centreon.
 *  Most of them are built after the same model : multiple fields to set
 *  and a save button.
 */
interface ConfigurationPage extends Page
{
    /**
     *  Fetch the values of the fields already set in the page.
     *
     *  @return An associative array of properties.
     */
    public function getProperties();

    /**
     *  Set the values of the fields in the page.
     *
     *  @param $properties  An associative array of properties.
     */
    public function setProperties($properties);

    /**
     *  Save the configuration form.
     */
    public function save();
}
