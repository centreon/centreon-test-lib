<?php

namespace Centreon\Command\Model\PresenterTemplate;

use Centreon\Command\Model\FileTemplate;

class PresenterTemplate extends FileTemplate
{
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public PresenterInterfaceTemplate $presenterInterface,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    public function generateModelContent(): string
    {
        $interfaceNamespace = $this->presenterInterface->namespace . '\\' . $this->presenterInterface->name;
        $interfaceName = $this->presenterInterface->name;
        $dataVariable = '$data';
        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use $interfaceNamespace;
        use Core\Application\Common\UseCase\AbstractPresenter;

        class $this->name extends AbstractPresenter implements $interfaceName
        {
        }

        EOF;

        return $content;
    }
}
