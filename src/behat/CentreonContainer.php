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
 *  Run a Centreon container and manage it.
 */
class CentreonContainer
{
    private $container_id;

    /**
     * Constructor.
     *
     * @param $image Docker image name used to run the container.
     */
    public function __construct($image)
    {
        $this->container_id = shell_exec('docker run -t -d -p 80 -p 7555 "' . $image . '" | tr -d "\n"');
        if (empty($this->container_id))
            throw new \Exception('Could not run Centreon Web container');
        $ch = curl_init('http://localhost:' . $this->getPort());
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 500);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
        while (!curl_exec($ch)) {
            sleep(1);
        }
        curl_close($ch);
    }

    /**
     *  Destructor.
     *
     *  Stop the container.
     */
    public function __destruct()
    {
        shell_exec('docker stop "' . $this->container_id . '"');
        shell_exec('docker rm "' . $this->container_id . '"');
    }

    /**
     *  Execute a command within a container.
     *
     *  @param string $cmd Command to execute.
     */
    public function execute($cmd)
    {
        exec('docker exec ' . $this->container_id . ' ' . $cmd, $output, $returnVar);

        return array(
            'output' => $output,
            'exit_code' => $returnVar
        );
    }

    /**
     *  Copy file or directory from localhost to container.
     *
     *  @param string $src Source file or directory.
     *  @param string $dst Destination directory
     */
    public function copyFromHost($src, $dst)
    {
        exec('docker exec -d -t ' . $this->container_id . ' sh -c "cd `dirname ' . $dst . '`; nc -l 7555 | tar x"', $output, $return);
        sleep(1);
        $ipAddress = trim(shell_exec("docker inspect -f '{{ .NetworkSettings.IPAddress }}' " . $this->container_id));
        shell_exec('cd `dirname ' . $src . '`; tar c `basename ' . $src . '` | nc ' . $ipAddress . ' 7555');
    }

    /**
     *  Get the port to which users can connect and access Centreon Web.
     */
    public function getPort()
    {
        return shell_exec('docker port "' . $this->container_id . '" 80 | cut -d : -f 2 | tr -d "\n"');
    }
}
