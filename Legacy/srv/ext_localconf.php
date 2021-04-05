<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

tx_rnbase_util_Extensions::addService(
    't3rest',
    't3rest',
    'tx_t3rest_srv_Logs',
    [
        'title' => 'T3rest logging service',
        'description' => 'Access to logging data',
        'subtype' => 'logs',
        'available' => true,
        'priority' => 50,
        'quality' => 50,
        'os' => '',
        'exec' => '',
        'classFile' => tx_rnbase_util_Extensions::extPath('t3rest', 'Legacy/srv/class.tx_t3rest_srv_Logs.php'),
        'className' => 'tx_t3rest_srv_Logs',
    ]
);
