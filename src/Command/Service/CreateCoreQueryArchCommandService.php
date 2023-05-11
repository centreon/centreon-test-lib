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

use Centreon\Command\Model\ControllerTemplate\QueryControllerTemplate;
use Centreon\Command\Model\DtoTemplate\ResponseDtoTemplate;
use Centreon\Command\Model\FactoryTemplate\FactoryTemplate;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterTemplate;
use Centreon\Command\Model\PresenterTemplate\{PresenterInterfaceTemplate, RepositoryTemplate};
use Centreon\Command\Model\UseCaseTemplate\QueryUseCaseTemplate;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCoreQueryArchCommandService
{
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
    public function createReadRepositoryTemplateIfNotExist(
        OutputInterface $output,
        string $modelName
    ): void {
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelName . '/Infrastructure/Repository/'
            . 'DbRead' . $modelName . 'Repository.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\Repository';
        if (!file_exists($filePath)) {
            $this->writeRepositoryTemplate = new RepositoryTemplate(
                $filePath,
                $namespace,
                'DbRead' . $modelName . 'Repository',
                $this->commandService->getRepositoryInterfaceTemplate(),
                false
            );
            preg_match('/^(.+).DbRead' . $modelName . 'Repository\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
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
                'DbRead' . $modelName . 'Repository',
                $this->commandService->getRepositoryInterfaceTemplate(),
                true
            );
            $output->writeln(
                '<info>Using Existing Repository : ' . $this->writeRepositoryTemplate->namespace . '\\'
                    . $this->writeRepositoryTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @return void
     */
    public function createResponseDtoTemplateIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'Response';
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelName . '/Application/UseCase/' . $useCaseName
            . '\\' . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (!file_exists($filePath)) {
            $this->responseDtoTemplate = new ResponseDtoTemplate(
                $filePath,
                $namespace,
                $className,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->responseDtoTemplate->filePath,
                $this->responseDtoTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Response : ' . $this->responseDtoTemplate->namespace . '\\'
                    . $this->responseDtoTemplate->name . '</info>'
            );
        } else {
            $this->responseDtoTemplate = new ResponseDtoTemplate(
                $filePath,
                $namespace,
                $className,
                true
            );
            $output->writeln(
                '<info>Using Existing Response : ' . $this->responseDtoTemplate->namespace . '\\'
                    . $this->responseDtoTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");
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
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelName . '/Application/UseCase/' . $useCaseName
            . '\\' . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (!file_exists($filePath)) {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $className,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
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
        $output->writeln("");
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
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelName . '/Infrastructure/API/' . $useCaseName
            . '\\' . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\API\\' . $useCaseName;
        if (!file_exists($filePath)) {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $className,
                $this->commandPresenterInterfaceTemplate,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
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
        $output->writeln("");
    }

    /**
     * @param OutputInterface $output
     * @param ModelTemplate $modelTemplate
     * @param string $useCaseType
     */
    public function createUseCaseIfNotExist(
        OutputInterface $output,
        ModelTemplate $modelTemplate,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelTemplate->name;
        $filePath = $this->commandService->getSrcPath() . '/Core/'  . $modelTemplate->name . '/Application/UseCase/'
            . $useCaseName . '\\' . $useCaseName . '.php';
        $namespace = 'Core\\' . $modelTemplate->name . '\\Application\\UseCase\\' . $useCaseName;
        if (!file_exists($filePath)) {
            $this->queryUseCaseTemplate = new QueryUseCaseTemplate(
                $filePath,
                $namespace,
                $useCaseName,
                $this->commandPresenterInterfaceTemplate,
                $this->responseDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                false,
                $modelTemplate
            );
            preg_match('/^(.+).' . $useCaseName . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->queryUseCaseTemplate->filePath,
                $this->queryUseCaseTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Use Case : ' . $this->queryUseCaseTemplate->namespace . '\\'
                    . $this->queryUseCaseTemplate->name . '</info>'
            );
        } else {
            $this->queryUseCaseTemplate = new QueryUseCaseTemplate(
                $filePath,
                $namespace,
                $useCaseName,
                $this->commandPresenterInterfaceTemplate,
                $this->responseDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                true,
                $modelTemplate
            );
            $output->writeln(
                '<info>Using Existing Use Case : ' . $this->queryUseCaseTemplate->namespace . '\\'
                    . $this->queryUseCaseTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");

        $this->commandService->createUnitTestFileIfNotExists($output, $this->queryUseCaseTemplate);
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
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelName . '/Infrastructure/Api/' . $useCaseName
            . '\\' . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\Api\\' . $useCaseName;
        if (!file_exists($filePath)) {
            $this->queryControllerTemplate = new QueryControllerTemplate(
                $filePath,
                $namespace,
                $className,
                $this->queryUseCaseTemplate,
                $this->commandPresenterInterfaceTemplate,
                false
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $this->queryControllerTemplate->filePath,
                $this->queryControllerTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Controller : ' . $this->queryControllerTemplate->namespace . '\\'
                    . $this->queryControllerTemplate->name . '</info>'
            );
        } else {
            $this->queryControllerTemplate = new QueryControllerTemplate(
                $filePath,
                $namespace,
                $className,
                $this->queryUseCaseTemplate,
                $this->commandPresenterInterfaceTemplate,
                true
            );
            $output->writeln(
                '<info>Using Existing Controller : ' . $this->queryControllerTemplate->namespace . '\\'
                    . $this->queryControllerTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");

        $this->commandService->createUnitTestFileIfNotExists($output, $this->queryControllerTemplate);
    }

    /**
     * @param OutputInterface $output
     * @param ModelTemplate $modelTemplate
     */
    public function createFactoryIfNotExist(
        OutputInterface $output,
        ModelTemplate $modelTemplate,
    ): void {
        $className = 'Db' . $modelTemplate->name . 'Factory';
        $filePath = $this->commandService->getSrcPath() . '/Core/' . $modelTemplate->name
            . '/Infrastructure/Repository/' . $className. '.php';
        $namespace = 'Core\\' . $modelTemplate->name . '\\Infrastructure\\Repository';
        if (!file_exists($filePath)) {
            $this->factoryTemplate = new FactoryTemplate(
                $filePath,
                $namespace,
                $className,
                $modelTemplate
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
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
        $output->writeln("");

        $this->commandService->createUnitTestFileIfNotExists($output, $this->factoryTemplate);
    }
}
