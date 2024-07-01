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

use Centreon\Command\CreateCoreArchCommand;
use Centreon\Command\Model\DtoTemplate\RequestDtoTemplate;
use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterTemplate;
use Centreon\Command\Model\UseCaseTemplate\CommandUseCaseTemplate;

class CommandControllerTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param CommandUseCaseTemplate $useCase
     * @param PresenterTemplate $presenter
     * @param RequestDtoTemplate $request
     * @param bool $exists
     */
    public function __construct(
        public string                 $filePath,
        public string                 $namespace,
        public string                 $name,
        public CommandUseCaseTemplate $useCase,
        public PresenterTemplate      $presenter,
        public RequestDtoTemplate     $request,
        public string                 $useCaseType,
    )
    {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateCreateRequest(): string
    {
        $requestVariable = 'request';
        $requestGetContent = 'request->getContent()';
        $requestDtoVariable = lcfirst($this->request->name);
        $requestDataVariable = 'requestData';
        return <<<REQUEST
    public function create{$this->request}(Request $$requestVariable): {$this->request}
        {
            $$requestDataVariable = json_decode((string) $$requestGetContent, true);
            $$requestDtoVariable = new {$this->request}($$requestDataVariable);

            return $$requestDtoVariable;
        }
    }    
    REQUEST;
    }

    public function generateDeleteMethod(): string
    {
        return <<<CORE
use Core\Infrastructure\Common\Api\DefaultPresenter;
use Core\\{$this->name}\Application\UseCase\\{$this->useCase}\\{$this->useCase};

final class {$this->useCase}Controller
{
    public function __invoke(
        $this->useCase \$useCase,
        DefaultPresenter \$presenter,
        int \${$this->name}Id,
    ): object {
        \$useCase(\${$this->name}Id, \$presenter);
        return \$presenter->show();
    }    
}
CORE;
    }

    public function createAddMethode(): string
    {
        return <<<COREADD

use Symfony\Component\HttpFoundation\Request;
use Core\\{$this->name}\Application\UseCase\\{$this->useCase}\\{$this->useCase};
use Core\\{$this->name}\Application\UseCase\\{$this->useCase}\\{$this->useCase}Request;

final class {$this->useCase}Controller
{
     public function __invoke(
{$this->useCase} \$useCase,
        Request \$request,
        {$this->useCase}Presenter \$presenter
    ): object {
        \${$this->useCase}Request = \$this->create{$this->useCase}Request(\$request);
        \$useCase(\${$this->useCase}Request, \$presenter);
        
        return \$presenter->show(); 
       }
COREADD;
    }

    public function generateModelContent(): string
    {
        if ($this->useCaseType === CreateCoreArchCommand::COMMAND_DELETE){
            $coreController = $this->generateDeleteMethod();
            $createNameRequest ='';
        } else {
            $coreController = $this->createAddMethode();
            $createNameRequest = $this->generateCreateRequest();
        }
        return <<<CORECONTROLLER
            <?php

            {$this->licenceHeader}
            
            declare(strict_types=1);

            namespace {$this->namespace};
                $coreController                          
                $createNameRequest

            CORECONTROLLER;
    }
}