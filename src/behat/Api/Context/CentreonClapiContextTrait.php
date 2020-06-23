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
    public function theFollowingClapiImportData(PyStringNode $data)
    {
        $file = tmpfile();
        $path = stream_get_meta_data($file)['uri'];
        file_put_contents($path, $data->getRaw());

        $this->getContainer()->copyToContainer($path, '/tmp/clapi.txt', 'web');

        $this->getContainer()->execute(
            '/usr/share/centreon/bin/centreon -u admin -p centreon -i /tmp/clapi.txt',
            'web'
        );

        fclose($file);
    }

    /**
     * Export and generate configuration
     *
     * @Given the configuration is generated and exported
     */
    public function theConfigurationIsGeneratedAndExported()
    {
        $this->getContainer()->execute(
            '/usr/share/centreon/bin/centreon -u admin -p centreon -a APPLYCFG -v "central"',
            'web'
        );
    }
}
