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
namespace Centreon\Test\Mock;

/**
 * Mock class for resultset
 *
 * @author Centreon
 * @version 1.0.0
 * @package centreon-license-manager
 * @subpackage test
 */
class CentreonUser extends \CentreonUser
{
    public function __construct($user)
    {
        $this->user_id = $user["contact_id"];
        $this->name = html_entity_decode($user["contact_name"], ENT_QUOTES, "UTF-8");
        $this->alias = html_entity_decode($user["contact_alias"], ENT_QUOTES, "UTF-8");
        $this->email = html_entity_decode($user["contact_email"], ENT_QUOTES, "UTF-8");
        $this->lang = $user["contact_lang"];
        $this->charset = "UTF-8";
        $this->passwd = $user["contact_passwd"];
        $this->token = $user['contact_autologin_key'];
        $this->admin = $user["contact_admin"];
        $this->version = 3;
        $this->default_page = $user["default_page"];
        $this->gmt = $user["contact_location"];
        $this->js_effects = $user["contact_js_effects"];
        $this->is_admin = null;
        $this->theme = $user['contact_theme'];
    }
}
