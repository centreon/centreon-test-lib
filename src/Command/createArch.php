#!/usr/bin/env php
<?php

require __DIR__ . '/../../vendor/autoload.php';

use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Service\CreateCoreArchCommandService;
use Centreon\Command\Service\CreateCoreCommandArchCommandService;
use Centreon\Command\Service\CreateCoreQueryArchCommandService;
use Symfony\Component\Console\Application;

$application = new Application();
$config = yaml_parse_file(__DIR__ . '/config.yaml');
if (! array_key_exists('centreon', $config) || empty($config['centreon'])) {
    throw new \Exception('empty path, please provide a value into config.yaml');
}

$centreonSrcPath = __DIR__ . "/" . ltrim($config["centreon"], "/") . "/src";
$commandService = new CreateCoreArchCommandService($centreonSrcPath);
$queryArchCommandService = new CreateCoreQueryArchCommandService($centreonSrcPath);
$commandArchCommandService = new CreateCoreCommandArchCommandService($centreonSrcPath);
$application->add(new CreateCoreArchCommand($commandService, $queryArchCommandService, $commandArchCommandService));

$application->run();