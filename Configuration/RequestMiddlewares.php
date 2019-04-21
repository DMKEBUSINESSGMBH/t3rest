<?php

return [
    'frontend' => [
        't3rest-authmapper' => [
            'target' => \DMK\T3rest\Middleware\AuthResolver::class,
            'before' => [
                'typo3/cms-frontend/authentication',
            ],
        ],
        't3rest-api' => [
            'target' => \DMK\T3rest\Middleware\RestApiMiddleware::class,
            'after' => [
                'typo3/cms-frontend/authentication',
            ],
            'before' => [
                'typo3/cms-frontend/site',
            ],
        ]
    ]
];
