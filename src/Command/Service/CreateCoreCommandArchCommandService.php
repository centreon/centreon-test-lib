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

namespace Centreon\Command\Service;

use Centreon\Command\Model\ControllerTemplate\CommandControllerTemplate;
use Centreon\Command\Model\DtoTemplate\RequestDtoTemplate;
use Centreon\Command\Model\FactoryTemplate\FactoryTemplate;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Model\PresenterTemplate\{PresenterInterfaceTemplate, PresenterTemplate};
use Centreon\Command\Model\RepositoryTemplate\RepositoryTemplate;
use Centreon\Command\Model\UseCaseTemplate\CommandUseCaseTemplate;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCoreCommandArchCommandService
{
    /** @var RepositoryTemplate */
    private RepositoryTemplate $writeRepositoryTemplate;

    /** @var RequestDtoTemplate */
    private RequestDtoTemplate $requestDtoTemplate;

    /** @var PresenterInterfaceTemplate */
    private PresenterInterfaceTemplate $commandPresenterInterfaceTemplate;

    /** @var CommandUseCaseTemplate */
    private CommandUseCaseTemplate $commandUseCaseTemplate;

    /** @var PresenterTemplate */
    private PresenterTemplate $commandPresenterTemplate;

    /** @var CommandControllerTemplate */
    private CommandControllerTemplate $commandControllerTemplate;

    /** @var FactoryTemplate */
    private FactoryTemplate $factoryTemplate;

    /**
     * @param CreateCoreArchCommandService $commandService
     */
    public function __construct(private CreateCoreArchCommandService $commandService)
    {
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     */
    public function createWriteRepositoryTemplateIfNotExist(
        OutputInterface $output,
        string $modelName
    ): void {
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR
            . 'Repository' . DIRECTORY_SEPARATOR . 'DbWrite'
            . $modelName . 'Repository.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\Repository';
        if (! file_exists($filePath)) {
            $this->writeRepositoryTemplate = new RepositoryTemplate(
                $filePath,
                $namespace,
                'DbWrite' . $modelName . 'Repository',
                $this->commandService->getRepositoryInterfaceTemplate(),
                false
            );
            preg_match('/^(.+).DbWrite' . $modelName . 'Repository\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->writeRepositoryTemplate->filePath,
                $this->writeRepositoryTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Repository : ' . $this->writeRepositoryTemplate->namespace . '\\'
                    . $this->writeRepositoryTemplate->name . '</info>'
            );
        } else {
            $this->writeRepositoryTemplate = new RepositoryTemplate(
                $filePath,
                $namespace,
                'DbWrite' . $modelName . 'Repository',
                $this->commandService->getRepositoryInterfaceTemplate(),
                true
            );
            $output->writeln(
                '<info>Using Existing Repository : ' . $this->writeRepositoryTemplate->namespace . '\\'
                    . $this->writeRepositoryTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $useCaseType
     */
    public function createRequestDtoTemplateIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'Request';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR .  'Application' . DIRECTORY_SEPARATOR .'UseCase' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->requestDtoTemplate = new RequestDtoTemplate(
                $filePath,
                $namespace,
                $className,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->requestDtoTemplate->filePath,
                $this->requestDtoTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Request : ' . $this->requestDtoTemplate->namespace . '\\'
                    . $this->requestDtoTemplate->name . '</info>'
            );
        } else {
            $this->requestDtoTemplate = new RequestDtoTemplate(
                $filePath,
                $namespace,
                $className,
                true
            );
            $output->writeln(
                '<info>Using Existing Request : ' . $this->requestDtoTemplate->namespace . '\\'
                    . $this->requestDtoTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $useCaseType
     */
    public function createPresenterInterfaceIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'PresenterInterface';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $className,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->commandPresenterInterfaceTemplate->filePath,
                $this->commandPresenterInterfaceTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Presenter Interface : ' . $this->commandPresenterInterfaceTemplate->namespace . '\\'
                    . $this->commandPresenterInterfaceTemplate->name . '</info>'
            );
        } else {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $className,
                true
            );
            $output->writeln(
                '<info>Using Existing Presenter Interface : ' . $this->commandPresenterInterfaceTemplate->namespace
                    . '\\' . $this->commandPresenterInterfaceTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $useCaseType
     */
    public function createPresenterIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'Presenter';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR . 'API' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\API\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $className,
                $this->commandPresenterInterfaceTemplate,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->commandPresenterTemplate->filePath,
                $this->commandPresenterTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Presenter : ' . $this->commandPresenterTemplate->namespace . '\\'
                    . $this->commandPresenterTemplate->name . '</info>'
            );
        } else {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $className,
                $this->commandPresenterInterfaceTemplate,
                true
            );
            $output->writeln(
                '<info>Using Existing Presenter : ' . $this->commandPresenterTemplate->namespace . '\\'
                    . $this->commandPresenterTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $useCaseType
     */
    public function createUseCaseIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'UseCase' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $useCaseName . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->commandUseCaseTemplate = new CommandUseCaseTemplate(
                $filePath,
                $namespace,
                $useCaseName,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                false
            );
            preg_match('/^(.+).' . $useCaseName . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->commandUseCaseTemplate->filePath,
                $this->commandUseCaseTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Use Case : ' . $this->commandUseCaseTemplate->namespace . '\\'
                    . $this->commandUseCaseTemplate->name . '</info>'
            );
        } else {
            $this->commandUseCaseTemplate = new CommandUseCaseTemplate(
                $filePath,
                $namespace,
                $useCaseName,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                true
            );
            $output->writeln(
                '<info>Using Existing Use Case : ' . $this->commandUseCaseTemplate->namespace . '\\'
                    . $this->commandUseCaseTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');

        $this->commandService->createUnitTestFileIfNotExists($output, $this->commandUseCaseTemplate);
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $useCaseType
     */
    public function createControllerIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'Controller';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR . 'API' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\API\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->commandControllerTemplate = new CommandControllerTemplate(
                $filePath,
                $namespace,
                $className,
                $this->commandUseCaseTemplate,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->commandControllerTemplate->filePath,
                $this->commandControllerTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Controller : ' . $this->commandControllerTemplate->namespace . '\\'
                    . $this->commandControllerTemplate->name . '</info>'
            );
        } else {
            $this->commandControllerTemplate = new CommandControllerTemplate(
                $filePath,
                $namespace,
                $className,
                $this->commandUseCaseTemplate,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                true
            );
            $output->writeln(
                '<info>Using Existing Controller : ' . $this->commandControllerTemplate->namespace . '\\'
                    . $this->commandControllerTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');

        $this->commandService->createUnitTestFileIfNotExists($output, $this->commandControllerTemplate);
    }

    /**
     * @param OutputInterface $output
     * @param ModelTemplate $modelTemplate
     */
    public function createFactoryIfNotExist(
        OutputInterface $output,
        ModelTemplate $modelTemplate,
    ): void {
        $className = 'New' . $modelTemplate->name . 'Factory';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelTemplate->name . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Model'
            . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelTemplate->name . '\\Domain\\Model';
        if (! file_exists($filePath)) {
            $this->factoryTemplate = new FactoryTemplate(
                $filePath,
                $namespace,
                $className,
                $modelTemplate
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->factoryTemplate->filePath,
                $this->factoryTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Factory : ' . $this->factoryTemplate->namespace . '\\'
                    . $this->factoryTemplate->name . '</info>'
            );
        } else {
            $this->factoryTemplate = new FactoryTemplate(
                $filePath,
                $namespace,
                $className,
                $modelTemplate,
                true
            );
            $output->writeln(
                '<info>Using Existing Factory : ' . $this->factoryTemplate->namespace . '\\'
                    . $this->factoryTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');

        $this->commandService->createUnitTestFileIfNotExists($output, $this->factoryTemplate);
    }
}
