<?php
/*
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
use Exception;

if (!defined('DOCKER_REGISTRY')) {
    define('DOCKER_REGISTRY', 'ci.int.centreon.com:5000');
}

/**
 * Class
 *
 * @class RestApiContext
 * @package Centreon\Test\Behat
 */
class RestApiContext extends CentreonContext
{
    /** @var string */
    protected $dockerImage = 'registry.centreon.com/postman/newman_alpine33:latest';
    /** @var string */
    protected $dockerNetwork = 'webdriver_default';
    /** @var string */
    protected $postmanEnv = 'Test1';
    /** @var int */
    protected $apiReturnValue;
    /** @var string */
    protected $apiLogfilePrefix = 'rest_api_log_';
    /** @var string */
    protected $logfile;

    /**
     * RestApiContext constructor
     *
     * @param array $parameters The list of parameters given in behat.yml
     */
    public function __construct($parameters = array())
    {
        parent::__construct($parameters);
    }

    /**
     * @Given a Centreon server with REST API testing data
     *
     * @return void
     * @throws Exception
     */
    public function aCentreonServerWithRestApiTestingData(): void
    {
        // Launch container.
        $this->launchCentreonWebContainer('docker_compose_web', [], ['CENTREON_DATASET' => '0']);

        // Copy images.
        $basedir = 'tests/rest_api/images';
        $imgdirs = scandir($basedir);
        foreach ($imgdirs as $dir) {
            if (($dir != '.') && ($dir != '..')) {
                $this->container->copyToContainer(
                    $basedir . '/' . $dir,
                    '/usr/share/centreon/www/img/media/' . $dir,
                    $this->webService
                );
            }
        }

        // Copy MIB.
        $this->container->copyToContainer(
            'tests/rest_api/IF-MIB.txt',
            '/usr/share/centreon/IF-MIB.txt',
            $this->webService
        );

        // Synchronize images.
        $this->iAmLoggedIn();
        $page = new ImageListingPage($this);
        $page->synchronize();
    }

    /**
     * @When the REST API :collection is called
     *
     * @param $collection
     *
     * @return void
     */
    public function theRestApiIsCalled($collection): void
    {
        $this->logfile = tempnam('/tmp', $this->apiLogfilePrefix . $collection);
        $cmd = 'docker run --rm' .
            ' --network ' . $this->dockerNetwork .
            ' -v "' . realpath('.') . '/features/api:/etc/newman"' .
            ' ' . $this->dockerImage .
            ' run "collections/' . $collection . '.postman_collection.json"' .
            ' --reporter-cli-no-assertions' .
            ' --environment="environment/' . $this->postmanEnv . '.postman_environment.json"' .
            ' --global-var "url=' . $this->container->getContainerId($this->webService, false) . '"';
        exec($cmd, $output, $returnValue);
        file_put_contents($this->logfile, implode("\n", $output));
        $this->apiReturnValue = $returnValue;
    }

    /**
     * @Then it replies as per specifications
     *
     * @return void
     * @throws Exception
     */
    public function theyItRepliesAsPerSpecifications(): void
    {
        if (!($this->apiReturnValue == 0)) {
            copy(
                $this->logfile,
                $this->composeFiles['log_directory'] . '/' . basename($this->logfile) . '.txt'
            );
            unlink($this->logfile);
            throw new Exception(
                'REST API are not working properly. Check newman log file for more details.'
            );
        }
        unlink($this->logfile);
    }
}
