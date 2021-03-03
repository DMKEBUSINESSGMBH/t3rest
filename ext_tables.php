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
    // $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mkkvbb_util_Wizicon'] = tx_rnbase_util_Extensions::extPath($_EXTKEY).'util/class.tx_mkkvbb_util_Wizicon.php';
    require_once tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod/ext_tables.php';
}
