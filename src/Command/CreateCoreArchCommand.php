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

namespace Centreon\Command;

use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Service\{
    CreateCoreArchCommandService,
    CreateCoreCommandArchCommandService,
    CreateCoreQueryArchCommandService
};
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Helper\QuestionHelper;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class CreateCoreArchCommand extends Command
{
    public const COMMAND_NAME = 'centreon:create-core-arch';
    public const COMMAND_CREATE = 'Create';
    public const COMMAND_UPDATE = 'Update';
    public const COMMAND_DELETE = 'Delete';
    public const COMMAND_FIND = 'Find';
    public const COMMAND_ACTION = [
        self::COMMAND_CREATE,
        self::COMMAND_UPDATE,
        self::COMMAND_DELETE,
        self::COMMAND_FIND,
    ];
    public const READ_REPOSITORY_TYPE = 'Read';
    public const WRITE_REPOSITORY_TYPE = 'Write';
    public const COMMAND_USECASES = [self::COMMAND_CREATE, self::COMMAND_UPDATE, self::COMMAND_DELETE];
    public const QUERY_USECASES = [self::COMMAND_FIND];

    private string $useCaseType;

    private ModelTemplate $modelTemplate;

    /**
     * @param CreateCoreArchCommandService $commandService
     * @param CreateCoreQueryArchCommandService $queryArchCommandService
     * @param CreateCoreCommandArchCommandService $commandArchCommandService
     */
    public function __construct(
        private CreateCoreArchCommandService $commandService,
        private CreateCoreQueryArchCommandService $queryArchCommandService,
        private CreateCoreCommandArchCommandService $commandArchCommandService
    ) {
        parent::__construct();
    }

    /**
     * @inheritDoc
     */
    public function configure(): void
    {
        $this
            ->setName(self::COMMAND_NAME)
            ->setDescription('Create architecture for a useCase')
            ->setHelp('This command allows you to create classes for a useCase');
    }

    /**
     * @inheritDoc
     */
    public function interact(InputInterface $input, OutputInterface $output): void
    {
        $output->writeln('');
        $output->writeln('You are going to create a use case architecture.');
        $output->writeln("Let's answer few questions first !");
        $output->writeln('');

        /** @var QuestionHelper $questionHelper */
        $questionHelper = $this->getHelper('question');

        $this->useCaseType = $this->commandService->askForUseCaseType($input, $output, $questionHelper);
        $this->modelTemplate = $this->commandService->askForModel($input, $output, $questionHelper, $this->useCaseType);
    }

    /**
     * @inheritDoc
     */
    public function execute(InputInterface $input, OutputInterface $output): int
    {
        if ($this->modelTemplate->exists === false && $this->modelTemplate->isNewFlag === false) {
            $this->commandService->createModel($this->modelTemplate);
            $output->writeln('<info>Creating Model : ' . $this->modelTemplate->namespace . '\\'
                . $this->modelTemplate->name . '</info>');
        } else if ($this->modelTemplate->exists === false && $this->modelTemplate->isNewFlag === true) {
            $this->commandService->createModel($this->modelTemplate);
            $output->writeln('<info>Creating Model : ' . $this->modelTemplate->namespace . '\\New'
                . $this->modelTemplate->name . '</info>');
        } else if ($this->modelTemplate->exists === true && $this->modelTemplate->isNewFlag === true) {
            $output->writeln(
                '<info>Using Existing Model : ' . $this->modelTemplate->namespace . '\\New' . $this->modelTemplate->name
                    . '</info>'
            );
        } else {
            $output->writeln(
                '<info>Using Existing Model : ' . $this->modelTemplate->namespace . '\\' . $this->modelTemplate->name
                    . '</info>'
            );
        }
        $output->writeln('<comment>' . $this->commandService->getRelativeFilePath($this->modelTemplate->filePath)
            . '</comment>');
        $output->writeln('');
        if ($this->isACommandUseCase()) {
            $this->createCommandArch($output);
        } else {
            $this->createQueryArch($output);
        }

        return Command::SUCCESS;
    }

    /**
     * @return bool
     */
    public function isACommandUseCase(): bool
    {
        return in_array($this->useCaseType, self::COMMAND_USECASES, true);
    }

    /**
     * @param OutputInterface $output
     */
    private function createCommandArch(OutputInterface $output): void
    {
        $this->commandService->createRepositoryInterfaceTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
            self::WRITE_REPOSITORY_TYPE
        );
        $this->commandArchCommandService->createFactoryIfNotExist(
            $output,
            $this->modelTemplate
        );
        $this->commandArchCommandService->createWriteRepositoryTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
        );
        $this->commandArchCommandService->createRequestDtoTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->commandArchCommandService->createPresenterInterfaceIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->commandArchCommandService->createPresenterIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->commandArchCommandService->createUseCaseIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType,
        );
        $this->commandArchCommandService->createControllerIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType,
        );
    }

    /**
     * @param OutputInterface $output
     */
    private function createQueryArch(OutputInterface $output): void
    {
        $this->commandService->createRepositoryInterfaceTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
            self::READ_REPOSITORY_TYPE
        );
        $this->queryArchCommandService->createFactoryIfNotExist(
            $output,
            $this->modelTemplate
        );
        $this->queryArchCommandService->createReadRepositoryTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
        );
        $this->queryArchCommandService->createResponseDtoTemplateIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->queryArchCommandService->createPresenterInterfaceIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->queryArchCommandService->createPresenterIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType
        );
        $this->queryArchCommandService->createUseCaseIfNotExist(
            $output,
            $this->modelTemplate,
            $this->useCaseType,
        );
        $this->queryArchCommandService->createControllerIfNotExist(
            $output,
            $this->modelTemplate->name,
            $this->useCaseType,
        );
    }
}
