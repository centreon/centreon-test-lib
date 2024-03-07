<?php

/*
 * Copyright 2005 - 2020 Centreon (https://www.centreon.com/)
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
 *
 * For more information : contact@centreon.com
 *
 */

namespace Centreon\Test\Behat\Api\Context;

use Centreon\Test\Behat\Container;
use Behat\Gherkin\Node\PyStringNode;

Trait CentreonClapiContextTrait
{
    /**
     * @return Container
     */
    abstract protected function getContainer();

    /**
     * Use clapi to import given data
     *
     * @Given the following CLAPI import data:
     */
    public function theFollowingClapiImportData(PyStringNode $data): void
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        file_put_contents($path, $data->getRaw());

        $this->getContainer()->copyToContainer($path, '/tmp/clapi.txt', $this->webService);

        $this->getContainer()->execute(
            '/usr/share/centreon/bin/centreon -u admin -p Centreon!2021 -i /tmp/clapi.txt',
            $this->webService
        );

        fclose($file);
    }

    /**
     * Export and generate configuration
     *
     * @Given the configuration is generated and exported
     */
    public function theConfigurationIsGeneratedAndExported(): void
    {
        $this->getContainer()->execute(
            '/usr/share/centreon/bin/centreon -u admin -p Centreon!2021 -a APPLYCFG -v "central"',
            $this->webService
        );

        $this->reloadAcl();
    }

    /**
     * Reload resources acl
     */
    private function reloadAcl(): void
    {
        $apacheUserCommand = 'getent passwd www-data 2>&1 > /dev/null && echo "www-data" || echo "apache"';
        $reloadAclCommand = 'su -s /bin/sh $APACHE_USER -c "/usr/bin/env php -q /usr/share/centreon/cron/centAcl.php"';

        // Reload ACL
        $this->container->execute(
            "bash -c 'APACHE_USER=$($apacheUserCommand) $reloadAclCommand'",
            $this->webService
        );
    }
}
