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

namespace Centreon\Command\Model\PresenterTemplate;

use Centreon\Command\Model\FileTemplate;

class PresenterTemplate extends FileTemplate
{
    /**
     * @param string $filePath
     * @param string $namespace
     * @param string $name
     * @param string useCase
     * @param PresenterInterfaceTemplate $presenterInterface
     * @param bool $exists
     */
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public string $useCase,
        public PresenterInterfaceTemplate $presenterInterface,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    /**
     * @return string
     */
    public function generateModelContent(): string
    {
        $interfaceNamespace = $this->presenterInterface->namespace . '\\' . $this->presenterInterface->name;
        $interfaceName = $this->presenterInterface->name . "Interface";
        $method = '';

        if($this->useCase === "Add") {
            $namespaceResponse = "use Core\\{$this->name}\Application\UseCase\\{$this->useCase}{$this->name}\\{$this->useCase}{$this->name}Response;";
            $method = <<<METHODADD
            
                /**
                * @@inheritDoc
                */
                public function presentResponse({$this->useCase}{$this->name}Response|ResponseStatusInterface \$response): void
                {
                    if (\$response instanceof ResponseStatusInterface) {
                        \$this->setResponseStatus(\$response);
                    } else {
                    }
                }          
            METHODADD;
        } elseif ($this->useCase === "Find") {
            $namespaceResponse = "use Core\\{$this->name}\Application\UseCase\\{$this->useCase}{$this->name}\\{$this->useCase}{$this->name}Response;";
            $method = <<<METHODFIND
            
                /**
                * @@inheritDoc
                */
                public function presentResponse(Find{$this->name}Response|ResponseStatusInterface \$response): void
                {
                    if (\$response instanceof ResponseStatusInterface) {
                        \$this->setResponseStatus(\$response);
                    } else {
                        \$this->present([]);
                    }
                }
            METHODFIND;
        } elseif ($this->useCase === "Update") {
            $method = <<<METHODUPDATE
            
                /**
                * @inheritDoc
                */
                public function presentResponse(ResponseStatusInterface \$response): void
                {
                    \$this->setResponseStatus(\$response);
                }
            METHODUPDATE;
        }

        return <<<EOF
            <?php
            {$this->licenceHeader}
            declare(strict_types=1);

            namespace {$this->namespace};
            
            use Core\Application\Common\UseCase\ResponseStatusInterface;
            use Core\Application\Common\UseCase\AbstractPresenter;
            use Core\\{$this->name}\Application\UseCase\\{$this->useCase}{$this->name}\\{$this->useCase}{$this->name}PresenterInterface;
            {$namespaceResponse}

            class {$this->useCase}{$this->name}Presenter extends AbstractPresenter implements {$this->useCase}{$this->name}PresenterInterface
            {
                {$method}
            }

            EOF;
    }
}
