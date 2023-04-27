<?php

namespace Centreon\Command\Model\ControllerTemplate;

use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\UseCaseTemplate\QueryUseCaseTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterInterfaceTemplate;

class QueryControllerTemplate extends FileTemplate
{
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public QueryUseCaseTemplate $useCase,
        public PresenterInterfaceTemplate $presenter,
        public bool $exists = false
    ) {
    }

    public function generateModelContent(): string
    {
        $useCaseNamespace = $this->useCase->namespace . '\\' . $this->useCase->name;
        $presenterNamespace = $this->presenter->namespace . '\\' . $this->presenter->name;
        $useCaseVariable = 'useCase';
        $presenterVariable = 'presenter';
        $show = 'presenter->show()';

        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use $useCaseNamespace;
        use $presenterNamespace;

        class $this->name
        {
            public function __invoke(
                $this->useCase $$useCaseVariable,
                $this->presenter $$presenterVariable
            ): object {
                $$useCaseVariable($$presenterVariable);

                return $$show;
            }
        }

        EOF;

        return $content;
    }
}
