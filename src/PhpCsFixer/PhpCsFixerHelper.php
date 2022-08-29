<?php

declare(strict_types=1);

namespace Centreon\PhpCsFixer;

use PhpCsFixer\Config;
use PhpCsFixer\Finder;

class PhpCsFixerHelper
{
    public static function styles(Finder $finder, array $rules = []): Config
    {
        $rules = array_merge(require __DIR__ . '/ruleset.php', $rules);

        return (new Config())
            ->setFinder($finder)
            ->setRiskyAllowed(true)
            ->setRules($rules);
    }
}
