<?php

return [
    'frontend' => [
        'dmk/t3rest/authmapper' => [
            'target' => \DMK\T3rest\Middleware\AuthResolver::class,
            'before' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
        'dmk/t3rest/api' => [
            'target' => \DMK\T3rest\Middleware\RestApiMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                Sys25\RnBase\Utility\TYPO3::isTYPO104OrHigher() ?
                    'typo3/cms-frontend/base-redirect-resolver' :
                    'typo3/cms-frontend/site',
            ],
        ],
    ],
];
