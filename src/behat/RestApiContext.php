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

use CentreonContext;

class RestApiContext extends CentreonContext
{
    protected $defaultNetwork = 'webdriver_default';
    protected $apiReturnValue;
    protected $apiLogfilePrefix = 'rest_api_log_';

    /**
     * Constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     * @Given a Centreon server with REST API testing data
     */
    public function aCentreonServerWithRestApiTestingData()
    {
        // Launch container.
        $this->launchCentreonWebContainer('web_fresh');

        // Copy images.
        $basedir = 'tests/rest_api/images';
        $imgdirs = scandir($basedir);
        foreach ($imgdirs as $dir) {
            if (($dir != '.') && ($dir != '..')) {
                $this->container->copyToContainer(
                    $basedir . '/' . $dir,
                    '/usr/share/centreon/www/img/media/' . $dir,
                    'web'
                );
            }
        }

        // Copy MIB.
        $this->container->copyToContainer(
            'tests/rest_api/IF-MIB.txt',
            '/usr/share/centreon/IF-MIB.txt',
            'web'
        );

        // Synchronize images.
        $this->iAmLoggedIn();
        $page = new ImageListingPage($this);
        $page->synchronize();
    }

    /**
     * @When call REST API ([\w+_-]+) with data ([\w+_-]+) on ([\w+:_-]+)
     */
    public function callRESTAPIWithData($collection, $env, $docker)
    {
        $this->logfile = tempnam('/tmp', $this->apiLogfilePrefix . $collection);
        $cmd = 'docker run -e POSTMAN_COLLECTION="' . $collection .
            '" -e POSTMAN_ENV="' . $env . '" -e CENTREON_URL="' . $this->container->getHost() . ':' .
            $this->containter->getPort('80', 'web') . '" ci.int.centreon.com:5000/' . $docker;
        exec($cmd, $output. $returnValue);
        file_put_contents($this->logfile, $output);
        $this->apiReturnValue = $returnValue;
    }

    /**
     * @Then they reply as per specifications
     */
    public function theyReplyAsPerSpecifications()
    {
        if (!($this->apiReturnValue == 0)) {
            copy(
                $this->logfile,
                $this->composeFiles['log_directory'] . '/' . basename($this->logfile) . '.txt'
            );
            unlink($this->logfile);
            throw new \Exception(
                'REST API are not working properly. Check newman log file for more details.'
            );
        }
        unlink($this->logfile);
    }
}
