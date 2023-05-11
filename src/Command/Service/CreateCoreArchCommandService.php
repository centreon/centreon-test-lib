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

use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ChoiceQuestion;
use Centreon\Command\Model\FileTemplate;
use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Model\UnitTestTemplate\UnitTestTemplate;
use Centreon\Command\Model\RepositoryTemplate\RepositoryInterfaceTemplate;
use Symfony\Component\Console\Question\ConfirmationQuestion;

class CreateCoreArchCommandService
{
    private const REPO_PREFIX = 'centreon/centreon';

    protected RepositoryInterfaceTemplate $repositoryInterfaceTemplate;

    /**
     * @param string $srcPath
     */
    public function __construct(protected string $srcPath)
    {
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param mixed $questionHelper
     * @return string
     */
    public function askForUseCaseType(InputInterface $input, OutputInterface $output, $questionHelper): string
    {
        $questionUseCaseType = new ChoiceQuestion(
            'What kind of use case would you like to create ? ',
            CreateCoreArchCommand::COMMAND_ACTION
        );

        $questionUseCaseType->setErrorMessage('Type %s is invalid.');
        $useCaseType = $questionHelper->ask($input, $output, $questionUseCaseType);
        $output->writeln('<info>You have selected: [' . $useCaseType . '] Use Case Type.</info>');
        $output->writeln("");
        return $useCaseType;
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     * @param mixed $questionHelper
     * @param string $useCaseType
     * @return ModelTemplate
     */
    public function askForModel(
        InputInterface $input,
        OutputInterface $output,
        $questionHelper,
        string $useCaseType
    ): ModelTemplate {
        $questionModelName = new Question('For which model is this use case intended ? ');
        $modelName = $questionHelper->ask($input, $output, $questionModelName);
        // if useCase type is 'Create' model name should start with 'New'
        if ($useCaseType === CreateCoreArchCommand::COMMAND_CREATE) {
            $modelName = 'New' . $modelName;
        }
        $output->writeln('<info>You have selected: [' . $modelName . '] Model.</info>');
        $output->writeln("");
        //Search for already existing models.
        $foundModels = $this->searchExistingModel($modelName);
        if (!empty($foundModels)) {
            return new ModelTemplate(
                $foundModels['path'],
                $foundModels['namespace'],
                $modelName,
                true
            );
        }

        // If the model doesn't exist or if the user want to create a new one. Asks to valid the name to avoid typos.
        $confirmationQuestion = new ConfirmationQuestion("You're going to create a model : " . $modelName . " [Y/n]");
        $confirmation = $questionHelper->ask($input, $output, $confirmationQuestion);
        if ($confirmation === false) {
            return $this->askForModel($input, $output, $questionHelper);
        }
        $newNamespace = 'Core\\' . $modelName . '\\Domain\\Model';
        $filePath = $this->srcPath . DIRECTORY_SEPARATOR . preg_replace("/\\\\/", DIRECTORY_SEPARATOR, $newNamespace) .
            DIRECTORY_SEPARATOR . $modelName . '.php';

        return new ModelTemplate($filePath, $newNamespace, $modelName);
    }

    /**
     * Look for existing model with the same name.
     *
     * @param string $modelName
     * @return array<string,string>
     */
    private function searchExistingModel(string $modelName): array
    {
        //Search for all model with the same name.
        $modelsInfos = iterator_to_array(
            new \GlobIterator(
                $this->srcPath . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . $modelName
                    . DIRECTORY_SEPARATOR . 'Domain' . DIRECTORY_SEPARATOR . 'Model' . DIRECTORY_SEPARATOR
                    . $modelName . '.php'
            )
        );
        $modelInfo = [];
        if (! empty($modelsInfos)) {
            $foundModel = array_shift($modelsInfos);

            // Set file informations
            $modelInfo['path'] = $foundModel->getRealPath();
            $fileContent = file($foundModel->getRealPath());

            // extract namespace
            foreach ($fileContent as $fileLine) {
                if (strpos($fileLine, 'namespace') !== false) {
                    $parts = explode(' ', $fileLine);
                    $namespace = rtrim(trim($parts[1]), ';\n');
                    $modelInfo['namespace'] = $namespace;
                    break;
                }
            }
        }

        return $modelInfo;
    }

    /**
     * @param ModelTemplate $model
     */
    public function createModel(ModelTemplate $model): void
    {
        preg_match('/^(.+).' . $model->name . '\.php$/', $model->filePath, $matches);
        $dirLocation = $matches[1];

        //Create dir if not exists,
        if (!is_dir($dirLocation)) {
            mkdir($dirLocation, 0777, true);
        }

        //Create and fill the file.
        file_put_contents($model->filePath, $model->generateModelContent());
    }

    /**
     * @param OutputInterface $output
     * @param string $modelName
     * @param string $repositoryType
     */
    public function createRepositoryInterfaceTemplateIfNotExist(
        OutputInterface $output,
        string $modelName,
        string $repositoryType
    ): void {
        $filePath = $this->srcPath . DIRECTORY_SEPARATOR . 'Core' . DIRECTORY_SEPARATOR . $modelName
            . DIRECTORY_SEPARATOR . 'Application' . DIRECTORY_SEPARATOR . 'Repository' . DIRECTORY_SEPARATOR
            . $repositoryType . $modelName . 'RepositoryInterface.php';
        $namespace = 'Core\\' . $modelName . '\\Application\\Repository';
        if (!file_exists($filePath)) {
            $this->repositoryInterfaceTemplate = new RepositoryInterfaceTemplate(
                $filePath,
                $namespace,
                $repositoryType . $modelName . 'RepositoryInterface',
                false
            );
            preg_match('/^(.+).'. $repositoryType . $modelName . 'RepositoryInterface\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }

            file_put_contents(
                $this->repositoryInterfaceTemplate->filePath,
                $this->repositoryInterfaceTemplate->generateModelContent()
            );
            $output->writeln(
                '<info>Creating Repository Interface : ' . $this->repositoryInterfaceTemplate->namespace . '\\'
                    . $this->repositoryInterfaceTemplate->name . '</info>'
            );
        } else {
            $this->repositoryInterfaceTemplate = new RepositoryInterfaceTemplate(
                $filePath,
                $namespace,
                $repositoryType . $modelName . 'RepositoryInterface',
                true
            );
            $output->writeln(
                '<info>Using Existing Repository Interface : ' . $this->repositoryInterfaceTemplate->namespace . '\\'
                    . $this->repositoryInterfaceTemplate->name . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");
    }

    /**
     * @param OutputInterface $output
     * @param FileTemplate $fileTemplate
     * @return void
     */
    protected function createUnitTestFileIfNotExists(OutputInterface $output, FileTemplate $fileTemplate): void
    {
        $className = $fileTemplate->name . 'Test';
        $filePath = $this->srcPath . '/../tests/php' . DIRECTORY_SEPARATOR
            . preg_replace("/\\\\/", DIRECTORY_SEPARATOR, $fileTemplate->namespace)
            . DIRECTORY_SEPARATOR . $className . '.php';
        if (! file_exists($filePath)) {
            preg_match('/^(.+).' . $className . '\.php$/', $filePath, $matches);
            $dirLocation = $matches[1];
            //Create dir if not exists,
            if (!is_dir($dirLocation)) {
                mkdir($dirLocation, 0777, true);
            }
            file_put_contents(
                $filePath,
                (new UnitTestTemplate())->generateContentForUnitTest('Test\\' . $fileTemplate->namespace)
            );
            $output->writeln(
                '<info>Creating Test file : ' . 'Test\\' . $fileTemplate->namespace . '\\'
                    . $className . '</info>'
            );
        } else {
            $output->writeln(
                '<info>Using Test file : ' . 'Test\\' . $fileTemplate->namespace . '\\'
                . $className . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->getRelativeFilePath($filePath) . '</comment>');
        $output->writeln("");
    }

    /**
     * @param string $filePath
     * @return string
     */
    public function getRelativeFilePath(string $filePath): string
    {
        return substr($filePath, strpos($filePath, self::REPO_PREFIX));
    }
}
