<?php

namespace Centreon\Command\Model\RepositoryTemplate;

use Centreon\Command\Model\FileTemplate;

class RepositoryInterfaceTemplate extends FileTemplate
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

        interface $this->name
        {
        }

        EOF;

        return $content;
    }
}
