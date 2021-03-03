<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}

if (!tx_rnbase_util_TYPO3::isTYPO60OrHigher()) {
    $GLOBALS['TCA']['tx_t3rest_providers'] = require tx_rnbase_util_Extensions::extPath(
        't3rest',
        'Configuration/TCA/tx_t3rest_providers.php'
    );
}

if (TYPO3_MODE == 'BE') {
    require_once tx_rnbase_util_Extensions::extPath('t3rest', 'Legacy/mod/ext_tables.php');
}
