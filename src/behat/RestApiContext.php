<?php
/**
 * Copyright 2016-2018 Centreon
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

use Centreon\Test\Behat\CentreonContext;
use Centreon\Test\Behat\Administration\ImageListingPage;

if (!defined('DOCKER_REGISTRY')) {
    define('DOCKER_REGISTRY', 'ci.int.centreon.com:5000');
}

class RestApiContext extends CentreonContext
{
    protected $dockerImage = 'registry.centreon.com/postman/newman_alpine33:latest';
    protected $dockerNetwork = 'webdriver_default';
    protected $postmanEnv = 'Test1';
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
        $this->launchCentreonWebContainer(__DIR__ . '/../../../../../docker-compose.yml');

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
     * @When the REST API :collection is called
     */
    public function theRestApiIsCalled($collection)
    {
        $this->logfile = tempnam('/tmp', $this->apiLogfilePrefix . $collection);
        $cmd = 'docker run --rm' .
            ' --network ' . $this->dockerNetwork .
            ' -v "' . realpath('.') . '/features/api:/etc/newman"' .
            ' ' . $this->dockerImage .
            ' run "collections/' . $collection . '.postman_collection.json"' .
            ' --reporter-cli-no-assertions' .
            ' --environment="environment/' . $this->postmanEnv . '.postman_environment.json"' .
            ' --global-var "url=' . $this->container->getContainerId('web', false) . '"';
        exec($cmd, $output, $returnValue);
        file_put_contents($this->logfile, implode("\n", $output));
        $this->apiReturnValue = $returnValue;
    }

    /**
     * @Then it replies as per specifications
     */
    public function theyItRepliesAsPerSpecifications()
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
