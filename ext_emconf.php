<?php

//
// Extension Manager/Repository config file for ext: "t3sportstats"
//
// Auto generated 12-02-2008 16:47
//
// Manual updates:
// Only the data in the array - anything else is removed by next write.
// "version" and "dependencies" must not be touched!
//

$EM_CONF['t3rest'] = [
    'title' => 'REST for TYPO3',
    'description' => 'Provides a REST interface for TYPO3.',
    'category' => 'fe',
    'author' => 'Rene Nitzsche, Michael Wagner, Hannes Bochmann, Mario Seidel',
    'author_email' => 'dev@dmk-ebusiness.de',
    'author_company' => 'DMK E-BUSINESS GmbH',
    'shy' => '',
    'version' => '11.0.1',
    'dependencies' => '',
    'conflicts' => '',
    'priority' => '',
    'module' => '',
    'state' => 'beta',
    'internal' => '',
    'uploadfolder' => 0,
    'createDirs' => '',
    'modify_tables' => '',
    'clearCacheOnLoad' => 1,
    'lockType' => '',
    'constraints' => [
        'depends' => [
            'rn_base' => '1.17.0-',
            'typo3' => '11.5.7-12.4.99',
        ],
        'conflicts' => [
        ],
        'suggests' => [
        ],
    ],
    'autoload' => [
        'classmap' => [
            'Classes/',
        ],
    ],
];
