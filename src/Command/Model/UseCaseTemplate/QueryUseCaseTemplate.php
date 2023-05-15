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

namespace Centreon\Command\Model\UseCaseTemplate;

use Centreon\Command\Model\DtoTemplate\ResponseDtoTemplate;
use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterInterfaceTemplate;
use Centreon\Command\Model\RepositoryTemplate\RepositoryInterfaceTemplate;

class QueryUseCaseTemplate extends FileTemplate implements \Stringable
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param PresenterInterfaceTemplate $presenter
     * @param ResponseDtoTemplate $response
     * @param RepositoryInterfaceTemplate $repository
     * @param bool $exists
     * @param ModelTemplate $model
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public PresenterInterfaceTemplate $presenter,
        public ResponseDtoTemplate $response,
        public RepositoryInterfaceTemplate $repository,
        public ModelTemplate $model,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function __toString(): string
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        $presenterInterfaceNamespace = $this->presenter->namespace . '\\' . $this->presenter->name;
        $presenterInterfaceName = $this->presenter->name;
        $repositoryNamespace = $this->repository->namespace . '\\' . $this->repository->name;
        $repositoryName = $this->repository->name;
        $responseName = $this->response->name;
        $repositoryVariable = 'repository';
        $presenterVariable = 'presenter';
        $responseVariable = 'response';
        $responseNamespace = $this->response->namespace . '\\' . $this->response->name;
        $modelNameVariable = lcfirst($this->model->name);
        $modelNamespace = $this->model->namespace . '\\' . $this->model->name;
        $modelName = $this->model->name;

        return <<<EOF
            <?php
            {$this->licenceHeader}
            declare(strict_types=1);

            namespace {$this->namespace};

            use {$presenterInterfaceNamespace};
            use {$repositoryNamespace};
            use {$responseNamespace};
            use {$modelNamespace};

            class {$this->name}
            {
                /**
                 * @param {$repositoryName} $$repositoryVariable
                 */
                public function __construct(private {$repositoryName} $$repositoryVariable)
                {
                }

                /**
                 * @param {$presenterInterfaceName} $$presenterVariable
                 */
                public function __invoke(
                    {$presenterInterfaceName} $$presenterVariable
                ): void {
                }

                public function createResponse({$modelName} $$modelNameVariable): {$responseName}
                {
                    $$responseVariable = new {$responseName}();

                    return $$responseVariable;
                }
            }

            EOF;
    }
}
