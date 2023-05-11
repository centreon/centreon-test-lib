#!/usr/bin/env php
<?php

/*
 * Copyright 2005 - 2023 Centreon (https://www.centreon.com/)
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 * https://www.apache.org/licenses/LICENSE-2.0
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

declare(strict_types=1);

require __DIR__ . '/../../vendor/autoload.php';

use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Service\CreateCoreArchCommandService;
use Centreon\Command\Service\CreateCoreCommandArchCommandService;
use Centreon\Command\Service\CreateCoreQueryArchCommandService;
use Symfony\Component\Console\Application;

$application = new Application();
if (! function_exists('yaml_parse_file')) {
    echo "Error: The php 'yaml' extension is missing.\n";
    exit(1);
}
$config = yaml_parse_file(__DIR__ . '/config.yaml');
if (! array_key_exists('centreon', $config) || empty($config['centreon'])) {
    throw new \Exception('empty path, please provide a value into config.yaml');
}

$centreonSrcPath = __DIR__ . "/" . ltrim($config["centreon"], "/") . "/src";
$commandService = new CreateCoreArchCommandService($centreonSrcPath);
$queryArchCommandService = new CreateCoreQueryArchCommandService($commandService);
$commandArchCommandService = new CreateCoreCommandArchCommandService($commandService);
$singleCommand = new CreateCoreArchCommand($commandService, $queryArchCommandService, $commandArchCommandService);
$application->add($singleCommand);
$application->setDefaultCommand($singleCommand->getName(), true);

$application->run();
