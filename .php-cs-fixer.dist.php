<?php

use Centreon\PhpCsFixer\PhpCsFixerHelper;
use PhpCsFixer\Finder;

$finder = Finder::create()
  ->in(__DIR__ . '/src/PHPStan');

return PhpCsFixerHelper::styles($finder);
