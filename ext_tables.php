<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

$TCA['tx_t3rest_providers'] = array(
    'ctrl' => array(
        'title' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => array(
            'disabled' => 'hidden',
        ),
        'dynamicConfigFile' => tx_rnbase_util_Extensions::extPath($_EXTKEY).'configurations/tca/providers.php',
        'iconfile' => tx_rnbase_util_Extensions::extRelPath($_EXTKEY).'icon_table.gif',
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, name',
    )
);

if (TYPO3_MODE == 'BE') {
    //    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mkkvbb_util_Wizicon'] = tx_rnbase_util_Extensions::extPath($_EXTKEY).'util/class.tx_mkkvbb_util_Wizicon.php';
    require_once(tx_rnbase_util_Extensions::extPath($_EXTKEY).'mod/ext_tables.php');
}
