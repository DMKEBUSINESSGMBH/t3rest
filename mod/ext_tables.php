<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (TYPO3_MODE == 'BE') {
    // Einbindung einer PageTSConfig
    tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'.$_EXTKEY.'/mod/pageTSconfig.txt">');

    tx_rnbase_util_Extensions::addModule('user', 'txt3restM1', '', tx_rnbase_util_Extensions::extPath($_EXTKEY) . 'mod/');
    // Auswertung der Logs
    tx_rnbase_util_Extensions::insertModuleFunction(
        'user_txt3restM1',
        'tx_t3rest_mod_Logs',
        tx_rnbase_util_Extensions::extPath($_EXTKEY, 'mod/class.tx_t3rest_mod_Logs.php'),
        'LLL:EXT:t3rest/mod/locallang.xml:label_t3rest_mod_logs'
    );
    // TODO: Schaltung von Werbung
}
