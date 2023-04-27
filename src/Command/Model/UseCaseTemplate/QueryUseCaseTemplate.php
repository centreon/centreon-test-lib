<?php

namespace Centreon\Command\Model\UseCaseTemplate;

use Centreon\Command\Model\FileTemplate;
use Centreon\Command\Model\DtoTemplate\ResponseDtoTemplate;
use Centreon\Command\Model\ModelTemplate\ModelTemplate;
use Centreon\Command\Model\PresenterTemplate\PresenterInterfaceTemplate;
use Centreon\Command\Model\RepositoryTemplate\RepositoryInterfaceTemplate;

class QueryUseCaseTemplate extends FileTemplate
{
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public PresenterInterfaceTemplate $presenter,
        public ResponseDtoTemplate $response,
        public RepositoryInterfaceTemplate $repository,
        public bool $exists = false,
        public ModelTemplate $model
    ) {
        parent::__construct();
    }

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

        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use $presenterInterfaceNamespace;
        use $repositoryNamespace;
        use $responseNamespace;
        use $modelNamespace;

        class $this->name
        {
            /**
             * @param $repositoryName $$repositoryVariable
             */
            public function __construct(private $repositoryName $$repositoryVariable)
            {
            }

            /**
             * @param $presenterInterfaceName $$presenterVariable
             */
            public function __invoke(
                $presenterInterfaceName $$presenterVariable
            ): void {
            }

            public function createResponse($modelName $$modelNameVariable): $responseName
            {
                $$responseVariable = new $responseName();

                return $$responseVariable;
            }
        }

        EOF;

        return $content;
    }

    public function __toString()
    {
        return $this->name;
    }

}
