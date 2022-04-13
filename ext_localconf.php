<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

/* @deprecated legacy code, will be removed for 10.x or later */
$TYPO3_CONF_VARS['FE']['eID_include']['t3rest'] = 'EXT:t3rest/Legacy/controller/class.tx_t3rest_controller_Base.php';

// Include services
/* @deprecated legacy code, will be removed for 10.x or later */
require_once \Sys25\RnBase\Utility\Extensions::extPath('t3rest', 'Legacy/srv/ext_localconf.php');

/* @deprecated legacy code, will be removed for 10.x or later */
if (!\Sys25\RnBase\Utility\TYPO3::isTYPO90OrHigher()) {
    // was called after db initialisation, direktly after eID
    // and before ob_start compression handler
    $TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['connectToDB']['t3rest'] = 'EXT:t3rest/Classes/Hook/TsFe.php:&Tx_T3rest_Hook_TsFe->checkAndRunRestApi';
}
