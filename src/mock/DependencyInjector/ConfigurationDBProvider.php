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

namespace Centreon\Test\Mock\DependencyInjector;

class ConfigurationDBProvider implements ServiceProviderInterface
{
    private $db;

    public function __construct(\Centreon\Test\Mock\CentreonDB $db)
    {
        $this->db = $db;
    }

    public function register(\Pimple\Container $container): void
    {
        $container['configuration_db'] = $this->db;
    }

    public function terminate(\Pimple\Container $container): void
    {
        $container['configuration_db'] = null;
    }
}
