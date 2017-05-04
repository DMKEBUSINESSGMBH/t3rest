<?php
if (! defined('TYPO3_MODE'))
    die('Access denied.');


require_once tx_rnbase_util_Extensions::extPath('rn_base', 'class.tx_rnbase.php');
tx_rnbase::load('tx_t3rest_util_ServiceRegistry');

tx_rnbase_util_Extensions::addService(
    't3rest',
    't3rest',
    'tx_t3rest_srv_Logs',
    array(
        'title' => 'T3rest logging service',
        'description' => 'Access to logging data',
        'subtype' => 'logs',
        'available' => TRUE,
        'priority' => 50,
        'quality' => 50,
        'os' => '',
        'exec' => '',
        'classFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY, 'srv/class.tx_t3rest_srv_Logs.php'),
        'className' => 'tx_t3rest_srv_Logs',
    )
);
