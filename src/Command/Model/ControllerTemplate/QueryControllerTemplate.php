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

namespace Centreon\Command\Model\ControllerTemplate;

use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterInterfaceTemplate;
use Centreon\Command\Model\UseCaseTemplate\QueryUseCaseTemplate;

class QueryControllerTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param QueryUseCaseTemplate $useCase
     * @param PresenterInterfaceTemplate $presenter
     * @param bool $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public QueryUseCaseTemplate $useCase,
        public PresenterInterfaceTemplate $presenter,
        public bool $exists = false,
        public string $useCaseType,
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        $useCaseNamespace = $this->useCase->namespace . '\\' . $this->useCase->name;
        $useCaseVariable = 'useCase';
        $presenterVariable = 'presenter';

        return <<<EOF
            <?php
            {$this->licenceHeader}
            declare(strict_types=1);

            namespace {$this->namespace};

            use {$useCaseNamespace};

            final class {$this->useCaseType}{$this->name}Controller
            {
                public function __invoke(
                    {$this->useCase} $$useCaseVariable,
                    {$this->useCaseType}{$this->name}Presenter $$presenterVariable,
                    int \${$this->name}Id,
                ): object {
                    \$useCase(\${$this->name}Id, \$presenter);

                    return \$presenter->show();
                }
            }

            EOF;
    }
}
