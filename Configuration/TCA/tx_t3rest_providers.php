<?php

return [
    'ctrl' => [
        'title' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers',
        'label' => 'name',
        'tstamp' => 'tstamp',
        'crdate' => 'crdate',
        'cruser_id' => 'cruser_id',
        'delete' => 'deleted',
        'enablecolumns' => [
            'disabled' => 'hidden',
        ],
        'iconfile' => 'EXT:t3rest/icon_table.gif',
    ],
    'interface' => [
        'showRecordFieldList' => 'hidden,starttime,fe_group,name',
    ],
    'feInterface' => [
        'fe_admin_fieldList' => 'hidden, name',
    ],
    'columns' => [
        'hidden' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.hidden',
            'config' => [
                'type' => 'check',
                'default' => '0',
            ],
        ],

        'name' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_name',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ],
        ],
        'restkey' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_restkey',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'trim',
            ],
        ],
        'classname' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_classname',
            'config' => [
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            ],
        ],
        'fe_group' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => [
                'type' => 'select',
                'renderType' => 'selectMultipleSideBySide',
                'size' => 7,
                'maxitems' => 20,
                'items' => [
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
                        -1,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
                        -2,
                    ],
                    [
                        'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
                        '--div--',
                    ],
                ],
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title',
            ],
        ],
        'config' => [
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_config',
            'config' => [
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'eval' => 'trim',
            ],
        ],
    ],
    'types' => [
        '0' => ['showitem' => 'hidden;;1;;1-1-1,name,restkey,classname,fe_group,config'],
    ],
    'palettes' => [
        '1' => ['showitem' => ''],
    ],
];
