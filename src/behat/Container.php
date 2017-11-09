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

/**
 *  Run a container and manage it.
 */
class Container
{
    private $composeFile;
    private $id;

    /**
     * Container constructor.
     * @param $composeFile Docker Compose file used to run the services.
     * @throws \Exception
     */
    public function __construct($composeFile)
    {
        $this->composeFile = $composeFile;
        $this->id = uniqid('', TRUE);

        // The current Docker release has issues with concurrent access to the Docker
        // daemon. While this gets fixed, attempt to run container creation twice
        // to ensure that it is not a transient failure.
        $errors = 0;
        do {
            try {
                $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' up -d');
                $errors = 0;
            } catch (\Exception $e) {
                if (++$errors >= 2) {
                    throw $e;
                }
                sleep(1);
            }
        } while ($errors != 0);
    }

    /**
     *  Execute a command on local host.
     */
    private function exec($cmd)
    {
        exec($cmd . ' 2>&1', $output, $returnVar);
        if ($returnVar != 0) {
            throw new \Exception('Cannot execute container control command: ' . $cmd . " \n " . implode("\n", $output) . ' (code ' . $returnVar . ')');
        }
    }

    /**
     *  Destructor.
     *
     *  Stop the container.
     */
    public function __destruct()
    {
        $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' kill');
        $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' down -v');
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
     *  @param $throwOnError Throw if an error occur.
     */
    public function execute($cmd, $service, $throwOnError = TRUE)
    {
        exec('docker exec ' . $this->getContainerId($service) . ' ' . $cmd, $output, $returnVar);
        if ($throwOnError == TRUE && $returnVar != 0) {
            throw new \Exception('Cannot execute command on container ' . $service . ': ' . $output[0] . ' (command was ' . $cmd . ').');
        }
        return array(
            'output' => implode("\n", $output),
            'exit_code' => $returnVar
        );
    }

    /**
     *  Get the container ID of a service.
     *
     *  @param $service Service name.
     */
    public function getContainerId($service, $longId = true)
    {
        exec('sh -c \'docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' ps -q ' . $service . ' | tr -d "\n"\'', $output, $returnVar);
        if ($returnVar != 0) {
            throw new \Exception('Cannot retrieve container ID of service ' . $service . ': ' . $output[0] . '.');
        }
        if (!$longId) {
            $output[0] = substr($output[0], 0, 12);
        }
        return $output[0];
    }

    /**
     *  Get the container(s) logs.
     *
     *  @param $service Service name.
     *
     *  @return The logs as a string.
     */
    public function getLogs($service = '')
    {
        $cmd = 'docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' logs --no-color';
        if (!empty($service)) {
            $cmd .= ' ' . $service;
        }
        unset($output);
        exec($cmd, $output, $returnVar);
        $logs = '';
        foreach ($output as $logline) {
            $logs .= $logline . "\n";
        }
        return $logs;
    }

    /**
     *  Get the address or host name of the Docker server.
     *
     *  @return Address or host name of the Docker server.
     */
    public function getHost()
    {
        $docker = getenv('DOCKER_HOST');
        if (!preg_match('@^(tcp://)?([^:]+)@', $docker, $matches)) {
            $retval = '127.0.0.1';
        }
        else {
            $retval = $matches[2];
        }
        return $retval;
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
    
    /**
     * 
     * @param type $service
     * @return boolean
     */
    public function serviceRunning($service)
    {
        try {
            $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' exec ' . $service . ' echo \'alive\'');
        } catch (\Exception $ex) {
            return false;
        }
        return true;
    }

    /**
     *  Stop a service.
     *
     *  @param $service  Service to stop.
     */
    public function stop($service)
    {
        $this->exec('docker-compose -f ' . $this->composeFile . ' -p ' . $this->id . ' stop ' . $service);
    }

    /**
     *  Wait for available URL.
     */
    public function waitForAvailableUrl($url)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 2);
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        $limit = time() + 60;
        while ((time() < $limit) && !curl_exec($ch)) {
            sleep(1);
        }
        if (time() >= $limit) {
            throw new \Exception('URL ' . $url . ' did not respond within a 60 seconds time frame.');
        }
    }
}
