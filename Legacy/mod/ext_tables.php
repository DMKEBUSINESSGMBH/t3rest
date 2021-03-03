<?php

if (!defined('TYPO3_MODE')) {
    exit('Access denied.');
}
if (TYPO3_MODE == 'BE') {
    // Einbindung einer PageTSConfig
    tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:t3rest/Legacy/mod/pageTSconfig.txt">');

    tx_rnbase_util_Extensions::registerModule(
        't3rest',
        'user',
        'M1',
        'bottom',
        [],
        [
            'access' => 'user,group',
            'routeTarget' => 'tx_t3rest_mod_Module',
            'icon' => 'EXT:t3rest/Legacy/mod/moduleicon.gif',
            'labels' => 'LLL:EXT:t3rest/Legacy/mod/locallang.xml',
        ]
    );

    tx_rnbase_util_Extensions::insertModuleFunction(
        'user_T3restM1',
        'tx_t3rest_mod_Logs',
        tx_rnbase_util_Extensions::extPath('t3rest', 'Legacy/mod/class.tx_t3rest_mod_Logs.php'),
        'LLL:EXT:t3rest/Legacy/mod/locallang.xml:label_t3rest_mod_logs'
    );
}
