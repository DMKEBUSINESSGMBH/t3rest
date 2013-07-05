<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');

$TCA['tx_t3rest_providers'] = Array (
	'ctrl' => Array (
		'title' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers',
		'label' => 'name',
		'tstamp' => 'tstamp',
		'crdate' => 'crdate',
		'cruser_id' => 'cruser_id',
		'delete' => 'deleted',
		'enablecolumns' => Array (
			'disabled' => 'hidden',
		),
		'dynamicConfigFile' => t3lib_extMgm::extPath($_EXTKEY).'configurations/tca/providers.php',
		'iconfile' => t3lib_extMgm::extRelPath($_EXTKEY).'icon_table.gif',
	),
	'feInterface' => Array (
		'fe_admin_fieldList' => 'hidden, name',
	)
);

if (TYPO3_MODE == 'BE') {
	//	$TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses']['tx_mkkvbb_util_Wizicon'] = t3lib_extMgm::extPath($_EXTKEY).'util/class.tx_mkkvbb_util_Wizicon.php';
	require_once(t3lib_extMgm::extPath($_EXTKEY).'mod/ext_tables.php');

}
