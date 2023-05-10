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

use Centreon\Command\Model\DtoTemplate\RequestDtoTemplate;
use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterInterfaceTemplate;
use Centreon\Command\Model\UseCaseTemplate\CommandUseCaseTemplate;

class CommandControllerTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param CommandUseCaseTemplate $useCase
     * @param PresenterInterfaceTemplate $presenter
     * @param RequestDtoTemplate $request
     * @param boolean $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public CommandUseCaseTemplate $useCase,
        public PresenterInterfaceTemplate $presenter,
        public RequestDtoTemplate $request,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        $useCaseNamespace = $this->useCase->namespace . '\\' . $this->useCase->name;
        $presenterNamespace = $this->presenter->namespace . '\\' . $this->presenter->name;
        $requestNamespace = $this->request->namespace . '\\' . $this->request->name;

        $useCaseVariable = 'useCase';
        $requestVariable = 'request';
        $requestGetContent = 'request->getContent()';
        $requestDtoVariable = lcfirst($this->request->name);
        $presenterVariable = 'presenter';
        $show = 'presenter->show()';
        $createDto = 'this->create' . $this->request->name . '($request)';
        $requestDataVariable = 'requestData';

        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use Symfony\Component\HttpFoundation\Request;
        use $useCaseNamespace;
        use $presenterNamespace;
        use $requestNamespace;

        final class $this->name
        {
            public function __invoke(
                $this->useCase $$useCaseVariable,
                Request $$requestVariable,
                $this->presenter $$presenterVariable
            ): object {
                $$requestDtoVariable = $$createDto;
                $$useCaseVariable($$presenterVariable, $$requestDtoVariable);

                return $$show;
            }

            public function create$this->request(Request $$requestVariable): $this->request
            {
                $$requestDataVariable = json_decode((string) $$requestGetContent, true);
                $$requestDtoVariable = new $this->request($$requestDataVariable);

                return $$requestDtoVariable;
            }
        }

        EOF;

        return $content;
    }
}
