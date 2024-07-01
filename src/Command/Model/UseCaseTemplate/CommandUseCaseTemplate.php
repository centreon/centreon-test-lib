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

use Centreon\Command\Model\{DtoTemplate\RequestDtoTemplate,
    ExceptionTemplate\ExceptionTemplate,
    FileTemplate,
    PresenterTemplate\PresenterInterfaceTemplate,
    RepositoryTemplate\RepositoryInterfaceTemplate};
use function Symfony\Component\DependencyInjection\Loader\Configurator\param;

class CommandUseCaseTemplate extends FileTemplate implements \Stringable
{

    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param PresenterInterfaceTemplate $presenter
     * @param RequestDtoTemplate $request
     * @param RepositoryInterfaceTemplate $repository
     * @param bool $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $useCaseType,
        public string $modelName,
        public PresenterInterfaceTemplate $presenter,
        public RequestDtoTemplate $request,
        public RepositoryInterfaceTemplate $repository,
        public bool $exists = false,
        public string $name,
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

        $presenterInterfaceName = $this->presenter->name;
        $requestName = $this->request->name;
        $repositoryNamespace = $this->repository->namespace . '\\' . $this->repository->name;
        $repositoryName = $this->repository->name;
        $repositoryVariable = 'repository';
        $presenterVariable = 'presenter';
        $requestVariable = 'request';
        $methodName = ExceptionTemplate::getMethodeName($this->useCaseType);
        $presentResponseVariable = 'presentResponse';

        if ($this->useCaseType === "Delete")
        {
            $phpDoc = " @param int $" . lcfirst($this->modelName) . "Id,\n     *  @param PresenterInterface $$presenterVariable,";
            $settingValue = "int $" . lcfirst($this->modelName) . "Id,\n        PresenterInterface $$presenterVariable,";
            $presenterNameSpace = "use Core\Application\Common\UseCase\PresenterInterface;";
            $presentResponseVariable = "setResponseStatus";
        }else {
            $phpDoc = " @param " . $requestName . ' $' . $requestVariable . ",\n     *  @param " . $presenterInterfaceName . ' $presenter';
            $settingValue = $requestName . ' $' . $requestVariable . ",\n        $presenterInterfaceName $$presenterVariable";
            $presenterNameSpace = "use Core\\{$this->modelName}\Infrastructure\API\\{$this->useCaseType}{$this->modelName}\\{$this->useCaseType}{$this->modelName}Presenter;";
        }
        return <<<EOF
            <?php
            
            {$this->licenceHeader}
            
            declare(strict_types=1);

            namespace {$this->namespace};
            
            $presenterNameSpace
            use {$repositoryNamespace};
            use Core\Application\Common\UseCase\ErrorResponse;
            use Core\\{$this->modelName}\Application\Exception\\{$this->modelName}Exception;
            use Centreon\Domain\Log\LoggerTrait;

            final class {$this->useCaseType}{$this->modelName}
            {
                use LoggerTrait;
                /**
                 * @param {$repositoryName} $$repositoryVariable
                 */
                public function __construct(private {$repositoryName} $$repositoryVariable)
                {
                }

                /**
                 * $phpDoc
                 */
                public function __invoke(
                    $settingValue
                ): void {
                    try {
                    } catch (\\Throwable \$exception) {
                        \$presenter->{$presentResponseVariable}(
                            new ErrorResponse({$this->modelName}Exception::{errorWhile$methodName}())
                        );
                        \$this->error((string) \$exception);    
                    }
                }
            }
            EOF;
    }
}
