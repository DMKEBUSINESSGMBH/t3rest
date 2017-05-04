<?php

return array(
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
        'iconfile' => tx_rnbase_util_Extensions::extRelPath('t3rest') . 'icon_table.gif',
    ),
    'interface' => array(
        'showRecordFieldList' => 'hidden,starttime,fe_group,name'
    ),
    'feInterface' => array(
        'fe_admin_fieldList' => 'hidden, name',
    ),
    'columns' => array(
        'hidden' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xml:LGL.hidden',
            'config' => array(
                'type' => 'check',
                'default' => '0'
            )
        ),

        'name' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_name',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            )
        ),
        'restkey' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_restkey',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            )
        ),
        'classname' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_classname',
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'eval' => 'required,trim',
            )
        ),
        'fe_group' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:lang/locallang_general.xlf:LGL.fe_group',
            'config' => array(
                'type' => 'select',
                'size' => 7,
                'maxitems' => 20,
                'items' => array(
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.hide_at_login',
                        -1
                    ),
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.any_login',
                        -2
                    ),
                    array(
                        'LLL:EXT:lang/locallang_general.xlf:LGL.usergroups',
                        '--div--'
                    )
                ),
                'exclusiveKeys' => '-1,-2',
                'foreign_table' => 'fe_groups',
                'foreign_table_where' => 'ORDER BY fe_groups.title'
            )
        ),
        'config' => array(
            'exclude' => 1,
            'label' => 'LLL:EXT:t3rest/locallang_db.xml:tx_t3rest_providers_config',
            'config' => array(
                'type' => 'text',
                'cols' => '30',
                'rows' => '5',
                'eval' => 'trim',
            )
        ),
    ),
    'types' => array(
        '0' => array('showitem' => 'hidden;;1;;1-1-1,name,restkey,classname,fe_group,config')
    ),
    'palettes' => array(
        '1' => array('showitem' => '')
    )
);
