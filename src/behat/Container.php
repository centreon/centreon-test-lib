<?php

/**
 * Copyright 2016-2021 Centreon
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

use Centreon\Test\Behat\SpinTrait;

/**
 *  Run a container and manage it.
 */
class Container
{
    use SpinTrait;

    private $composeFile;
    private $id;
    private $host = null;
    private $containerIds = [];
    private $containerPorts = [];

    /** @var list<string> */
    private $profiles;

    /**
     * Container constructor.
     * @param string $composeFilePath Docker Compose file used to run the services.
     * @param string[] $profiles docker-compose profiles to activate
     * @throws \Exception
     */
    public function __construct(string $composeFilePath, array $profiles = [])
    {
        $this->composeFile = $composeFilePath;
        $this->profiles = $profiles;
        $this->id = uniqid() . rand(1, 1000000);

        $command =
            'docker-compose -f ' . $this->composeFile . ' '
            . implode(
                ' ',
                array_map(
                    fn (string $profile) => '--profile ' . escapeshellarg($profile),
                    $profiles
                )
            )
            . ' -p ' . $this->id . ' up -d --quiet-pull';

        $this->spin(
            function ($context) use ($command) {
                passthru($command, $returnVar);

                if ($returnVar !== 0) {
                    throw new \Exception(
                        'Cannot execute container control command: '
                        . $command. " \n "
                        . ' (code ' . $returnVar . ')'
                    );
                }

                return true;
            },
            'Cannot start docker containers',
            30
        );

        $this->initContainersInfos();
    }

    /**
     * Init container infos (ids, ports, services)
     */
    private function initContainersInfos()
    {
        exec('docker ps --no-trunc | grep ' . $this->id, $output, $returnVar);
        foreach ($output as $line) {
            if (preg_match('/^(\w+).+\s{3,}(.+\d+->\d+\/tcp)+\s+\w+(?:_|\-)([\w-]+)(?:_|\-)\d+$/', $line, $matches)) {
                $containerId = $matches[1];
                if (count($matches) === 4) {
                    $service = $matches[3];
                    $this->containerPorts[$service] = [];
                    $ports = explode(',', $matches[2]);
                    foreach ($ports as $port) {
                        // example : 0.0.0.0:49354->80/tcp, :::49353->80/tcp
                        // avoid to catch port which begin with ":::"
                        if (preg_match('/[^:]:(\d+)->(\d+)/', $port, $matchesPort)) {
                            $this->containerPorts[$service][$matchesPort[2]] = $matchesPort[1];
                        }
                    }
                } else {
                    $service = $matches[2];
                    $this->containerPorts[$service] = [];
                }
                $this->containerIds[$service] = $containerId;
            }
        }
    }

    /**
     *  Execute a command on local host.
     */
    private function exec($cmd)
    {
        exec($cmd . ' 2>&1', $output, $returnVar);
        if ($returnVar != 0) {
            throw new \Exception(
                'Cannot execute container control command: '
                . $cmd . " \n "
                . implode("\n", $output)
                . ' (code ' . $returnVar . ')'
            );
        }
    }

    /**
     *  Destructor.
     *
     *  Stop the container.
     */
    public function __destruct()
    {
        try {
            $this->spin(
                function ($context) {
                    $command = sprintf(
                        'docker-compose -f %s -p %s %s',
                            $this->composeFile,
                            $this->id,
                            implode(
                                ' ',
                                array_map(
                                    fn (string $profile) => '--profile ' . escapeshellarg($profile),
                                    $this->profiles
                                )
                            )
                    );
                    $context->exec($command . ' kill');
                    $context->exec($command . ' down -v');

                    return true;
                },
                'Cannot stop docker containers',
                30
            );
        } catch (\Throwable $e) {
            echo 'Exception: ' . $e->getMessage();
        }
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
        if (!isset($this->containerIds[$service])) {
            throw new \Exception('Cannot retrieve container ID of service ' . $service);
        }

        return $longId
            ? $this->containerIds[$service]
            : substr($this->containerIds[$service], 0, 12);
    }

    /**
     * Get the container ip address of a service.
     *
     * @param string $service Service name.
     * @return string
     * @throws \Exception
     */
    public function getContainerIpAddress(string $service): string
    {
        $containerIpAddress = shell_exec(
            "docker inspect -f '{{range.NetworkSettings.Networks}}{{.IPAddress}}{{end}}' "
            . $this->getContainerId($service, false)
        );

        if (!is_string($containerIpAddress)) {
            throw new \Exception('Cannot retrieve container ip address of service ' . $service);
        }

        return $containerIpAddress;
    }

    /**
     * Get the container(s) logs.
     *
     * @return string The logs as a string.
     */
    public function getLogs()
    {
        $command = sprintf(
            'docker-compose -f %s -p %s %s logs -t --no-color',
                escapeshellarg($this->composeFile),
                escapeshellarg($this->id),
                implode(
                    ' ',
                    array_map(
                        fn (string $profile) => '--profile ' . escapeshellarg($profile),
                        $this->profiles
                    )
                )
        );

        unset($output);
        exec($command, $output, $returnVar);

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
        if ($this->host === null) {
            $docker = getenv('DOCKER_HOST');
            if (!preg_match('@^(tcp://)?([^:]+)@', $docker, $matches)) {
                $retval = '127.0.0.1';
            } elseif (preg_match('@^(unix://)?([^:]+)@', $docker, $matches)) {
                $retval = '127.0.0.1';
            } else {
                $retval = $matches[2];
            }
            $this->host = $retval;
        }

        return $this->host;
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
        if (!isset($this->containerPorts[$service][$containerPort])) {
            throw new \Exception('Cannot get corresponding port of ' . $containerPort . ' on service ' . $service);
        }

        return $this->containerPorts[$service][$containerPort];
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
