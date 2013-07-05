<?php
if (!defined ('TYPO3_MODE')) 	die ('Access denied.');


$TCA['tx_t3rest_providers'] = Array (
	'ctrl' => $TCA['tx_t3rest_providers']['ctrl'],
	'interface' => Array (
		'showRecordFieldList' => 'hidden,starttime,fe_group,name'
	),
	'feInterface' => $TCA['tx_t3rest_providers']['feInterface'],
	'columns' => Array (
		'hidden' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
			'config' => Array (
				'type' => 'check',
				'default' => '0'
			)
		),

		'name' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_name',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'restkey' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_restkey',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'classname' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_classname',
			'config' => Array (
				'type' => 'input',
				'size' => '30',
				'eval' => 'required,trim',
			)
		),
		'config' => Array (
			'exclude' => 1,
			'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_config',
			'config' => Array (
				'type' => 'text',
				'cols' => '30',	
				'rows' => '5',
				'eval' => 'trim',
			)
		),
	),
	'types' => Array (
		'0' => Array('showitem' => 'hidden;;1;;1-1-1, name,restkey,classname,config')
	),
	'palettes' => Array (
		'1' => Array('showitem' => '')
	)
);
