<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

/* @deprecated legacy code, will be removed for 10.x or later */
if (!tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
    $GLOBALS['TCA']['tx_t3rest_providers'] = require tx_rnbase_util_Extensions::extPath(
        't3rest',
        'Configuration/TCA/tx_t3rest_providers.php'
    );
}

/* @deprecated legacy code, will be removed for 10.x or later */
if (TYPO3_MODE == 'BE') {
    require_once tx_rnbase_util_Extensions::extPath('t3rest', 'Legacy/mod/ext_tables.php');
}
