<?php

$finder = PhpCsFixer\Finder::create()
    ->in('Classes')
    ->in('Tests')
;

return PhpCsFixer\Config::create()
    ->setFinder($finder)
    ->setRules([
        '@PSR2' => true,
        '@Symfony' => true,
    ])
    ->setLineEnding("\n")
;
