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
use Centreon\Command\Model\DtoTemplate\ResponseDtoTemplate;
use Centreon\Command\Model\ExceptionTemplate\ExceptionTemplate;
use Centreon\Command\Model\RouteTemplate\RouteTemplate;
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

    private ResponseDtoTemplate $responseDtoTemplate;

    /** @var PresenterInterfaceTemplate */
    private PresenterInterfaceTemplate $commandPresenterInterfaceTemplate;

    /** @var ExceptionTemplate */
    private ExceptionTemplate $commandExceptionTemplate;

    /** @var RouteTemplate @var RouteTemplate */
    private RouteTemplate $commandRouteTemplate;

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
        if (! file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
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
        } elseif(file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
            $this->requestDtoTemplate = new RequestDtoTemplate(
                $filePath,
                $namespace,
                $className,
                false
            );
        } else {

            $output->writeln(
                '<info>Using Existing Request or not allowed to write for : ' . $useCaseType . 'method'
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
    public function createResponseDtoTemplateIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCaseType
    ): void {
        $useCaseName = $useCaseType . $modelName;
        $className = $useCaseName . 'Response';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR .  'Application' . DIRECTORY_SEPARATOR .'UseCase' . DIRECTORY_SEPARATOR
            . $useCaseName . DIRECTORY_SEPARATOR . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\UseCase\\' . $useCaseName;
        if (! file_exists($filePath)) {
            $this->responseDtoTemplate = new ResponseDtoTemplate(
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
        $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
            $filePath,
            $namespace,
            $modelName,
            $useCaseType
        );
        $this->commandPresenterTemplate = new PresenterTemplate(
            $filePath,
            $namespace,
            $modelName,
            $useCaseType,
            $this->commandPresenterInterfaceTemplate,
            true
        );
        if (! file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType
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
                '<info>Creating Presenter : ' . $this->commandPresenterInterfaceTemplate->namespace . '\\'
                . $this->commandPresenterInterfaceTemplate->name . '</info>'
            );
        } elseif (file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType
            );
            $output->writeln(
                '<info>Using Existing PresenterInterface : ' . $this->commandPresenterInterfaceTemplate->namespace . '\\'
                . $this->commandPresenterTemplate->name . '</info>'
            );
        } else {
            $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType
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
    public function createExceptionIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCase
    ): void {
        $className = $modelName . 'Exception';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Exception' . DIRECTORY_SEPARATOR
            . $className . '.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\Exception';
        if (! file_exists($filePath)) {
            $this->commandExceptionTemplate = new ExceptionTemplate(
                $filePath,
                $namespace,
                $modelName,
            );
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->commandExceptionTemplate->filePath,
                $this->commandExceptionTemplate->generateModelContent($useCase)
            );
            $output->writeln(
                '<info>Creating Exception Template : ' . $this->commandExceptionTemplate->namespace . '\\'
                . $className . '</info>'
            );
        } else {
            require ($filePath);
            $classPath = $namespace . '\\' . $modelName . 'Exception';
            $reflection = new \ReflectionClass(new $classPath);
            $isMethodeAlreadyExist = $reflection->hasMethod(ExceptionTemplate::getMethodeName($useCase));
            $this->commandExceptionTemplate = new ExceptionTemplate(
                $filePath,
                $namespace,
                $modelName,
            );
            if($isMethodeAlreadyExist === true) {
                $output->writeln(
                    '<info>Using Existing Exception Template : ' . $this->commandExceptionTemplate->namespace
                    . '\\' . $className . '</info>'
                );
                $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>'."\n");
                $output->writeln(
                    '<info>Method ' . $this->commandExceptionTemplate->getName() . 'Exception::' . ExceptionTemplate::getMethodeName($useCase) . '() already exist </info>'."\n"
                );
                return ;
            }
            $classContent = file_get_contents($filePath);
            $index = strrpos($classContent,"}",0);
            $textBeforeIndex = substr($classContent, 0, $index);
            $texte = $this->commandExceptionTemplate->verifErrorWhile($useCase);
            $newText = $textBeforeIndex . "\n    "  . $texte . "\n}";
            file_put_contents(
                $this->commandExceptionTemplate->filePath,
                $newText
            );
            $output->writeln(
                '<info>Using Existing Exception Template : ' . $this->commandExceptionTemplate->namespace
                . '\\' . $this->commandExceptionTemplate->name . '</info>'
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
    public function createRouteIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $useCase
    ): void {
        $className = $modelName . 'Route';
        $filePath = $this->commandService->getSrcPath() . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR
            . $modelName . DIRECTORY_SEPARATOR . 'Infrastructure' . DIRECTORY_SEPARATOR . 'API' . DIRECTORY_SEPARATOR . $useCase . $modelName . DIRECTORY_SEPARATOR
            . $className . '.yaml';
        $namespace = 'Core\\' . $modelName . '\\Infrastructure\\API\\' . $useCase . $modelName . '\\';
        if (! file_exists($filePath)) {
            $this->commandRouteTemplate = new RouteTemplate(
                $filePath,
                $namespace,
                $modelName,
            );
            preg_match('/^(.+).' . $className . '\.yaml$/', $filePath, $matches);
            $dirLocation = $matches[1];
            // Create dir if not exists,
            if (! is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->commandRouteTemplate->filePath,
                $this->commandRouteTemplate->generateModelContent($useCase)
            );
            $output->writeln(
                '<info>Creating Route Template : ' . $this->commandRouteTemplate->namespace . '\\'
                . $className . '</info>'
            );
        } else {
            require ($filePath);
            $this->commandRouteTemplate = new RouteTemplate(
                $filePath,
                $namespace,
                $modelName,
            );
            $output->writeln(
                '<info>Using Existing Route Template : ' . $this->commandRouteTemplate->namespace
                . '\\' . $this->commandRouteTemplate->name . '</info>'
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
        $this->commandPresenterInterfaceTemplate = new PresenterInterfaceTemplate(
            $filePath,
            $namespace,
            $useCaseName . 'Presenter',
            $useCaseType
        );
        if (! file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType,
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
        } elseif (file_exists($filePath) && ($useCaseType === "Add" || $useCaseType === "Update")) {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType,
                $this->commandPresenterInterfaceTemplate,
                true
            );
            $output->writeln(
                '<info>Using Existing Presenter : ' . $this->commandPresenterTemplate->namespace . '\\'
                    . $this->commandPresenterTemplate->name . '</info>'
            );
        }else {
            $this->commandPresenterTemplate = new PresenterTemplate(
                $filePath,
                $namespace,
                $modelName,
                $useCaseType,
                $this->commandPresenterInterfaceTemplate,
                true
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
        $this->requestDtoTemplate = new RequestDtoTemplate(
            $filePath,
            $namespace,
            $useCaseName . 'Request',
            false
        );
        if (! file_exists($filePath)) {
            $this->commandUseCaseTemplate = new CommandUseCaseTemplate(
                $filePath,
                $namespace,
                $useCaseType,
                $modelName,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                false,
                $useCaseName
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
                $useCaseType,
                $modelName,
                $this->commandPresenterInterfaceTemplate,
                $this->requestDtoTemplate,
                $this->commandService->getRepositoryInterfaceTemplate(),
                true,
                $useCaseName
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
                $modelName,
                $this->commandUseCaseTemplate,
                $this->commandPresenterTemplate,
                $this->requestDtoTemplate,
                $useCaseType,
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
                $modelName,
                $this->commandUseCaseTemplate,
                $this->commandPresenterTemplate,
                $this->requestDtoTemplate,
                $useCaseType
            );
            $output->writeln(
                '<info>Using Existing Controller : ' . $this->commandControllerTemplate->namespace . '\\'
                    . $this->commandControllerTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln('');

    }

}
