<?php

declare(strict_types=1);
use PhpCsFixer\Finder;

$finder = new Finder()
    ->in([
        './src',
        './tests',
    ]);

$config = require __DIR__.'/vendor/zantolov/toolbelt-dev/.php-cs-fixer.dist.php';

$config
    ->setFinder($finder);

return $config;
