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
 *  Run a container and manage it.
 */
class Container
{
    private $composeFile;
    private $id;

    /**
     *  Execute a command on local host.
     */
    private function exec($cmd)
    {
        exec($cmd, $output, $returnVar);
        if ($returnVar != 0) {
            throw new \Exception('Cannot execute container control command: ' . $cmd);
        }
    }

    /**
     * Constructor.
     *
     * @param $composeFile Docker Compose file used to run the services.
     * @param $checkRoutine Check routine that will confirm that
     *                      services are running.
     */
    public function __construct($composeFile, $checkRoutine = NULL)
    {
        $this->composeFile = $composeFile;
        $this->id = uniqid();
        $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' up -d');
        if (isset($checkRoutine)) {
            $checkRoutine($this);
        }
    }

    /**
     *  Destructor.
     *
     *  Stop the container.
     */
    public function __destruct()
    {
        $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' down');
    }

    /**
     *  Copy files from container to host.
     *
     *  @param $source Source path.
     *  @param $destination Destination path.
     *  @param $service Service name.
     */
    public function copyFromContainer($source, $destination, $service)
    {
        $this->exec('docker cp ' . $this->getContainerId($service) . ':' . $source . ' ' . $destination);
    }

    /**
     *  Copy files from host to a container.
     *
     *  @param $source Source path.
     *  @param $destination Destination path.
     *  @param $service Service name.
     */
    public function copyToContainer($source, $destination, $service)
    {
        $this->exec('docker cp ' . $source . ' ' . $this->getContainerId($service) . ':' . $destination);
    }

    /**
     *  Execute a command within a container.
     *
     *  @param $cmd Command to execute.
     *  @param $service Name of service.
     */
    public function execute($cmd, $service)
    {
        exec('docker exec ' . $this->getContainerId($service) . ' ' . $cmd, $output, $returnVar);
        return array(
            'output' => $output,
            'exit_code' => $returnVar
        );
    }

    /**
     *  Get the container ID of a service.
     *
     *  @param $service Service name.
     */
    public function getContainerId($service)
    {
        return shell_exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' ps -q ' . $service . ' | tr -d "\n"');
    }

    /**
     *  Get the host port to which a container port is redirected.
     *
     *  @param $containerPort Container port.
     *
     *  @return Host port.
     */
    public function getPort($containerPort, $service)
    {
        return shell_exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' port ' . $service . ' ' . $containerPort . ' | cut -d : -f 2 | tr -d "\n"');
    }
}
