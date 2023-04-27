<?php

namespace Centreon\Command\Model\UnitTestTemplate;

use Centreon\Command\Model\FileTemplate;

class UnitTestTemplate extends FileTemplate
{
    public function generateContentForUnitTest(string $fileNamespace)
    {
        $namespace = $fileNamespace;

        $content = <<<EOF
        <?php
        $this->licenceHeader
        declare(strict_types=1);

        namespace $namespace;

        it('should be erased or throw an error', function () {
            expect(false)->toBeTrue();
        });

        EOF;

        return $content;
    }
}
