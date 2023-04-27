<?php

namespace Centreon\Command\Model\PresenterTemplate;

use Centreon\Command\Model\FileTemplate;

class PresenterInterfaceTemplate extends FileTemplate
{
    public function __construct(
        public string $filePath,
        public string $namespace,
        public string $name,
        public bool $exists = false
    ) {
        parent::__construct();
    }

    public function generateModelContent(): string
    {
        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $this->namespace;

        use Core\Application\Common\UseCase\PresenterInterface;

        interface $this->name extends PresenterInterface
        {
        }

        EOF;

        return $content;
    }

    public function __toString()
    {
        return $this->name;
    }
}
