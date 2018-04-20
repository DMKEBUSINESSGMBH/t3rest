<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}
if (TYPO3_MODE == 'BE') {
    // Einbindung einer PageTSConfig
    tx_rnbase_util_Extensions::addPageTSConfig('<INCLUDE_TYPOSCRIPT: source="FILE:EXT:'.$_EXTKEY.'/mod/pageTSconfig.txt">');

    tx_rnbase_util_Extensions::registerModule(
        'Txt3rest',
        'user',
        'M1',
        'bottom',
        array(),
        array(
            'access' => 'user,group',
            'routeTarget' => 'tx_t3rest_mod_Module',
            'icon' => 'EXT:t3rest/mod/moduleicon.gif',
            'labels' => 'LLL:EXT:t3rest/mod/locallang.xml',
        )
    );

    tx_rnbase_util_Extensions::insertModuleFunction(
        'user_Txt3restM1',
        'tx_t3rest_mod_Logs',
        tx_rnbase_util_Extensions::extPath('t3rest', 'mod/class.tx_t3rest_mod_Logs.php'),
        'LLL:EXT:t3rest/mod/locallang.xml:label_t3rest_mod_logs'
    );
}
